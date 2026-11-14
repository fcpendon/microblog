<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class LoginController extends Controller
{
    public function __construct(private LoginService $loginService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $this->loginService->attemptLoginUser($request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Invalid username or password.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful!',
        ]);
    }
}
