<?php

namespace App\Http\Controllers;

use App\Services\LogoutService;
use Exception;
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
        try {
            $this->logoutService->attemptLogout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Logout failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Logout successful!',
        ]);
    }
}
