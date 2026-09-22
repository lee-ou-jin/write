<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MeetingUser;
use Illuminate\Support\Facades\DB;

class RecordingController extends Controller
{
    private $appId;
    private $customerId;
    private $customerSecret;
    private $baseUrl = 'https://api.sd-rtn.com/v1/apps';

    public function __construct()
    {
        $this->appId = config('services.agora.app_id');
        $this->customerId = config('services.agora.customer_id');
        $this->customerSecret = config('services.agora.customer_secret');
    }

    public function startRecording(Request $request)
    {
        try {
            $channelName = $request->input('channel_name');
            $uid = (string)$request->input('uid');

            Log::info('녹화 시작 요청', [
                'channel_name' => $channelName,
                'uid' => $uid,
                'app_id' => $this->appId,
                'customer_id' => $this->customerId,
                'customer_secret' => $this->customerSecret ? '설정됨' : '설정되지 않음'
            ]);

            if (!$uid) {
                throw new \Exception('uid가 필요합니다.');
            }

            // 1. 리소스 ID 획득
            $acquireResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->customerId . ':' . $this->customerSecret),
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$this->appId}/cloud_recording/acquire", [
                'cname' => $channelName,
                'uid' => $uid,
                'clientRequest' => new \stdClass()
            ]);

            if (!$acquireResponse->successful()) {
                throw new \Exception('리소스 ID 획득 실패: ' . $acquireResponse->body());
            } else {
                Log::info('리소스 ID 획득 성공', ['response' => $acquireResponse->json()]);
            }

            $resourceId = $acquireResponse->json()['resourceId'];

            // 2. 녹화 시작
            $recordingConfig = [
                'maxIdleTime' => 30,
                'streamTypes' => 2,
                'channelType' => 1,
                'transcodingConfig' => [
                    'height' => 720,
                    'width' => 1280,
                    'bitrate' => 2000,
                    'fps' => 30,
                ],
                'subscribeVideoUids' => ["1"],
                'subscribeAudioUids' => ["1"],
                'subscribeUidGroup' => 0,
            ];

            // AWS S3 설정
            $storageConfig = [
                'vendor' => 1, // 1: AWS S3
                'region' => 0, // 0: US East
                'bucket' => config('services.aws.bucket'),
                'accessKey' => config('services.aws.key'),
                'secretKey' => config('services.aws.secret'),
                'fileNamePrefix' => ["recordings", $channelName],
            ];

            $startResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->customerId . ':' . $this->customerSecret),
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$this->appId}/cloud_recording/resourceid/{$resourceId}/mode/mix/start", [
                'cname' => $channelName,
                'uid' => $uid,
                'clientRequest' => [
                    'token' => $request->input('token'),
                    'recordingConfig' => $recordingConfig,
                    'recordingFileConfig' => [
                        'avFileType' => ['hls', 'mp4'],
                    ],
                    'storageConfig' => $storageConfig,
                ]
            ]);

            if (!$startResponse->successful()) {
                throw new \Exception('녹화 시작 실패: ' . $startResponse->body());
            }

            Log::info('녹화 시작 응답', ['response' => $startResponse->json()]);

            return response()->json([
                'success' => true,
                'resourceId' => $resourceId,
                'sid' => $startResponse->json()['sid'],
            ]);
        } catch (\Exception $e) {
            Log::error('녹화 시작 중 오류', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => '녹화 시작 중 오류 발생: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function stopRecording(Request $request)
    {
        try {
            $channelName = $request->input('channelName');
            $uid = $request->input('uid');
            $resourceId = $request->input('resourceId');
            $sid = $request->input('sid');

            Log::info('녹화 중지 요청 시작', [
                'channelName' => $channelName,
                'uid' => $uid,
                'resourceId' => $resourceId,
                'sid' => $sid
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->customerId . ':' . $this->customerSecret),
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$this->appId}/cloud_recording/resourceid/{$resourceId}/sid/{$sid}/mode/mix/stop", [
                'cname' => $channelName,
                'uid' => $uid,
                'clientRequest' => new \stdClass(),
            ]);

            if ($response->successful()) {
                Log::info('녹화 중지 성공', ['response' => $response->json()]);

                // 녹화 파일 정보 조회
                Log::info('녹화 파일 정보 조회 시작');
                $queryResponse = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->customerId . ':' . $this->customerSecret),
                    'Content-Type' => 'application/json',
                ])->get("{$this->baseUrl}/{$this->appId}/cloud_recording/resourceid/{$resourceId}/sid/{$sid}/mode/mix/query");

                if ($queryResponse->successful()) {
                    $recordingInfo = $queryResponse->json();
                    Log::info('녹화 파일 정보 조회 성공', ['recordingInfo' => $recordingInfo]);

                    // MP4 파일 URL 찾기
                    $mp4File = null;
                    if (isset($recordingInfo['fileListMode']) && $recordingInfo['fileListMode'] === 'json') {
                        foreach ($recordingInfo['fileList'] as $file) {
                            if (strpos($file['fileName'], '.mp4') !== false) {
                                $mp4File = $file;
                                break;
                            }
                        }
                    }

                    if ($mp4File) {
                        Log::info('MP4 파일 찾음', ['mp4File' => $mp4File]);

                        // 임시 파일로 다운로드
                        $tempPath = storage_path('app/temp/' . uniqid() . '.mp4');
                        if (!file_exists(storage_path('app/temp'))) {
                            mkdir(storage_path('app/temp'), 0777, true);
                            Log::info('임시 디렉토리 생성', ['path' => storage_path('app/temp')]);
                        }

                        Log::info('Agora에서 파일 다운로드 시작', ['url' => $mp4File['fileName']]);
                        // Agora에서 파일 다운로드
                        $downloadResponse = Http::get($mp4File['fileName']);
                        if ($downloadResponse->successful()) {
                            file_put_contents($tempPath, $downloadResponse->body());
                            Log::info('파일 다운로드 완료', ['tempPath' => $tempPath]);

                            // Vimeo에 업로드
                            Log::info('Vimeo 업로드 시작');
                            $vimeoRequest = new \Illuminate\Http\Request();
                            $vimeoRequest->merge([
                                'file' => new \Illuminate\Http\UploadedFile($tempPath, 'recording.mp4', 'video/mp4', null, true),
                                'title' => 'Meeting Recording - ' . date('Y-m-d H:i:s'),
                                'smart_glasses_id' => $request->input('smart_glasses_id'),
                                'task_code' => $request->input('task_code'),
                                'equip_name' => $request->input('equip_name'),
                                'mode' => 'online',
                                'privacy_view' => 'anybody',
                                'controller' => true,
                                'include_title_name' => true,
                                'include_title_owner' => true,
                                'include_title_profile' => true,
                                'end_screen_type' => 'loop',
                                'license' => 'by',
                            ]);

                            $vimeoController = new \App\Http\ApiControllers\VimeoController();
                            $vimeoResponse = $vimeoController->upload($vimeoRequest);
                            Log::info('Vimeo 업로드 완료', ['response' => $vimeoResponse->getData()]);

                            // 임시 파일 삭제
                            unlink($tempPath);
                            Log::info('임시 파일 삭제 완료', ['tempPath' => $tempPath]);

                            return response()->json([
                                'success' => true,
                                'recordingInfo' => $recordingInfo,
                                'vimeoUpload' => $vimeoResponse->getData()
                            ]);
                        } else {
                            Log::error('Agora 파일 다운로드 실패', [
                                'status' => $downloadResponse->status(),
                                'body' => $downloadResponse->body()
                            ]);
                        }
                    } else {
                        Log::warning('MP4 파일을 찾을 수 없음', ['recordingInfo' => $recordingInfo]);
                    }
                } else {
                    Log::error('녹화 파일 정보 조회 실패', [
                        'status' => $queryResponse->status(),
                        'body' => $queryResponse->body()
                    ]);
                }
            } else {
                Log::error('녹화 중지 실패', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => '녹화 중지 실패',
            ], 500);
        } catch (\Exception $e) {
            Log::error('녹화 중지 중 예외 발생', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => '녹화 중지 중 오류 발생',
            ], 500);
        }
    }
}
