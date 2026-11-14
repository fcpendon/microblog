<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostIndexRequest;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    const POSTS_OFFSET_DEFAULT = 0;

    const POSTS_LIMIT_DEFAULT = 10;

    /**
     * Create a controller using the post service.
     */
    public function __construct(private PostService $postService) {}

    /**
     * List all posts, or optionally filter using a username.
     */
    public function index(PostIndexRequest $request, string $username = ''): AnonymousResourceCollection
    {
        $data = $request->validated();
        $offset = $data['offset'] ?? self::POSTS_OFFSET_DEFAULT;
        $limit = $data['limit'] ?? self::POSTS_LIMIT_DEFAULT;

        return PostResource::collection(
            $this->postService->getPosts($username, $offset, $limit)
        );
    }

    /**
     * Create a post for the authenticated user.
     */
    public function store(PostRequest $request): JsonResponse
    {
        return (new PostResource(
            $this->postService->create(
                $request->validated(),
                $request->file('image')
            )
        ))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Update a post owned by the authenticated user.
     */
    public function update(PostRequest $request, Post $post): PostResource
    {
        return new PostResource(
            $this->postService->update(
                $post,
                $request->validated(),
                $request->file('image')
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
