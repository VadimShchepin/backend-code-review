<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessageStatusEnum;
use App\Event\MessageSentEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SendMessageHandler
{
    public function __construct(
        private EntityManagerInterface   $manager,
        private EventDispatcherInterface $eventDispatcher)
    {
    }
    
    public function __invoke(SendMessage $sendMessage): void
    {
        $message = new Message(
            $sendMessage->text,
        );

        $this->manager->persist($message);
        $this->manager->flush();

        $this->eventDispatcher->dispatch(new MessageSentEvent($message), MessageSentEvent::NAME);

    }
}