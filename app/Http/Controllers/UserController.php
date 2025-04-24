<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserEditRequest;
use App\Http\Resources\FollowUserCollection;
use App\Http\Resources\UserGridCollection;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    )
    {
        $this->middleware('auth:api', ['only' => ['follow', 'unfollow', 'edit', 'setFollow']]);
    }

    public function getFullname(string $username): JsonResponse
    {
        return $this->userService->getUserFullname($username);
    }

    public function setFollow(string $username): JsonResponse
    {
        return $this->userService->toggleFollow(
            auth()->user(),
            $username
        );
    }

    public function getUserPage(string $username): JsonResponse
    {
        return $this->userService->getUserProfilePage($username);
    }

    public function getUser(string $username): JsonResponse
    {
        return $this->userService->getUserWithStats(
            $username,
            auth()->user()
        );
    }

    public function getFollowers(Request $request, User $user): FollowUserCollection
    {
        return $this->userService->getUserFollowers(
            $user,
            $request->input('page', 1),
            auth()->user()
        );
    }

    public function getFollowing(Request $request, User $user): FollowUserCollection
    {
        return $this->userService->getUserFollowing(
            $user,
            $request->input('page', 1),
            auth()->user()
        );
    }

    public function edit(UserEditRequest $request): JsonResponse
    {
        return $this->userService->updateUserProfile(
            auth()->user(),
            $request->validated(),
            $request->file('avatar'),
            $request->only(['website', 'instagram', 'reddit', 'x']
            )
        );
    }

    public function getCreatorsByCategory(Request $request): UserGridCollection|JsonResponse
    {
        return $this->userService->getCreators(
            $request->input('sort'),
            $request->input('page', 1),
            $request->input('seed', time()),
            auth()->user()
        );
    }
}
