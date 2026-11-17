<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostIndexRequest;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Create a controller using the post service.
     */
    public function __construct(private PostService $postService)
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * List all posts, or optionally filter using a username.
     */
    public function index(PostIndexRequest $request, ?User $user = null): AnonymousResourceCollection
    {
        return PostResource::collection(
            $this->postService->getPosts(
                $request->validated('offset'),
                $request->validated('limit'),
                $user
            )
        );
    }

    /**
     * Create a post for the authenticated user.
     */
    public function store(PostRequest $request): PostResource
    {
        return new PostResource(
            $this->postService->create(
                $request->validated(),
                $request->file('image')
            )
        );
    }

    /**
     * Update a post owned by the authenticated user.
     */
    public function update(PostRequest $request, Post $post): PostResource
    {
        return new PostResource(
            $this->postService->update(
                $request->validated(),
                $request->file('image'),
                $post
            )
        );
    }

    /**
     * Delete a post owned by the authenticated user.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->postService->delete($post);

        return response()->json([
            'message' => 'Delete post successful!',
        ]);
    }

    /**
     * Like a post.
     */
    public function like(Post $post): PostResource
    {
        return new PostResource(
            $this->postService->like($post)
        );
    }

    /**
     * Unlike a post.
     */
    public function unlike(Post $post): PostResource
    {
        return new PostResource(
            $this->postService->unlike($post)
        );
    }
}
