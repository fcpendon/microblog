<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Create a controller using the comment service.
     */
    public function __construct(private CommentService $commentService)
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    /**
     * List comments belonging to a post.
     */
    public function index(Post $post): AnonymousResourceCollection
    {
        return CommentResource::collection(
            $this->commentService->getForPost($post)
        );
    }

    /**
     * Create a comment on a post.
     */
    public function store(CommentRequest $request, Post $post): CommentResource
    {
        return new CommentResource(
            $this->commentService->create(
                $request->validated('content'),
                $post
            )
        );
    }

    /**
     * Update a comment owned by the authenticated user.
     */
    public function update(CommentRequest $request, Comment $comment): CommentResource
    {
        return new CommentResource(
            $this->commentService->update(
                $request->validated('content'),
                $comment
            )
        );
    }

    /**
     * Delete a comment owned by the authenticated user.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->commentService->delete($comment);

        return response()->json([
            'message' => 'Delete comment successful!',
        ]);
    }

    /**
     * Like a comment.
     */
    public function like(Comment $comment): CommentResource
    {
        return new CommentResource(
            $this->commentService->like($comment)
        );
    }

    /**
     * Unlike a comment.
     */
    public function unlike(Comment $comment): CommentResource
    {
        return new CommentResource(
            $this->commentService->unlike($comment)
        );
    }
}
