<?php

namespace App\Services;

use App\Contracts\Likeable;
use App\Models\User;

class LikeService
{
    public function like(Likeable $target, User $user): void
    {
        $target->likers()->syncWithoutDetaching([$user->getKey()]);
    }

    public function unlike(Likeable $target, User $user): void
    {
        $target->likers()->detach($user->getKey());
    }
}
