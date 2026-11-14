<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsAvatarRequest;
use App\Http\Requests\SettingsDetailsRequest;
use App\Http\Requests\SettingsPasswordRequest;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $settingsService) {}

    public function update_details(SettingsDetailsRequest $request): JsonResponse
    {
        $this->settingsService->attemptUpdateUserDetails($request->validated());

        return response()->json([
            'message' => 'Update details successful!',
        ]);
    }

    public function update_password(SettingsPasswordRequest $request): JsonResponse
    {
        $this->settingsService->attemptUpdateUserPassword($request->validated());

        return response()->json([
            'message' => 'Update password successful!',
        ]);
    }

    public function upload_avatar(SettingsAvatarRequest $request): JsonResponse
    {
        $this->settingsService->attemptUploadUserAvatar($request->file('avatar'));

        return response()->json([
            'message' => 'Upload avatar successful!',
        ]);
    }
}
