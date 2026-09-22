<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskLogSend;
use App\Events\TaskLogSendCaptured;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaskLogSendController extends Controller
{
    private function resolveTask(string $taskCode): ?Task
    {
        return Task::query()
            ->where('code', $taskCode)
            ->first();
    }

    public function store(Request $request, $taskCode)
    {
        try {
            Log::info('IMAGE CHECK', [
                'has_file' => $request->hasFile('image'),
                'file' => $request->file('image'),
                'input' => $request->input('image'),
            ]);

            $task = $this->resolveTask((string) $taskCode);
            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'error' => 1,
                    'message' => '유효한 task를 찾을 수 없습니다.',
                ], 404);
            }

            $decodedImage = null;
            $extension = 'png';

            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                if (!$imageFile || !$imageFile->isValid()) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 1,
                        'message' => '유효하지 않은 이미지 파일입니다.',
                    ], 400);
                }

                $extension = $imageFile->getClientOriginalExtension() ?: $imageFile->extension() ?: 'png';
                $decodedImage = file_get_contents($imageFile->getRealPath());
            } else {
                $base64Image = $request->input('image');
                if (!$base64Image || !is_string($base64Image)) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 1,
                        'message' => 'image 파라미터가 필요합니다.',
                    ], 400);
                }

                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                    $extension = $matches[1];
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                }

                $decodedImage = base64_decode($base64Image, true);
                if ($decodedImage === false) {
                    return response()->json([
                        'status' => 'error',
                        'error' => 1,
                        'message' => '유효하지 않은 Base64 이미지입니다.',
                    ], 400);
                }
            }

            $fileName = 'task-log-send/' . uniqid() . '.' . $extension;
            Storage::disk('public')->put($fileName, $decodedImage);

            $taskLogSend = TaskLogSend::create([
                'task_id' => $task->id,
                'image' => $fileName,
            ]);

            $imageUrl = asset('storage/' . $fileName);
            event(new TaskLogSendCaptured($taskCode, $imageUrl, $taskLogSend->id));

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $taskLogSend,
            ]);
        } catch (\Exception $e) {
            Log::error('TaskLogSend 저장 실패', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to save task log send'], 500);
        }
    }

    public function getLatest($taskCode)
    {
        $task = $this->resolveTask((string) $taskCode);

        if (!$task) {
            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => null,
            ]);
        }

        $latest = TaskLogSend::query()
            ->where('task_id', $task->id)
            ->latest()
            ->first();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $latest,
        ]);
    }

    public function getSendedImages($taskCode)
    {
        $task = $this->resolveTask((string) $taskCode);

        if (!$task) {
            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => [],
            ]);
        }

        $images = TaskLogSend::query()
            ->where('task_id', $task->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $images,
        ]);
    }
}
