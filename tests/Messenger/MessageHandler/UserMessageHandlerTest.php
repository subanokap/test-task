<?php

namespace App\Tests\Messenger\MessageHandler;

use App\Messenger\MessageHandler\UserMessageHandler;
use App\Messenger\Message\UserMessage;
use App\Service\User\UserCreatorInterface;
use App\Tests\BaseTestCase;
use Doctrine\ORM\EntityManagerInterface;

class UserMessageHandlerTest extends BaseTestCase
{
    private UserCreatorInterface $userService;
    private UserMessageHandler $handler;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserCreatorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new UserMessageHandler($this->entityManager, $this->userService, $this->logger,);
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function testHandlerCallsServiceAndFlushes(): void
    {
        $message = new UserMessage([
            'email' => 'test@test.com',
            'password' => '123456',
            'firstName' => 'Elon',
            'lastName' => 'Musk'
        ], '1.1.1.1');

        $this->userService->expects($this->once())
            ->method('createUser');

        $this->entityManager->expects($this->once())
            ->method('flush');

        ($this->handler)($message);
    }
}