<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskImageCaptured implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meetingId;
    public $imageUrl;

    public function __construct($meetingId, $imageUrl)
    {
        $this->meetingId = $meetingId;
        $this->imageUrl = $imageUrl;
    }

    public function broadcastOn()
    {
        return new Channel('meeting.' . $this->meetingId);
    }

    public function broadcastAs()
    {
        return 'TaskImageCaptured';
    }

    public function broadcastWith()
    {
        return [
            'imageUrl' => $this->imageUrl,
        ];
    }
}
