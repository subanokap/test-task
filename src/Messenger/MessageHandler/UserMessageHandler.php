<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\UserMessage;
use App\Service\User\UserCreatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserCreatorInterface   $userService,
        private readonly LoggerInterface        $logger
    ) {}

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    public function __invoke(UserMessage $message): void
    {
        try {
            $this->userService->createUser($message);

            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $errorMessage = sprintf(
                'CRITICAL ERROR in %s. Exception: %s. Message: %s. Context: [Name: %s %s, Email: %s, IP: %s, Phones: %s]',
                __METHOD__,
                get_class($exception),
                $exception->getMessage(),
                $message->getFirstName(),
                $message->getLastName(),
                $message->getEmail(),
                $message->getIpAddress(),
                json_encode($message->getPhoneNumbers(), JSON_THROW_ON_ERROR)
            );

            $this->logger->critical($errorMessage, ['exception' => $exception]);

            throw $exception;
        }
    }
}