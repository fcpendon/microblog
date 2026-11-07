<?php

namespace App\Http\Controllers;

use App\Services\LogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(private LogoutService $logoutService)
    {}

    /**
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->logoutService->attemptLogout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful!',
        ]);
    }
}
