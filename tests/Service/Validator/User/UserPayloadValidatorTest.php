<?php

namespace App\Tests\Service\Validator\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Validation\User\UserPayloadValidator;
use PHPUnit\Framework\TestCase;

class UserPayloadValidatorTest extends TestCase
{
    private UserRepository $userRepository;
    private UserPayloadValidator $validator;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->validator = new UserPayloadValidator($this->userRepository);
    }

    public function testReturnsErrorsOnMissingRequiredFields(): void
    {
        $data = [
            'lastName' => 'Musk',
            'password' => '123456',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(2, $errors);
        $this->assertContains("Field 'email' is required", $errors);
        $this->assertContains("Field 'firstName' is required", $errors);
    }

    public function testReturnsErrorOnInvalidEmailFormat(): void
    {
        $data = [
            'email' => 'invalid-email-format',
            'firstName' => 'Elon',
            'lastName' => 'Musk',
            'password' => '123456',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(1, $errors);
        $this->assertContains('Invalid email format', $errors);
    }

    public function testReturnsErrorOnDuplicateEmail(): void
    {
        $data = [
            'email' => 'exists@test.com',
            'firstName' => 'Elon',
            'lastName' => 'Musk',
            'password' => '123456',
        ];

        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'exists@test.com'])
            ->willReturn($this->createMock(User::class));

        $errors = $this->validator->validate($data);

        $this->assertContains('Email \'exists@test.com\' is already taken.', $errors);
    }
}