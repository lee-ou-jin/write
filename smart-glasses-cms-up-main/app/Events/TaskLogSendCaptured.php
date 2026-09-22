<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskLogSendCaptured implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $taskCode;
    public string $imageUrl;
    public ?int $taskLogSendId;

    public function __construct($taskCode, $imageUrl, $taskLogSendId = null)
    {
        $this->taskCode = $taskCode;
        $this->imageUrl = $imageUrl;
        $this->taskLogSendId = $taskLogSendId;
    }

    public function broadcastOn()
    {
        return new Channel('task.' . $this->taskCode);
    }

    public function broadcastAs(): string
    {
        return 'TaskLogSendCaptured';
    }

    public function broadcastWith(): array
    {
        return [
            'taskCode' => $this->taskCode,
            'imageUrl' => $this->imageUrl,
            'taskLogSendId' => $this->taskLogSendId,
        ];
    }
}
