<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER, nullable: false)]
    private ?int $id = null;
    #[ORM\Column(name: 'email', type: Types::STRING, length: 180, unique: true, nullable: false)]
    private ?string $email = null;
    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    private array $roles = [];
    #[ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: false)]
    private ?string $password = null;
    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: false)]
    private string $firstName;
    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: false)]
    private string $lastName;
    #[ORM\Column(name: 'phone_numbers', type: Types::JSON, nullable: false)]
    private array $phoneNumbers;
    #[ORM\Column(name: 'ip_address', type: Types::STRING, length: 45, nullable: false)]
    private string $ipAddress;
    #[ORM\Column(name: 'country_code', type: Types::STRING, length: 10, nullable: true)]
    private ?string $country = null;
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $firstName, string $lastName, array $phoneNumbers, string $ipAddress)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumbers = $phoneNumbers;
        $this->ipAddress = $ipAddress;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    public function setPhoneNumbers(array $phoneNumbers): self
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}