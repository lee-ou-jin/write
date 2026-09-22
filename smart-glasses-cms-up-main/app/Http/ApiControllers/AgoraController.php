<?php

namespace App\Http\ApiControllers;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Jerry\LiveCard\RtcTokenBuilder2;

class AgoraController extends Controller
{
    public function generateToken($userId, $meetingId): JsonResponse
    {
        $appId = config('services.agora.app_id');
        $appCertificate = config('services.agora.app_certificate');

        if (empty($appId) || empty($appCertificate)) {
            return response()->json([
                'status' => 'error',
                'error' => 1,
                'message' => 'AGORA_APP_ID and AGORA_APP_CERTIFICATE are required.',
            ], 400);
        }

        $meeting = Meeting::query()->findOrFail($meetingId);

        $uId = $this->getOrCreateAgoraUid($meeting, (int) $userId);

        if (empty($meeting->channel_name)) {
            $meeting->channel_name = 'meeting_' . $meeting->id;
            $meeting->save();
        }

        $channelName = $meeting->channel_name;

        $expireTimestamp = now()->timestamp + 3600;

        $token = RtcTokenBuilder2::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            $uId,
            RtcTokenBuilder2::ROLE_PUBLISHER,
            $expireTimestamp,
            $expireTimestamp
        );

        return response()->json([
            'status' => 'OK',
            'error' => 0,
            'data' => [
                'app_id' => $appId,
                'token' => $token,
                'channel_name' => $channelName,
                'agora_uid' => $uId,
                'expired_at' => $expireTimestamp,
            ],
        ]);
    }

    private function getOrCreateAgoraUid(Meeting $meeting, int $userId): int
    {
        if ($userId !== 0) {
            $meetingUser = $meeting->users()
                ->where('user_id', $userId)
                ->first();

            if ($meetingUser && !empty($meetingUser->pivot->agora_uid)) {
                return (int) $meetingUser->pivot->agora_uid;
            }

            $uId = $this->generateUniqueAgoraUid();

            if ($meetingUser) {
                $meeting->users()->updateExistingPivot($userId, [
                    'agora_uid' => $uId,
                ]);
            } else {
                $meeting->users()->attach($userId, [
                    'agora_uid' => $uId,
                ]);
            }

            return $uId;
        }

        return $this->generateUniqueAgoraUid();
    }

    private function generateUniqueAgoraUid(): int
    {
        do {
            $uId = random_int(1, 2147483647);
        } while (
            DB::table('meeting_users')
            ->where('agora_uid', $uId)
            ->exists()
        );

        return $uId;
    }
}
