<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    const SEARCH_USER_LIMIT = 50;

    public function __construct(private UserService $userService)
    {}

    /**
     * @param SearchUserRequest $request
     * @return JsonResponse
     */
    public function search(SearchUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $keyword = $data['keyword'];
        $limit = $data['limit'] ?? self::SEARCH_USER_LIMIT;
        $users = $this->userService->searchByNameOrUsername($keyword, $limit);

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\UserResource|JsonResponse
     */
    public function get_profile(Request $request)
    {
        return $this->userService->getByUsernameResource($request->user);
    }
}
