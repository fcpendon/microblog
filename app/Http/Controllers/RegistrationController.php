<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Services\RegistrationService;
use Exception;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(private RegistrationService $registrationService)
    {}

    /**
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->registrationService->attemptRegisterUser($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Registration successful!',
        ]);
    }
}
