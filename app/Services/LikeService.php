<?php

namespace App\Services;

use App\Contracts\Likeable;
use App\Models\User;

class LikeService
{
    /**
     * @param Likeable $target
     * @param User $user
     * @return void
     */
    public function like(Likeable $target, User $user): void
    {
        $target->likers()->syncWithoutDetaching([$user->getKey()]);
    }

    /**
     * @param Likeable $target
     * @param User $user
     * @return void
     */
    public function unlike(Likeable $target, User $user): void
    {
        $target->likers()->detach($user->getKey());
    }
}
