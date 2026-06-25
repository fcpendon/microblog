<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsAvatarRequest;
use App\Http\Requests\SettingsDetailsRequest;
use App\Http\Requests\SettingsPasswordRequest;
use App\Services\SettingsService;
use Exception;
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

        try {
            $this->settingsService->attemptUpdateUserDetails($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update details failed: ' . $e->getMessage(),
            ], 422);
        }

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

        try {
            $this->settingsService->attemptUpdateUserPassword($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update password failed: ' . $e->getMessage(),
            ], 422);
        }

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

        try {
            $this->settingsService->attemptUploadUserAvatar($image);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Upload avatar failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Upload avatar successful!',
        ]);
    }
}
