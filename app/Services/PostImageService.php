<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;

class PostImageService
{
    public function __construct(private StorageService $storageService) {}

    public function create(Post $post, UploadedFile $image): void
    {
        $data['image_path'] = $this->storageService->store($image, 'post_images');
        $post->images()->create($data);
    }

    public function update(Post $post, UploadedFile $image): void
    {
        $this->delete($post);
        $this->create($post, $image);
    }

    public function delete(Post $post): void
    {
        $post_images = $post->images()->get();

        foreach ($post_images as $post_image) {
            $this->storageService->delete($post_image->image_path);
            $post_image->delete();
        }
    }
}
