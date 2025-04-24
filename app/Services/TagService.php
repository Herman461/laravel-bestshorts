<?php


namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagService
{
    public function getTrendingTags(int $limit): Collection
    {
        return DB::table('tag_video')
            ->select('tags.id', 'tags.slug', 'tags.name', DB::raw('count(*) as tag_count'))
            ->join('tags', 'tags.id', '=', 'tag_video.tag_id')
            ->groupBy('tag_id')
            ->orderBy('tag_count', 'desc')
            ->take($limit)
            ->get();
    }

    public function getAllTags(): Collection
    {
        return DB::table('tag_video')
            ->select('tag_id', 'tags.slug', 'tags.name', DB::raw('count(*) as tag_count'))
            ->join('tags', 'tags.id', '=', 'tag_video.tag_id')
            ->groupBy('tag_id', 'tags.slug', 'tags.name')
            ->orderBy('tags.name')
            ->get();
    }

    public function searchTags(?string $searchTerm): Collection
    {
        $searchTerm = '%' . ($searchTerm ?? '') . '%';

        return DB::table('tags')
            ->where('name', 'like', $searchTerm)
            ->leftJoin('tag_video', 'tag_video.tag_id', '=', 'tags.id')
            ->select('tags.id', 'tags.name', 'tags.slug', DB::raw('count(tag_video.id) as tag_count'))
            ->groupBy('tags.id')
            ->limit(10)
            ->get();
    }
}
