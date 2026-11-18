<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function __construct(
        private PostImageService $postImageService,
        private LikeService $likeService
    ) {}

    /**
     * Retrieve a paginated set of posts for the feed.
     * Can filter out posts based on user.
     *
     * @return Collection<int, Post>
     */
    public function getPosts(int $offset, int $limit, ?User $user): Collection
    {
        return Post::query()
            ->when($user, fn ($query) => $query->where('user_id', $user->id))
            ->with([
                'author:id,name,username,image_path',
                'images',
                'latestComment.author:id,name,username,image_path',
            ])
            ->withCount(['likes', 'comments'])
            ->withExists([
                'likers as is_liked' => fn ($query) => $query->where('users.id', Auth::id()),
            ])
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Create a post for the authenticated user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $image): Post
    {
        $post = Post::create([...$data, 'user_id' => Auth::id()]);

        if ($image) {
            $this->postImageService->create($post, $image);
        }

        return $this->loadPost($post);
    }

    /**
     * Update a post owned by the authenticated user.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(array $data, ?UploadedFile $image, Post $post): Post
    {
        $post = $this->findOwnPost($post);
        $post->update($data);

        if ($image) {
            $this->postImageService->update($post, $image);
        } elseif (empty($data['old_image'])) {
            $this->postImageService->delete($post);
        }

        return $this->loadPost($post);
    }

    /**
     * Delete a post owned by the authenticated user.
     */
    public function delete(Post $post): void
    {
        $post = $this->findOwnPost($post);

        $this->postImageService->delete($post);
        $post->delete();
    }

    /**
     * Like a post.
     */
    public function like(Post $post): Post
    {
        $this->likeService->like($post, Auth::user());

        return $this->loadPost($post);
    }

    /**
     * Unlike a post.
     */
    public function unlike(Post $post): Post
    {
        $this->likeService->unlike($post, Auth::user());

        return $this->loadPost($post);
    }

    /**
     * Find a post owned by the authenticated user.
     */
    private function findOwnPost(Post $post): Post
    {
        return Post::query()
            ->whereKey($post->getKey())
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    /**
     * Load the post relationships and counters used by PostResource.
     */
    private function loadPost(Post $post): Post
    {
        return Post::query()
            ->whereKey($post->getKey())
            ->with([
                'author:id,name,username,image_path',
                'images',
                'latestComment.author:id,name,username,image_path',
            ])
            ->withCount('likes')
            ->withExists([
                'likers as is_liked' => fn ($query) => $query->where('users.id', Auth::id()),
            ])
            ->firstOrFail();
    }
}
