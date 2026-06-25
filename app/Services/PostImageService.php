<?php

namespace App\Services;

use App\Models\Post;

class PostImageService
{
    public function __construct(private StorageService $storageService)
    {}

    /**
     * @param Post $post
     * @param $image
     * @return void
     */
    public function create(Post $post, $image)
    {
        $data['image_path'] = $this->storageService->store($image, 'post_images');
        $post->images()->create($data);
    }

    /**
     * @param Post $post
     * @param $image
     * @return void
     */
    public function update(Post $post, $image)
    {
        $this->delete($post);
        $this->create($post, $image);
    }

    /**
     * @param Post $post
     * @return void
     */
    public function delete(Post $post)
    {
        $post_images = $post->images()->get();

        foreach ($post_images as $post_image) {
            $this->storageService->delete($post_image->image_path);
            $post_image->delete();
        }
    }
}
