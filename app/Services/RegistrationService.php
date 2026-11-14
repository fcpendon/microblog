<?php

namespace App\Services;

use App\Models\User;
use InvalidArgumentException;

class RegistrationService
{
    public function __construct(private UserService $userService) {}

    public function attemptRegisterUser(array $data): User
    {
        return $this->userService->create($data);
    }
}
