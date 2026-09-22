<?php

namespace App\Events;

use App\Models\TaskLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskLogCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $taskLog;

    /**
     * Create a new event instance.
     */
    public function __construct(TaskLog $taskLog)
    {
        $this->taskLog = $taskLog;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('task.' . $this->taskLog->task_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->taskLog->id,
            'task_id' => $this->taskLog->task_id,
            'image' => $this->taskLog->image,
        ];

    }
}
