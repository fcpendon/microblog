<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    const SEARCH_USER_LIMIT = 50;

    public function __construct(private UserService $userService) {}

    public function search(SearchUserRequest $request): AnonymousResourceCollection
    {
        $keyword = $request->validated('keyword');
        $limit = $request->validated('limit') ?? self::SEARCH_USER_LIMIT;

        return UserResource::collection(
            $this->userService->searchByNameOrUsername($keyword, $limit)
        );
    }

    public function get_profile(User $user): UserResource
    {
        return new UserResource($user);
    }
}
