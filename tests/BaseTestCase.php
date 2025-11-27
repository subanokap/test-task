<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseTestCase extends TestCase
{
    protected EntityManagerInterface|MockObject $entityManager;
    protected HttpClientInterface|MockObject $httpClient;
    protected LoggerInterface|MockObject $logger;
    protected UserPasswordHasherInterface|MockObject $passwordHasher;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
    }
}