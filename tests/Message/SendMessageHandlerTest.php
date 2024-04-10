<?php

namespace App\Tests\Message;

use App\Entity\Message;
use App\Entity\MessageStatusEnum;
use App\Event\MessageSentEvent;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class SendMessageHandlerTest extends WebTestCase
{
    use InteractsWithMessenger;
    /**
     * @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $entityManagerMock;

    /**
     * @var EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $eventDispatcherMock;

    private SendMessageHandler $sendMessageHandler;
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->sendMessageHandler = new SendMessageHandler($this->entityManagerMock, $this->eventDispatcherMock);
    }
    public function test_send_message_persistence(): void
    {

        $sendMessage = new SendMessage('Test message');

        // Set up expectations before invoking the method that should trigger these actions
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Message::class));

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(MessageSentEvent::class));

        $this->sendMessageHandler->__invoke($sendMessage);
    }
}
