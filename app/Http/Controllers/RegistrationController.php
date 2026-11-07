<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Services\RegistrationService;
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

        $user = $this->registrationService->attemptRegisterUser($data);
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registration successful. Check your email for a verification link to finish setting up your account.',
        ], JsonResponse::HTTP_CREATED);
    }
}
