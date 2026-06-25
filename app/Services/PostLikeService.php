<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class PostLikeService
{
    /**
     * @param int $post_id
     * @return void
     */
    public function like(int $post_id)
    {
        Auth::user()->likes()->attach($post_id);
    }

    /**
     * @param int $post_id
     * @return void
     */
    public function unlike(int $post_id)
    {
        Auth::user()->likes()->detach($post_id);
    }
}
