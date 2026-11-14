<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function create(array $data): User
    {
        $user = new User($data);
        $user->password = $user->password ? bcrypt($user->password) : null;
        $user->save();

        return $user->fresh();
    }

    public function searchByNameOrUsername(string $keyword, int $limit): Collection
    {
        return User::where('username', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->distinct('username')
            ->limit($limit)
            ->get();
    }
}
