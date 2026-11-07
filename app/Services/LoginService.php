<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class LoginService
{
    /**
     * @param array $data
     */
    public function attemptLoginUser(array $data)
    {
        if (!Auth::attempt($data)) {
            throw new InvalidArgumentException('Invalid username or password.');
        }
    }
}
