<?php

namespace App\Services;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PostService
{
    public function __construct(
        private PostImageService $postImageService,
        private PostLikeService $postLikeService
    ) {}

    /**
     * @param string $username
     * @param int $offset
     * @param int $limit
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getPosts(string $username, int $offset, int $limit)
    {
        $post = Post::when($username, function ($query, $username) {
                return $query->whereRelation('author', 'username', $username);
            })
            ->latest()
            ->with(['author:id,name,username,image_path', 'images'])
            ->with(['likers' => function ($query) {
                $query->select('user_id')
                    ->where('user_id', Auth::user()->id);
                }])
            ->withCount('likes')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return PostResource::collection($post);
    }

    /**
     * @param int $post_id
     * @return PostResource
     */
    public function getPost(int $post_id): PostResource
    {
        $post = Post::where('id', $post_id)
            ->with(['author:id,name,username,image_path', 'images'])
            ->with(['likers' => function ($query) {
                $query->select('user_id')
                    ->where('user_id', Auth::user()->id);
            }])
            ->withCount('likes')
            ->first();

        return new PostResource($post);
    }

    /**
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        $post = Auth::user()->posts()->create($data);

        if ($data['image']) {
            $this->postImageService->create($post, $data['image']);
        }
    }

    /**
     * @param int $post_id
     * @param array $data
     * @return void
     */
    public function update(int $post_id, array $data)
    {
        $post = $this->findOwnPost($post_id);

        if (!$post) {
            throw new InvalidArgumentException('This tweet does not belong to you');
        }

        $post->update($data);

        if ($data['image']) {
            $this->postImageService->update($post, $data['image']);
        } elseif (!$data['old_image']) {
            $this->postImageService->delete($post);
        }
    }

    /**
     * @param int $post_id
     * @return void
     */
    public function delete(int $post_id)
    {
        $post = $this->findOwnPost($post_id);

        $this->postImageService->delete($post);
        $post->delete();
    }

    /**
     * @param int $post_id
     * @return mixed
     */
    public function findOwnPost(int $post_id)
    {
        $post = Auth::user()->posts()->find($post_id);

        if (!$post) {
            throw new InvalidArgumentException('This tweet does not belong to you');
        }

        return $post;
    }

    /**
     * @param int $post_id
     * @return PostResource
     */
    public function likePost(int $post_id): PostResource
    {
        $this->postLikeService->like($post_id);

        return $this->getPost($post_id);
    }

    /**
     * @param int $post_id
     * @return PostResource
     */
    public function unlikePost(int $post_id): PostResource
    {
        $this->postLikeService->unlike($post_id);

        return $this->getPost($post_id);
    }
}
