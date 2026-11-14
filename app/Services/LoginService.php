<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class LoginService
{
    public function attemptLoginUser(array $data): void
    {
        if (! Auth::attempt($data)) {
            throw new InvalidArgumentException('Invalid username or password.');
        }
    }
}
