<?php

namespace App\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LogoutService
{
    /**
     *
     * @throws AuthenticationException
     */
    public function attemptLogout()
    {
        if (!Auth::user()) {
            throw new AuthenticationException('User already logged out');
        }

        Auth::user()->tokens()->delete();
        Auth::logout();
    }
}
