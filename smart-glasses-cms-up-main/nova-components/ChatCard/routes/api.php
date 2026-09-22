<?php

use App\Events\MessageCreated;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::post('/message', function (Request $request) {
    $validated = $request->validate([
        'meeting_id' => 'required|integer|exists:meetings,id',
        'content' => 'required|string|max:5000',
    ]);

    $userId = $request->user()?->id;
    if (! $userId) {
        return response()->json([
            'status' => 'error',
            'error' => 1,
            'message' => '로그인이 필요합니다.',
        ], 401);
    }

    try {
        $message = Message::query()->create([
            'meeting_id' => $validated['meeting_id'],
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);
        $message->load('user:id,name');

        try {
            broadcast(new MessageCreated($message));
        } catch (\Throwable $e) {
            Log::warning('메시지 브로드캐스트 실패', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => $message,
        ]);
    } catch (\Throwable $e) {
        Log::error('메시지 저장 실패', [
            'meeting_id' => $validated['meeting_id'],
            'user_id' => $userId,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'status' => 'error',
            'error' => 1,
            'message' => '메시지 저장에 실패했습니다.',
        ], 500);
    }
});

Route::get('/messages/{meeting_id}', function (Request $request, $meeting_id) {
    $messages = Message::query()
        ->with('user:id,name')
        ->where('meeting_id', $meeting_id)
        ->oldest()
        ->get();

    return response()->json([
        'status' => 'OK',
        'error' => 0,
        'data' => $messages,
    ]);
});
