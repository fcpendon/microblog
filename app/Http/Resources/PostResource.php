<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'author' => new UserResource($this->author),
            'content' => $this->content,
            'images' => PostImageResource::collection($this->images),
            'posted_at' => $this->posted_at,
            'is_liked' => count($this->likers) ? true : false,
            'likes' => $this->likes_count,
        ];
    }
}
