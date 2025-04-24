<?php


namespace App\Services;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedService
{
    public function getVideos(string $sort, int $page): LengthAwarePaginator
    {
        $query = Video::query()->withCount('likes');

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'trending':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(
            perPage: 12,
            page: $page
        );
    }
}
