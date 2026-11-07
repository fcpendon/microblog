<?php

namespace App\Services;

use App\Models\User;
use InvalidArgumentException;

class RegistrationService
{
    public function __construct(private UserService $userService)
    {}

    /**
     * @param array $data
     * @return User
     */
    public function attemptRegisterUser(array $data): User
    {
        return $this->userService->create($data);
    }

    /**
     * @param string $username
     * @return bool
     */
    public function existsInDb(string $username): bool
    {
        if ($this->userService->getByUsername($username)) {
            throw new InvalidArgumentException('Username already exists');
        }

        return false;
    }
}
