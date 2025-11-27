<?php

namespace App\Service\Validation\User;

use App\Repository\UserRepository;

readonly class UserPayloadValidator implements UserValidatorInterface
{
    private const MAX_PASSWORD_LENGTH = 6;

    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function validate(array $data): array
    {
        $errors = [];
        $requiredFields = ['email', 'firstName'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            $errors[] = sprintf("Email '%s' is already taken.", $data['email']);
        }

        if (isset($data['password']) && strlen($data['password']) < self::MAX_PASSWORD_LENGTH) {
            $errors[] = 'Password must be at least 6 characters';
        }

        return $errors;
    }
}