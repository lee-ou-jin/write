<?php

namespace App\Http\ApiControllers;

use App\Models\Task;
use App\Models\TaskLog;
use App\Models\Equipment;
use App\Models\TaskLogSend;
use App\Models\SmartGlasses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function history(): JsonResponse
    {
        $tasks = Task::query()
            ->where('status', 'complete')
            ->where('mode', 'online')
            ->with('equipment')
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $tasks
        ]);
    }

    // get task logs
    public function getTaskLogs(string $code): JsonResponse
    {
        $task = Task::query()
            ->where('code', $code)
            ->first();

        $taskLogs = TaskLog::query()
            ->where('task_id', $task->id)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $taskLogs
        ]);
    }

    public function historyDetail(string $code): JsonResponse
    {
        $task = Task::query()
            ->where('code', $code)
            ->first();

        $taskLogsSend = TaskLogSend::query()
            ->where('task_id', $task->id)
            ->get();

        $taskLogs = TaskLog::query()
            ->where('task_id', $task->id)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $task,
            'taskLogsSend' => $taskLogsSend,
            'taskLogs' => $taskLogs
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $input = $request->get('input');
        $tasks = Task::query()
            ->where('code', 'like', '%' . $input . '%')
            ->get(['id', 'code', 'equip_name', 'completed_at']);

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $tasks
        ]);
    }

    public function getNewMeeting(string $glassesId): JsonResponse
    {
        try {
            /** @var SmartGlasses $glasses */
            $glasses = SmartGlasses::query()
                ->where('id', $glassesId)
                ->firstOrFail();

            /** @var Task $task */
            $task = $glasses->tasks->where('status', 'progress')
                ->sortByDesc('created_at')
                ->whereNull('completed_at')
                ->firstOrFail();

            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'error' => 1,
                    'message' => '요청하신 데이터를 찾을 수 없습니다.'
                ], 404);
            }

            /** @var Meeting $meeting */
            $meeting = $task->meeting()->where('end_time', null)->firstOrFail();


            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => [
                    'task' => $task,
                    'meeting' => $meeting
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => '요청하신 데이터를 찾을 수 없습니다.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => '서버 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }
}
