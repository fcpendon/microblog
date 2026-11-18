<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    public function __construct(private LikeService $likeService) {}

    /**
     * Retrieve a post's comments in chronological order.
     *
     * @return Collection<int, Comment>
     */
    public function getForPost(Post $post): Collection
    {
        return $post->comments()
            ->with([
                'author:id,name,username,image_path',
            ])
            ->withCount('likes')
            ->withExists([
                'likers as is_liked' => fn ($query) => $query->where('users.id', Auth::id()),
            ])
            ->latest()
            ->get();
    }

    /**
     * Create a comment for the authenticated user.
     */
    public function create(string $content, Post $post): Comment
    {
        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $content,
        ]);

        return $this->loadComment($comment);
    }

    /**
     * Update a comment owned by the authenticated user.
     */
    public function update(string $content, Comment $comment): Comment
    {
        $comment = $this->findOwnComment($comment);
        $comment->update(['content' => $content]);

        return $this->loadComment($comment);
    }

    /**
     * Delete a comment owned by the authenticated user.
     */
    public function delete(Comment $comment): void
    {
        $this->findOwnComment($comment)->delete();
    }

    /**
     * Like a comment.
     */
    public function like(Comment $comment): Comment
    {
        $this->likeService->like($comment, Auth::user());

        return $this->loadComment($comment);
    }

    /**
     * Unlike a comment.
     */
    public function unlike(Comment $comment): Comment
    {
        $this->likeService->unlike($comment, Auth::user());

        return $this->loadComment($comment);
    }

    /**
     * Find a comment owned by the authenticated user.
     */
    private function findOwnComment(Comment $comment): Comment
    {
        return Comment::query()
            ->whereKey($comment->getKey())
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    /**
     * Load the comment relationships and counters used by CommentResource.
     */
    private function loadComment(Comment $comment): Comment
    {
        return Comment::query()
            ->whereKey($comment->getKey())
            ->with([
                'author:id,name,username,image_path',
            ])
            ->withCount('likes')
            ->withExists([
                'likers as is_liked' => fn ($query) => $query->where('users.id', Auth::id()),
            ])
            ->firstOrFail();
    }
}
