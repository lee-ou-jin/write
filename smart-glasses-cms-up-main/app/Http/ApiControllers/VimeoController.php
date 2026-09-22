<?php

namespace App\Http\ApiControllers;

use App\Models\Task;
use Illuminate\Support\Arr;
use App\Models\SmartGlasses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use AmuzPackages\VimeoField\Models\VimeoVideo;
use AmuzPackages\VimeoField\Services\VimeoClient;
use Illuminate\Support\Facades\Http;

class VimeoController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $validated = $this->validateUploadRequest($request);
        if (isset($validated['error'])) {
            return $validated['error'];
        }

        try {
            $vimeoVideo = $this->uploadVideoToVimeo($request);
            $task = $this->createAssociatedTask($request, $vimeoVideo);

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => [
                    'video_id' => optional($vimeoVideo)->id,
                    'embed_url' => optional($vimeoVideo)->embed_url,
                    'title' => optional($vimeoVideo)->title,
                    'task' => $task
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Vimeo upload failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['file']),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => '비디오 업로드 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    private function validateUploadRequest(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'smart_glasses_id' => 'required|string',
            'task_code' => 'required|string',
            'file' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/x-msvideo,video/quicktime,video/x-ms-wmv',
                'max:512000'
            ],
            'title' => 'string|max:255',
            'mode' => 'string',
            'equip_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'error' => response()->json([
                    'status' => 'error',
                    'error' => 1,
                    'message' => $validator->errors()->first()
                ], 400)
            ];
        }

        return ['success' => true];
    }

    private function getVimeoOptions(Request $request, string $title): array
    {
        // $options = [
        //     'name' => $title,
        // ];

        // $folderUri = config('vimeo-field.root_path');
        // if (!empty($folderUri)) {
        //     $options['folder_uri'] = $folderUri;
        // }


        // return $options;
        return [
            'name' => $title,
        ];
    }

    private function uploadVideoToVimeo(Request $request): VimeoVideo
    {
        $file = $request->file('file');
        $title = $request->input('title') ?? $request->input('task_code') . ': ' . $request->input('equip_name');
        $options = $this->getVimeoOptions($request, $title);

        $vimeoClient = new VimeoClient();

        $vimeo = $vimeoClient->upload($file, $options);

        $this->addVideoToVimeoFolder(Arr::get($vimeo, 'video_id'));

        return $this->createVimeoVideo($request, $vimeo, $title);
    }

    private function addVideoToVimeoFolder(?string $videoId): void
    {
        if (empty($videoId)) {
            Log::warning('gani Vimeo folder add skipped: empty video_id');
            return;
        }

        $profileId = config('services.vimeo.profile_id');
        $folderId = config('services.vimeo.folder_id');

        $url = "https://api.vimeo.com/users/{$profileId}/folders/{$folderId}/videos/{$videoId}";

        $response = Http::withToken(config('services.vimeo.access'))
            ->put($url);

        Log::info('Vimeo add video to folder', [
            'url' => $url,
            'video_id' => $videoId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    private function createVimeoVideo(Request $request, array $vimeo, string $title): VimeoVideo
    {
        $vimeoVideo = new VimeoVideo();
        $vimeoVideo->setAttribute('id', Arr::get($vimeo, 'video_id'));
        $vimeoVideo->setAttribute('embed_url', Arr::get($vimeo, 'embed_url'));
        $vimeoVideo->setAttribute('title', $title);
        $vimeoVideo->setAttribute('player_color', $request->input('player_color'));
        $vimeoVideo->setAttribute('controller', $request->input('controller'));
        $vimeoVideo->setAttribute('privacy_view', $request->input('privacy_view'));
        $vimeoVideo->setAttribute('license', $request->input('license'));
        $vimeoVideo->setAttribute('include_title_name', $request->input('include_title_name'));
        $vimeoVideo->setAttribute('include_title_owner', $request->input('include_title_owner'));
        $vimeoVideo->setAttribute('include_title_profile', $request->input('include_title_profile'));
        $vimeoVideo->setAttribute('end_screen_type', $request->input('end_screen_type'));

        $smartGlassesId = $request->input('smart_glasses_id');
        $smartGlasses = SmartGlasses::query()->findOrFail($smartGlassesId);
        $vimeoVideo->setAttribute('uploader_type', get_class($smartGlasses));
        $vimeoVideo->setAttribute('uploader_id', $smartGlasses->getKey());

        $vimeoVideo->save();

        return $vimeoVideo;
    }

    private function createAssociatedTask(Request $request, ?VimeoVideo $vimeoVideo): Task
    {
        return Task::query()->create([
            'code' => $request->input('task_code'),
            'title' => $request->input('title') ?? $request->input('task_code'),
            'description' => $request->input('title'),
            'smart_glasses_id' => $request->input('smart_glasses_id'),
            'vimeo_video_id' => optional($vimeoVideo)->id,
            'mode' => $request->input('mode') ?? 'offline',
            'status' => 'progress',
            'equip_name' => $request->input('equip_name'),
        ]);
    }

    public function index(): JsonResponse
    {
        $videos = VimeoVideo::query()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $videos
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $video = VimeoVideo::query()
            ->findOrFail($id);

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $video
        ]);
    }
}
