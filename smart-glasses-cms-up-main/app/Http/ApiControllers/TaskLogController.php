<?php

namespace App\Http\ApiControllers;

use App\Models\Task;
use App\Models\Meeting;
use App\Models\TaskLog;
use App\Models\TaskLogSend;
use Illuminate\Http\Request;
use App\Events\TaskImageCaptured;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TaskLogController extends Controller
{
    private function resolveTask(string $taskIdentifier): Task
    {
        $task = Task::query()
            ->where('code', $taskIdentifier)
            ->first();

        if ($task) {
            return $task;
        }

        if (is_numeric($taskIdentifier)) {
            $meeting = Meeting::query()->find($taskIdentifier);
            if ($meeting) {
                return Task::query()->findOrFail($meeting->task_id);
            }
        }

        abort(404);
    }

    private function resolveTaskId(string $identifier): int
    {
        return $this->resolveTask($identifier)->id;
    }

    public function getLatestImage($taskCode)
    {
        try {
            $task = $this->resolveTask((string) $taskCode);
            $latestImage = TaskLog::where('task_id', $task->id)
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->latest()
                ->first();

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $latestImage
            ]);
        } catch (\Exception $e) {
            Log::error('최신 이미지 조회 실패', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch latest image'], 500);
        }
    }


    public function storeImage(Request $request, $taskCode)
    {
        Log::info('이미지 저장 시작', ['taskCode' => $taskCode]);

        // 1. Base64 데이터 수신
        $base64Image = $request->input('image'); // 요청에서 base64 데이터 가져오기
        Log::info('Base64 데이터 수신 길이', ['length' => strlen($base64Image)]);

        // 2. Base64 디코딩 및 파일 확장자 확인
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1]; // 확장자 추출 (png, jpg 등)
            Log::info('Data URL 형식의 이미지 확인됨', ['extension' => $extension]);
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        } else {
            // 일반 base64 문자열로 가정하고 처리
            Log::info('일반 Base64 문자열로 처리');
            $extension = 'jpg'; // 기본 확장자 설정
        }

        // Base64 디코딩
        $decodedImage = base64_decode($base64Image);
        Log::info('Base64 디코딩 완료', ['decoded_length' => strlen($decodedImage)]);

        if (!$decodedImage) {
            Log::error('Base64 디코딩 실패');
            return response()->json(['error' => 'Base64 decoding failed'], 400);
        }

        // 4. 이미지 저장
        $fileName = 'images/' . uniqid() . '.' . $extension;
        Log::info('파일 저장 시도', ['fileName' => $fileName]);

        try {
            $stored = Storage::disk('public')->put($fileName, $decodedImage);
            Log::info('파일 저장 결과', ['success' => $stored]);
        } catch (\Exception $e) {
            Log::error('파일 저장 실패', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'File storage failed'], 500);
        }

        // 5. DB에 저장
        try {
            $task = $this->resolveTask((string) $taskCode);
            $taskLog = TaskLog::query()->create([
                'task_id' => $task->getKey(),
                'image' => $fileName,
            ]);
            Log::info('DB 저장 완료', ['taskLog_id' => $taskLog->id]);

            // 이벤트 발생
            $imageUrl = asset('storage/' . $fileName);
            event(new TaskImageCaptured($taskCode, $imageUrl));
        } catch (\Exception $e) {
            Log::error('DB 저장 실패', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Database storage failed'], 500);
        }

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $taskLog
        ]);
    }

    public function getTaskImages($taskCode)
    {
        try {
            $task = $this->resolveTask((string) $taskCode);

            $taskLogs = TaskLog::query()
                ->where('task_id', $task->id)
                ->whereNotNull('image')
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $taskLogs,
            ]);
        } catch (\Exception $e) {
            Log::error('Task 이미지 전체 조회 실패', [
                'taskCode' => $taskCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => 'Task 이미지 전체 조회 실패',
            ], 500);
        }
    }

    public function getSendedImages($taskCode)
    {
        $task = $this->resolveTask((string) $taskCode);

        $sendedImages = TaskLogSend::query()
            ->where('task_id', $task->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $sendedImages
        ]);
    }

    public function checkImageShown($taskId)
    {
        try {
            $resolvedTaskId = $this->resolveTaskId((string) $taskId);
            $taskLog = TaskLog::where('task_id', $resolvedTaskId)
                ->where('is_shown', false)
                ->latest()
                ->first();

            return response()->json([
                'success' => true,
                'isShown' => $taskLog ? false : true,
                'imageUrl' => $taskLog ? $taskLog->image : null
            ]);
        } catch (\Exception $e) {
            Log::error('이미지 열림 여부 확인 중 오류 발생: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '이미지 열림 여부 확인 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    public function markImageAsShown($taskId)
    {
        try {
            $resolvedTaskId = $this->resolveTaskId((string) $taskId);
            $taskLog = TaskLog::where('task_id', $resolvedTaskId)
                ->latest()
                ->first();

            if ($taskLog) {
                $taskLog->update(['is_shown' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => '이미지 열림 상태가 업데이트되었습니다.'
            ]);
        } catch (\Exception $e) {
            Log::error('이미지 열림 상태 업데이트 중 오류 발생: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '이미지 열림 상태 업데이트 중 오류가 발생했습니다.'
            ], 500);
        }
    }
}
