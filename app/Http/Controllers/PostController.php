<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\PostIndexRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    const POSTS_OFFSET_DEFAULT = 0;
    const POSTS_LIMIT_DEFAULT = 10;

    public function __construct(private PostService $postService)
    {}

    /**
     * @param Request $request
     * @param string $username
     * @return JsonResponse
     */
    public function get_posts(PostIndexRequest $request, string $username = ''): JsonResponse
    {
        $data = $request->validated();

        $offset = $data['offset'] ?? self::POSTS_OFFSET_DEFAULT;
        $limit = $data['limit'] ?? self::POSTS_LIMIT_DEFAULT;
        $posts = $this->postService->getPosts($username, $offset, $limit);

        return response()->json([
            'data' => $posts,
        ]);
    }

    /**
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function create(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['image'] = $request->file('image') ?? null;

        $this->postService->create($data);

        return response()->json([
            'message' => 'Create post successful!',
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function update(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['image'] = $request->file('image') ?? null;

        $this->postService->update($request->id, $data);

        return response()->json([
            'message' => 'Update post successful!',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->postService->delete($request->id);

        return response()->json([
            'message' => 'Delete post successful!',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function like_post(Request $request): JsonResponse
    {
        $result = $this->postService->likePost($request->id);

        return response()->json([
            'message' => 'Like post successful!',
            'data' => $result,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function unlike_post(Request $request): JsonResponse
    {
        $result = $this->postService->unlikePost($request->id);

        return response()->json([
            'message' => 'Unlike post successful!',
            'data' => $result,
        ]);
    }
}
