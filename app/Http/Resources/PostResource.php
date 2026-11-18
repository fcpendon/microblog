<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'author' => new UserResource($this->author),
            'content' => $this->content,
            'images' => PostImageResource::collection($this->images),
            'created_at' => $this->created_at?->diffForHumans(['parts' => 2]),
            'is_liked' => $this->is_liked,
            'likes_count' => $this->whenCounted('likes'),
            'comments_count' => $this->whenCounted('comments'),
            'latest_comment' => new CommentResource($this->whenLoaded('latestComment')),
        ];
    }
}
