<?php

namespace App\Service\Validation\User;

interface UserValidatorInterface
{
    public function validate(array $data): array;
}