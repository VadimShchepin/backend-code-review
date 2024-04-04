<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function test_it_has_connection(): void
    {
        self::bootKernel();
        $repository = self::getContainer()->get(MessageRepository::class);

        $this->assertInstanceOf(MessageRepository::class, $repository);

        $messages = $repository->findAll();

        $this->assertSame([], $messages);
    }
}