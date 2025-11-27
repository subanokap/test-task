<?php

namespace App\Service\User;

use App\Entity\User;
use App\Messenger\Message\UserMessage;
use App\Service\Geo\GeoLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService implements UserCreatorInterface
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly GeoLocatorInterface         $geoService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function createUser(UserMessage $message): User
    {
        $ip = $message->getIpAddress();

        try {
            $country = $this->geoService->getCountryByIp($ip);
        } catch (\Throwable $exception) {
            $country = 'Unknown';
        }

        $user = new User($message->getFirstName(), $message->getLastName(), $message->getPhoneNumbers(), $ip);

        $user->setEmail($message->getEmail());

        $hashedPassword = $this->passwordHasher->hashPassword($user, $message->getPassword());

        $user->setPassword($hashedPassword);
        $user->setCountry($country);

        $this->entityManager->persist($user);

        return $user;
    }
}