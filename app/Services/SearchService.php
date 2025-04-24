<?php

namespace App\Services;

use App\Http\Resources\UserGridCollection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SearchService
{
    public function search(
        ?string $searchTerm,
        int $videoLimit,
        int $userLimit,
        int $tagLimit,
        bool $isSearchPage,
        ?User $currentUser
    ): array {
        $searchTerm = $searchTerm ? "%$searchTerm%" : null;
        $followedUserIds = $currentUser ? $currentUser->following()->pluck('users.id')->toArray() : [];

        return [
            'tags' => $this->searchTags($searchTerm, $tagLimit),
            'videos' => $this->searchVideos($searchTerm, $videoLimit),
            'creators' => $this->searchCreators($searchTerm, $userLimit, $isSearchPage, $followedUserIds),
        ];
    }

    private function searchTags(?string $searchTerm, int $limit)
    {
        $query = $searchTerm
            ? DB::table('tags')
                ->where('name', 'like', $searchTerm)
                ->leftJoin('tag_video', 'tag_video.tag_id', '=', 'tags.id')
            : DB::table('tag_video')
                ->join('tags', 'tags.id', '=', 'tag_video.tag_id');

        return $query
            ->select('tags.id', 'tags.slug', 'tags.name', DB::raw('count(tag_video.id) as tag_count'))
            ->groupBy('tags.id')
            ->orderBy('tag_count', 'desc')
            ->limit($limit)
            ->get();
    }

    private function searchVideos(?string $searchTerm, int $limit)
    {
        $query = DB::table('videos')
            ->leftJoin('likes', 'likes.video_id', '=', 'videos.id')
            ->leftJoin('users', 'users.id', '=', 'videos.user_id')
            ->select(
                'videos.id',
                'videos.title',
                'videos.views',
                'videos.slug',
                'videos.preview',
                'users.username',
                DB::raw('count(likes.id) as likes_count')
            )
            ->groupBy('videos.id', 'users.username');

        if ($searchTerm) {
            $query->where('title', 'like', $searchTerm);
        } else {
            $query->orderBy('views', 'desc');
        }

        return $query->limit($limit)
            ->get()
            ->map(function ($item) {
                $item->preview = config('app.server_url') . $item->preview;
                return $item;
            });
    }

    private function searchCreators(
        ?string $searchTerm, int $limit,
        bool $isSearchPage, array $followedUserIds
    )
    {
        if ($isSearchPage) {
            $query = User::query()
                ->select('users.id', 'username', 'fullname', 'avatar')
                ->withCount(['followers', 'following', 'videos'])
                ->join('videos', 'users.id', '=', 'videos.user_id')
                ->selectRaw('COALESCE(SUM(videos.views), 0) as views')
                ->groupBy('users.id');

            if ($searchTerm) {
                $query->where('username', 'like', "%$searchTerm%");
            } else {
                $query->orderBy('views', 'desc');
            }

            return new UserGridCollection(
                $query->take(6)->get(),
                null,
                $followedUserIds
            );
        }

        return DB::table('users')
            ->when($searchTerm, fn($q) => $q->where('username', 'like', "%$searchTerm%"))
            ->leftJoin('followers', 'followers.user_id', '=', 'users.id')
            ->select('users.id', 'users.avatar', 'users.username', 'users.fullname', DB::raw('count(followers.id) as followers_count'))
            ->groupBy('users.id')
            ->limit($limit)
            ->get();
    }
}
