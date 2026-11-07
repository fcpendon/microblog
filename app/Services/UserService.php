<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserService
{
    /**
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        $user = new User($data);
        $user->password = $user->password ? bcrypt($user->password) : null;
        $user->save();

        return $user->fresh();
    }

    /**
     * @param string $keyword
     * @param int $limit
     * @return JsonResource
     */
    public function searchByNameOrUsername(string $keyword, int $limit): JsonResource
    {
        $user = User::where('username', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->distinct('username')
            ->limit($limit)
            ->get();

        return UserResource::collection($user);
    }

    /**
     * @param string $username
     * @return User | null
     */
    public function getByUsername(string $username): User | null
    {
        return User::where('username', $username)->first();
    }

    /**
     * @param string $username
     * @return UserResource
     */
    public function getByUsernameResource(string $username): UserResource
    {
        return new UserResource(User::where('username', $username)->firstOrFail());
    }

    /**
     * @param string $user_id
     * @param array $data
     * @return void
     */
    public function update(string $user_id, array $data)
    {
        User::find($user_id)->update($data);
    }
}
