<?php

namespace App\Http\Controllers;

use App\Services\LogoutService;
use Exception;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(private LogoutService $logoutService)
    {}

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->logoutService->attemptLogout();
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
