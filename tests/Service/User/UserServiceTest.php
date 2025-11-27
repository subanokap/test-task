<?php

namespace App\Tests\Service\User;

use App\Messenger\Message\UserMessage;
use App\Service\Geo\GeoLocatorInterface;
use App\Service\User\UserService;
use App\Tests\BaseTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends BaseTestCase
{
    private GeoLocatorInterface $geoService;
    private UserPasswordHasherInterface $hasher;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->geoService = $this->createMock(GeoLocatorInterface::class);
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->hasher->method('hashPassword')->willReturn('hashed_secure_password');

        $this->userService = new UserService($this->entityManager, $this->geoService, $this->hasher);
    }

    public function testUserIsCreatedAndPasswordHashed(): void
    {
        $ip = '10.0.0.1';

        $this->geoService->expects($this->once())->method('getCountryByIp')
            ->with($ip)
            ->willReturn('PL');

        $this->entityManager->expects($this->once())->method('persist');

        $message = new UserMessage([
            'email' => 'test@user.com',
            'password' => 'testpass',
            'firstName' => 'Jan',
            'lastName' => 'Kowalski',
            'phoneNumbers' => ['+48123']
        ], $ip);

        $user = $this->userService->createUser($message);

        $this->assertEquals('test@user.com', $user->getEmail());
        $this->assertEquals('PL', $user->getCountry());

        $this->assertEquals('hashed_secure_password', $user->getPassword());
    }
}