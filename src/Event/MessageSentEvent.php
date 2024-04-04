<?php

namespace App\Event;
use App\Entity\Message;

use Symfony\Contracts\EventDispatcher\Event;

class MessageSentEvent extends Event
{

    /**
     * @param Message $message
     */
    public function __construct(readonly private Message $message)
    {
    }
    public const NAME = 'message.sent';

    public function getMessage(): Message
    {
        return $this->message;
    }
}