<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'public_image_path' => $this->public_image_path,
        ];
    }
}
