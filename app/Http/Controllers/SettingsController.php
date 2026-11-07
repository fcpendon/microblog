<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsAvatarRequest;
use App\Http\Requests\SettingsDetailsRequest;
use App\Http\Requests\SettingsPasswordRequest;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $settingsService)
    {}

    /**
     * @param SettingsDetailsRequest $request
     * @return JsonResponse
     */
    public function update_details(SettingsDetailsRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->settingsService->attemptUpdateUserDetails($data);

        return response()->json([
            'message' => 'Update details successful!',
        ]);
    }

    /**
     * @param SettingsPasswordRequest $request
     * @return JsonResponse
     */
    public function update_password(SettingsPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $this->settingsService->attemptUpdateUserPassword($data);

        return response()->json([
            'message' => 'Update password successful!',
        ]);
    }

    /**
     * @param SettingsAvatarRequest $request
     * @return JsonResponse
     */
    public function upload_avatar(SettingsAvatarRequest $request): JsonResponse
    {
        $image = $request->file('avatar');

        $this->settingsService->attemptUploadUserAvatar($image);

        return response()->json([
            'message' => 'Upload avatar successful!',
        ]);
    }
}
