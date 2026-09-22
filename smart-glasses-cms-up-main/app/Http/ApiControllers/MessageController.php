<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function getByMeetingId(int $meeting_id): JsonResponse
    {
        $messages = Message::query()
            ->with('user:id,name')
            ->where('meeting_id', $meeting_id)
            ->oldest()
            ->get()
            ->map(function ($message) {
                $message = $message->toArray();
                $message['user_name'] = $message['user']['name'] ?? '-';
                unset($message['user']);

                return $message;
            });

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $messages
        ]);
    }

    public function getByTaskCode(string $code): JsonResponse
    {
        try {
            $task = Task::query()
                ->where('code', $code)
                ->first();

            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'error' => 1,
                    'message' => '요청하신 Task code를 찾을 수 없습니다.'
                ], 404);
            }

            if (!$task->meeting) {
                return response()->json([
                    'status' => 'error',
                    'error' => 1,
                    'message' => '해당 Task에 연결된 미팅이 없습니다.'
                ], 404);
            }

            $messages = $task->meeting->messages()->with('user:id,name')->get();

            $messages = $messages->map(function ($message) {
                $message = $message->toArray();
                $message['user_name'] = $message['user']['name'] ?? null;
                unset($message['user']);
                return $message;
            });

            return response()->json([
                'status' => 'OK',
                'error' => 0,
                'data' => $messages
            ]);
        } catch (\Throwable $e) {
            Log::error('메시지 조회 실패(getByTaskCode)', [
                'code' => $code,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => '메시지 조회 중 서버 오류가 발생했습니다.'
            ], 500);
        }
    }
}
