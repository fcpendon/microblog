<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class SettingsService
{
    public function __construct(private StorageService $storageService) {}

    public function attemptUpdateUserDetails(array $data): void
    {
        $$user = Auth::user();
        $params = [];
        $emailChanged = false;

        if ($this->isNewName($data['name'])) {
            $params['name'] = $data['name'];
        }

        if ($this->isNewUsername($data['username'])) {
            $params['username'] = $data['username'];
        }

        if (array_key_exists('email', $data) && $user->email !== $data['email']) {
            $params['email'] = $data['email'];
            $params['email_verified_at'] = null;
            $emailChanged = true;
        }

        if (count($params)) {
            $user->update($params);
        }

        if ($emailChanged) {
            $user->refresh()->sendEmailVerificationNotification();
        }
    }

    public function isNewName(string $name): bool
    {
        return Auth::user()->name !== $name;
    }

    public function isNewUsername(string $username): bool
    {
        return Auth::user()->username !== $username;
    }

    public function attemptUpdateUserPassword(array $data): void
    {
        /** @var User $user */
        $user = Auth::user();
        $params['password'] = bcrypt($data['password']);

        $user->update($params);
        $user->tokens()->delete();
    }

    public function attemptUploadUserAvatar(UploadedFile $avatar): void
    {
        /** @var User $user */
        $user = Auth::user();
        $params['image_path'] = $this->storageService->store($avatar, 'users');

        $image_path = $user->image_path;
        if ($image_path) {
            $this->storageService->delete($image_path);
        }

        $user->update($params);
    }
}
