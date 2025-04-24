<?php

namespace App\Services;

use App\Http\Resources\FollowUserCollection;
use App\Http\Resources\UserGridCollection;
use App\Http\Responses\ApiResponse;
use App\Models\Social;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserService
{
    public function getUserFullname(string $username): JsonResponse
    {
        $user = User::query()->where('username', $username)->firstOrFail();

        return ApiResponse::success(
            'user',
            'fullname_retrieved',
            ['fullname' => $user->fullname]
        );
    }

    public function toggleFollow(User $authUser, string $username): JsonResponse
    {
        $user = User::query()->where('username', $username)->firstOrFail();

        if ($user->id === $authUser->id) {
            return ApiResponse::error('user', 'cannot_follow_self', 403);
        }

        $isFollowing = $authUser->following()->toggle($user->id);
        $followed = !empty($isFollowing['attached']);

        return ApiResponse::success(
            'user',
            $followed ? 'followed' : 'unfollowed',
            ['is_followed' => $followed]
        );
    }

    public function getUserProfilePage(string $username): JsonResponse
    {
        $user = User::query()->select("username", "fullname", "avatar")
            ->withCount('videos')
            ->where('username', $username)
            ->firstOrFail();

        return ApiResponse::success(
            'user',
            'profile_retrieved',
            ['data' => $user]
        );
    }

    public function getUserWithStats(string $username, User $authUser = null): JsonResponse
    {
        $user = User::query()->withCount([
            'videos as views' => fn($q) => $q->select(DB::raw('COALESCE(SUM(views), 0)')),
            'followers',
            'following',
            'videos'
        ])
            ->where('username', $username)
            ->firstOrFail();

        $responseData = [
            'user' => $user,
            'social_links' => $user->social_links()->first(),
        ];

        if ($authUser) {
            $responseData['auth_user'] = [
                'is_following' => $user->isFollowing($authUser),
                'is_followed' => $user->isFollowedBy($authUser),
            ];
        }

        return ApiResponse::success('user', 'retrieved', $responseData);
    }

    public function getUserFollowers(User $user, int $page, User $authUser = null): FollowUserCollection
    {
        $followers = $user->followers()->withCount('followers')->paginate(
            perPage: 16,
            page: $page
        );
        $currentUser  = auth()->user();
        $followedUserIds = $currentUser ? $currentUser->following()->pluck('users.id')->toArray() : [];
        $totalPages = $followers->lastPage();

        return new FollowUserCollection($followers, $followedUserIds, $totalPages);
    }

    public function getUserFollowing(User $user, int $page, User $authUser = null): FollowUserCollection
    {
        $following = $user->following()->withCount('followers')->paginate(
            perPage: 16,
            page: $page
        );

        $currentUser  = auth()->user();
        $followedUserIds = $currentUser ? $currentUser->following()->pluck('users.id')->toArray() : [];
        $totalPages = $following->lastPage();

        return new FollowUserCollection($following, $followedUserIds, $totalPages);
    }

    public function updateUserProfile(User $user, array $data, UploadedFile $avatar, array $links): JsonResponse
    {
        if ($avatar) {
            $manager = new ImageManager(new Driver());
            $avatarName = $user->username . '.webp';

            $manager->read($avatar)
                ->toWebp()
                ->save(public_path('storage/avatars/' . $avatarName));

            $data['avatar'] = '/storage/avatars/' . $avatarName;
        }

        $user->update($data);

        if ($social = Social::query()->where('user_id', '=', $user->id)->first()) {
            $social->fill($links);
            $social->save();
        } else {
            $social = Social::query()->make($links);

            $user->social_links()->save($social);
        }

        return ApiResponse::success(
            'user',
            'profile_updated',
            [
                'user' => $user->only(['fullname', 'avatar', 'description']),
                'social_links' => $links
            ]
        );
    }

    public function getCreators(string $sort, int $page, int $seed, User $authUser = null): UserGridCollection|JsonResponse
    {
        $query = User::query()->has('videos')
            ->withCount([
                'videos as views' => fn($q) => $q->select(DB::raw('COALESCE(SUM(views), 0)')),
                'followers',
                'following',
                'videos'
            ]);

        switch ($sort) {
            case 'explore':
                $query->orderByRaw("RAND($seed)");
                break;
            case 'hot-and-new':
                $query->where('created_at', '>=', now()->subDays(30))
                    ->orderByRaw("RAND($seed)");
                break;
            case 'trending':
                $query->orderBy('views', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                return ApiResponse::error('user', 'invalid_sort_parameter', 404);
        }

        $result = $query->paginate(
            perPage: 18,
            page: $page
        );

        $followedIds = $authUser ? $authUser->following()->pluck('users.id')->toArray() : [];

        return new UserGridCollection($result, $result->lastPage(), $followedIds);
    }
}
