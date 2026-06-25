<?php

namespace App\Services;

use App\Models\User;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function __construct(private UserService $userService)
    {}

    /**
     * @param array $data
     * @return string
     */
    public function attemptLoginUser(array $data): string
    {
        $user = $this->getUser($data);

        if (!Auth::attempt($data)) {
            throw new InvalidArgumentException('Oops, your password is incorrect');
        }

        return $user->createToken('auth-token')->plainTextToken;
    }

    /**
     * @param array $data
     * @return User
     */
    public function getUser(array $data): User
    {
        if (!$user = $this->userService->getByUsername($data['username'])) {
            throw new InvalidArgumentException('Uh oh! It seems like username is not registered yet');
        }

        return $user;
    }
}
