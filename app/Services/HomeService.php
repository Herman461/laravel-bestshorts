<?php


namespace App\Services;

use App\Http\Resources\UserGridCollection;
use App\Http\Resources\VideoGridCollection;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeService
{
    public function getHomeData(?User $user): array
    {
        $followedUserIds = collect($user?->following())->pluck('users.id')->toArray() ?? [];

        return [
            'trending_creators' => $this->getTrendingCreators($followedUserIds),
            'trending_videos' => $this->getTrendingVideos(),
            'new_videos' => $this->getNewVideos(),
            'new_creators' => $this->getNewCreators($followedUserIds),
        ];
    }

    private function getTrendingVideos(): VideoGridCollection
    {
        $lastWeek = Carbon::now()->subDays(7);

        return new VideoGridCollection(
            Video::query()
                ->with(['tags', 'user'])
                ->withCount('likes')
                ->whereBetween('created_at', [$lastWeek, now()])
                ->orderBy('views', 'desc')
                ->take(6)
                ->get()
        );
    }

    private function getNewVideos(): VideoGridCollection
    {
        $lastWeek = Carbon::now()->subDays(7);
        $trendingVideoIds = collect($this->getTrendingVideos()->getData())->pluck('id')->toArray();

        return new VideoGridCollection(
            Video::query()
                ->with(['tags', 'user'])
                ->withCount('likes')
                ->whereBetween('created_at', [$lastWeek, now()])
                ->whereNotIn('id', $trendingVideoIds)
                ->inRandomOrder()
                ->take(6)
                ->get()
        );
    }

    private function getTrendingCreators(array $followedUserIds): UserGridCollection
    {
        return new UserGridCollection(
            User::query()
                ->select('users.id', 'username', 'fullname', 'avatar')
                ->withCount(['followers', 'following', 'videos'])
                ->join('videos', 'users.id', '=', 'videos.user_id')
                ->selectRaw('COALESCE(SUM(videos.views), 0) as views')
                ->groupBy('users.id')
                ->orderBy('views', 'desc')
                ->take(6)
                ->get(),
            null,
            $followedUserIds
        );
    }

    private function getNewCreators(array $followedUserIds): UserGridCollection
    {
        $lastThreeDays = Carbon::now()->subDays(3);
        $trendingCreatorIds = collect($this->getTrendingCreators($followedUserIds)->getData())->pluck('user.id')->toArray();

        return new UserGridCollection(
            User::query()
                ->withCount([
                    'videos as views' => fn($query) => $query->select(DB::raw('COALESCE(SUM(views), 0)')),
                    'followers', 'following', 'videos'
                ])
                ->whereBetween('created_at', [$lastThreeDays, now()])
                ->whereNotIn('id', $trendingCreatorIds)
                ->inRandomOrder()
                ->take(6)
                ->get(),
            null,
            $followedUserIds
        );
    }
}
