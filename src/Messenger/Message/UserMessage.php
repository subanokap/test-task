<?php

namespace App\Messenger\Message;

class UserMessage
{
    public function __construct(
        private array  $payload,
        private string $ipAddress
    ) {}

    public function getFirstName(): string
    {
        return $this->payload['firstName'];
    }

    public function getLastName(): string
    {
        return $this->payload['lastName'];
    }

    public function getEmail(): string
    {
        return $this->payload['email'];
    }

    public function getPassword(): string
    {
        return $this->payload['password'];
    }

    public function getPhoneNumbers(): array
    {
        return $this->payload['phoneNumbers'] ?? [];
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }
}