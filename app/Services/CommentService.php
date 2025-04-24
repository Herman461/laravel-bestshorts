<?php

namespace App\Services;



use App\Models\Comment;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    public function createComment(int $userId, int $videoId, string $comment): Builder|Model
    {
        return Comment::query()->create([
            'user_id' => $userId,
            'video_id' => $videoId,
            'comment' => $comment
        ]);
    }

    public function getVideoComments(Video $video): Collection
    {
        return Comment::query()->with('user:id,username,fullname,avatar')
            ->where('video_id', $video->id)
            ->latest()
            ->get();
    }
}
