<?php


namespace App\Services;


use App\Models\Tag;
use App\Models\Video;

use Illuminate\Support\Facades\DB;

class VideoService
{
    public function getRandomVideos(?string $seed, int $page)
    {
        return Video::query()
            ->orderBy(DB::raw("RAND($seed)"))
            ->paginate(18, ['*'], 'page', $page);
    }

    public function getVideoDetails(string $slug)
    {
        return Video::with(['tags', 'user'])
            ->withCount('likes')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function getVideoBySlug(string $slug)
    {
        return Video::query()->where('slug', $slug)->firstOrFail();
    }

    public function deleteVideo(string $slug, int $userId): void
    {
        $video = Video::query()->where('slug', $slug)->firstOrFail();

        if ($userId !== $video->user_id) {
            throw new \Exception(trans('video.errors.insufficient_permissions'), 403);
        }

        $video->delete();
    }

    public function incrementVideoViews(string $slug): void
    {
        Video::query()->where('slug', $slug)->firstOrFail()->increment('views');
    }

    public function getVideoComments(string $slug, int $page)
    {
        return Video::query()->where('slug', $slug)
            ->firstOrFail()
            ->comments()
            ->paginate(3, ['*'], 'page', $page);
    }

    public function toggleVideoLike(string $slug, int $userId): string
    {
        $video = Video::query()->where('slug', $slug)->firstOrFail();
        $like = $video->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return 'unliked';
        }

        $video->likes()->create(['user_id' => $userId]);
        return 'liked';
    }

    public function getSortedVideos(
        ?string $sort,
        string  $type,
        int     $page,
        ?string $seed,
        ?string $username,
        ?string $tag
    ): array
    {
        $query = Video::with(['tags', 'user'])
            ->withCount('likes');

        if ($username) {
            $query->whereHas('user', fn($q) => $q->where('username', $username));
        }

        if ($tag) {
            if (!Tag::query()->where('slug', $tag)->exists()) {
                return [
                    'error' => 'tag_not_found',
                    'code' => 404
                ];
            }
            $query->whereHas('tags', fn($q) => $q->where('slug', $tag));
        }

        $this->applySorting($query, $sort, $seed);

        return [
            'data' => $query->paginate(18, ['*'], 'page', $page)
        ];
    }

    private function applySorting($query, ?string $sort, ?string $seed): void
    {
        match ($sort) {
            'newly-released' => $query->orderBy('created_at', 'desc'),
            'top-this-month' => $query->where('created_at', '>=', now()->subDays(30))
                ->orderByRaw("RAND($seed)"),
            'most-viewed' => $query->orderBy('views', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderByRaw("RAND($seed)"),
        };
    }
}
