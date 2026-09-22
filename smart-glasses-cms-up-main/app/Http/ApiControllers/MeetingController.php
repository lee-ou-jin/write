<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
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

            $task = Task::query()->find($taskIdentifier);

            if ($task) {
                return $task;
            }
        }

        abort(404);
    }

    public function endMeeting($meetingId)
    {
        try {
            $meeting = Meeting::findOrFail($meetingId);
            $meeting->update([
                'end_time' => now(),
            ]);

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $meeting
            ]);
        } catch (\Exception $e) {
            Log::error('미팅 종료 처리 실패', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to end meeting'], 500);
        }
    }

    public function completeTask($taskIdentifier)
    {
        Log::info('완료 처리할 태스크 식별자: ' . $taskIdentifier);
        try {
            $task = $this->resolveTask((string) $taskIdentifier);

            if ($task->status == 'progress') {
                $task->update([
                    'status' => 'complete',
                    'completed_at' => now(),
                ]);
            }

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $task
            ]);
        } catch (\Exception $e) {
            Log::error('태스크 완료 처리 실패', [
                'task_identifier' => $taskIdentifier,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to complete task'], 500);
        }
    }

    public function saveRecordingPath(Request $request, $meetingId)
    {
        try {
            $meeting = Meeting::findOrFail($meetingId);
            $meeting->recordings_path = $request->input('recordings_path');
            $meeting->save();

            return response()->json([
                'success' => true,
                'message' => '녹화 파일 경로가 저장되었습니다.',
            ]);
        } catch (\Exception $e) {
            Log::error('Save recording path error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '녹화 파일 경로 저장 중 오류 발생',
            ], 500);
        }
    }
}
