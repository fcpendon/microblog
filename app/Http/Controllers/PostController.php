<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $postService)
    {}

    /**
     * @param Request $request
     * @param string $username
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get_posts(Request $request, string $username = '')
    {
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;

        return $this->postService->getPosts($username, $offset, $limit);
    }

    /**
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function create(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['image'] = $request->file('image') ?? null;

        try {
            $this->postService->create($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Post tweet failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Post tweet successful!',
        ]);
    }

    /**
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function update(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['image'] = $request->file('image') ?? null;

        try {
            $this->postService->update($request->id, $data);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Update tweet failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Update tweet successful!',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $this->postService->delete($request->id);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Delete tweet failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Delete tweet successful!',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function like_post(Request $request): JsonResponse
    {
        try {
            $result = $this->postService->likePost($request->id);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Like post failed: ' . $e->getMessage(),
            ], 422);
        }

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
        try {
            $result = $this->postService->unlikePost($request->id);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unlike post failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Unlike post successful!',
            'data' => $result,
        ]);
    }
}
