<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->short_id,
            'author' => new UserResource($this->author),
            'content' => $this->content,
            'created_at' => $this->created_at?->diffForHumans(['parts' => 2]),
            'is_liked' => $this->is_liked,
            'likes_count' => $this->whenCounted('likes'),
            'is_mine' => Auth::id() === $this->user_id,
        ];
    }
}
