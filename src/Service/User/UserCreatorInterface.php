<?php

namespace App\Service\User;

use App\Entity\User;
use App\Messenger\Message\UserMessage;

interface UserCreatorInterface
{
    public function createUser(UserMessage $message): User;
}