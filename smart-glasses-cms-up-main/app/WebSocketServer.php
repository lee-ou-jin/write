<?php

namespace App;

use App\Models\Message;
use App\Models\User;
use App\Models\Meeting;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\Auth;

class WebSocketServer implements MessageComponentInterface
{
    protected $chatRooms;

    public function __construct()
    {
        $this->chatRooms = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {

        $queryString = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryString, $queryParams);

        // JWT 토큰 추출
        if (isset($queryParams['token'])) {
            $token = $queryParams['token'];
            $user = $this->authenticateToken($token);

            if ($user) {
                $conn->user = $user;
            } else {
                echo "인증 실패: 유효하지 않은 토큰\n";
                $conn->close();
                return;
            }
        } else {
            echo "인증 실패: 토큰 없음\n";
            $conn->close();
            return;
        }

        // 채팅방 ID 추출
        if (isset($queryParams['chat_room'])) {
            $chatRoomId = $queryParams['chat_room'];

            // 사용자가 해당 채팅방에 참여하고 있는지 확인
            $chatRoom = Meeting::query()->find($chatRoomId);
            if (!$chatRoom) {
                echo "채팅방 없음: ID {$chatRoomId}\n";
                $conn->close();
                return;
            }

            $isParticipant = $chatRoom->users()->where('users.id', $conn->user->id)->exists();
            if (!$isParticipant) {
                echo "접근 거부: 사용자가 채팅방에 참여하지 않음\n";
                $conn->close();
                return;
            }

            // 연결 설정
            $conn->chatRoomId = $chatRoomId;
            if (!isset($this->chatRooms[$chatRoomId])) {
                $this->chatRooms[$chatRoomId] = new \SplObjectStorage();
            }
            $this->chatRooms[$chatRoomId]->attach($conn);

            echo "새로운 연결: 사용자 {$conn->user->id} / 채팅방 {$chatRoomId}\n";

        } else {
            echo "채팅방 ID 없음\n";
            $conn->close();
            return;
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        $chatRoomId = $from->chatRoomId;

        // 메시지 파싱 (JSON 형식으로 가정)
        $data = json_decode($msg, true);
        if (!$data || !isset($data['content'])) {
            echo "잘못된 메시지 형식\n";
            return;
        }

        // 메시지 저장
        $message = new Message();
        $message->content = $data['content'];
        $message->user_id = $from->user->id;
        $message->chat_room_id = $chatRoomId;
        $message->save();

        // 클라이언트에게 메시지 브로드캐스트
        $response = [
            'message_id' => $message->id,
            'user_id' => $message->user_id,
            'content' => $message->content,
            'created_at' => $message->created_at->toDateTimeString(),
        ];

        foreach ($this->chatRooms[$chatRoomId] as $client) {
            $client->send(json_encode($response));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {

        $chatRoomId = $conn->chatRoomId;

        // 해당 채팅방에서 연결을 제거
        $this->chatRooms[$chatRoomId]->detach($conn);

        // 만약 채팅방에 연결이 더 이상 없다면, 채팅방 자체를 제거
        if ($this->chatRooms[$chatRoomId]->count() === 0) {
            unset($this->chatRooms[$chatRoomId]);
        }

        echo "연결 종료: 사용자 {$conn->user->id} / 채팅방 {$chatRoomId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {

        echo "에러 발생: {$e->getMessage()}\n";

        $conn->close();
    }

    // 토큰 검증 메소드
    protected function authenticateToken($token)
    {
        try {
            $secretKey = config('app.jwt_secret'); // 환경 변수 또는 설정 파일에서 가져옴
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $userId = $decoded->sub; // 토큰에서 사용자 ID 추출

            return User::query()->find($userId);
        } catch (\Exception $e) {
            echo "토큰 검증 실패: {$e->getMessage()}\n";
            return null;
        }
    }
}
