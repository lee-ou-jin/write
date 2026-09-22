<?php

namespace Jerry\ChatCard;

use App\Models\Message;
use Laravel\Nova\Card;

class ChatCard extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

//    public $user;

    // 모델 가져오기
    public function __construct($resourceId)
    {
        $this->withMeta([
            'user' => auth()->user(),
        ]);

        parent::__construct();
    }


    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'chat-card';
    }

    public function resourceId($resourceId)
    {
        return $this->withMeta([
            'messages' => Message::query()->where('meeting_id', $resourceId)->get(),
        ]);
    }

//    public function userId($user)
//    {
//        return $this->withMeta([
//            'user' => $user
//        ]);
//    }
}
