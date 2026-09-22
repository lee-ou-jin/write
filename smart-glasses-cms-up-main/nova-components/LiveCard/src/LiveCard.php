<?php

namespace Jerry\LiveCard;

use App\Models\Meeting;
use App\Support\NovaReverbConfig;
use Laravel\Nova\Card;

class LiveCard extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

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
        return 'live-card';
    }

    public function resourceId($resourceId)
    {
        $meeting = Meeting::query()
            ->with('task:id,code')
            ->where('id', $resourceId)
            ->first();

        if (!$meeting || !$meeting->task) {
            return $this->withMeta([
                'userId' => auth()->id(),
                'meetingId' => $resourceId,
                'taskCode' => null,
            ]);
        }

        return $this->withMeta([
            'userId' => auth()->id(),
            'meetingId' => $resourceId,
            'taskCode' => $meeting->task->code,
            'echo' => NovaReverbConfig::make(),
        ]);
    }
}
