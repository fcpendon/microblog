<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {}

    /**
     * @param SearchUserRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(SearchUserRequest $request)
    {
        $data = $request->validated();

        $name = $data['name'];
        $limit = $data['limit'] ?? 100;

        return $this->userService->searchByNameOrUsername($name, $limit);
    }

    /**
     * @param Request $request
     * @return \App\Http\Resources\UserResource|JsonResponse
     */
    public function get_profile(Request $request)
    {
        try {
            return $this->userService->getByUsernameResource($request->user);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => 'User error: ' . $e->getMessage(),
            ], 422);
        }
    }
}
