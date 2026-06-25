<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Exception;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(private LoginService $loginService)
    {}

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $token = $this->loginService->attemptLoginUser($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token
        ]);
    }
}
