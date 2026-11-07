<?php

namespace App\Services;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
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
     * @return JsonResource
     */
    public function getPosts(string $username, int $offset, int $limit): JsonResource
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
            ->firstOrFail();

        return new PostResource($post);
    }

    /**
     * @param array $data
     * @return void
     */
    public function create(array $data)
    {
        $post = Post::create([...$data, 'author_id' => Auth::id()]);

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
            throw new InvalidArgumentException('This post does not belong to you');
        }

        $post->update($data);

        if ($data['image']) {
            $this->postImageService->update($post, $data['image']);
        } elseif (empty($data['old_image'])) {
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

        if (!$post) {
            throw new InvalidArgumentException('This post does not belong to you');
        }

        $this->postImageService->delete($post);
        $post->delete();
    }

    /**
     * @param int $post_id
     * @return mixed
     */
    public function findOwnPost(int $post_id)
    {
        return Post::whereKey($post_id)
            ->where('author_id', Auth::id())
            ->firstOrFail();
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
