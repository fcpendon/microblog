<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class SettingsService
{
    public function __construct(
        private UserService $userService,
        private RegistrationService $registrationService,
        private StorageService $storageService
    ) {}

    /**
     * @param array $data
     * @return void
     */
    public function attemptUpdateUserDetails(array $data)
    {
        $user_id = Auth::user()->id;
        $params = array();

        if ($this->isNewName($data['name'])) {
            $params['name'] = $data['name'];
        }
        if ($this->isNewUsername($data['username'])) {
            $params['username'] = $data['username'];
        }

        if (count($params)) {
            $this->userService->update($user_id, $params);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNewName(string $name): bool
    {
        return Auth::user()->name !== $name;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function isNewUsername(string $username): bool
    {
        return Auth::user()->username !== $username
            && !$this->registrationService->existsInDb($username);
    }

    /**
     * @param array $data
     * @return void
     */
    public function attemptUpdateUserPassword(array $data)
    {
        $user_id = Auth::user()->id;
        $params['password'] = bcrypt($data['password']);

        $this->userService->update($user_id, $params);
    }

    /**
     * @param $avatar
     * @return void
     */
    public function attemptUploadUserAvatar(UploadedFile $avatar)
    {
        $user_id = Auth::user()->id;
        $params['image_path'] = $this->storageService->store($avatar, 'users');

        $image_path = Auth::user()->image_path;
        if ($image_path) {
            $this->storageService->delete($image_path);
        }

        $this->userService->update($user_id, $params);
    }
}
