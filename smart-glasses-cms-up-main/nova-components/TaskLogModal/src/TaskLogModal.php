<?php

namespace Jerry\TaskLogModal;

use App\Models\Meeting;
use App\Support\NovaReverbConfig;
use Laravel\Nova\Card;

class TaskLogModal extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'task-log-modal';
    }

    public function resourceId($resourceId)
    {
        $meeting = Meeting::query()
            ->with('task:id,code')
            ->where('id', $resourceId)
            ->first();

        if (!$meeting || !$meeting->task) {
            return $this->withMeta([
                'taskId' => null,
                'taskCode' => null,
            ]);
        }

        return $this->withMeta([
            'taskId' => $meeting->task_id,
            'taskCode' => $meeting->task->code,
            'echo' => NovaReverbConfig::make(),
        ]);
    }
}
