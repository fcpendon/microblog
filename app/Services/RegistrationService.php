<?php

namespace App\Services;

use InvalidArgumentException;
use App\Models\User;

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
        $this->existsInDb($data['username']);

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
