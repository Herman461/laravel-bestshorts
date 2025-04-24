<?php

namespace App\Services;

use App\Http\Resources\VideoCollection;
use App\Http\Responses\ApiResponse;
use App\Models\Playlist;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class PlaylistService
{
    public function createPlaylist(User $user, string $name): JsonResponse
    {
        $slug = str()->slug($name);

        if ($user->playlists()->where('slug', $slug)->exists()) {
            return ApiResponse::error(
                'playlists',
                'unique_name_required',
                422
            );
        }

        if ($user->playlists()->count() >= 12) {
            return ApiResponse::error(
                'playlists',
                'max_playlists_reached',
                422
            );
        }

        $playlist = $user->playlists()->create([
            'name' => $name,
            'slug' => $slug
        ]);

        return ApiResponse::success(
            'playlists',
            'created',
            ['playlist' => $playlist],
            201
        );
    }

    public function updatePlaylist(User $user, string $slug, string $newName): JsonResponse
    {
        $playlist = $this->findUserPlaylist($user, $slug);

        if (!$playlist) {
            return $this->notFoundResponse();
        }

        $newSlug = str()->slug($newName);

        if ($playlist->slug === $newSlug) {
            return ApiResponse::error(
                'playlists',
                'unique_name_required',
                422
            );
        }

        $playlist->update(['name' => $newName, 'slug' => $newSlug]);

        return ApiResponse::success(
            'playlists',
            'updated',
            ['playlist' => $playlist]
        );
    }

    public function deletePlaylist(User $user, string $slug): JsonResponse
    {
        $playlist = $this->findUserPlaylist($user, $slug);

        if (!$playlist) {
            return $this->notFoundResponse();
        }

        $playlist->delete();

        return ApiResponse::success(
            'playlists',
            'deleted',
            ['playlist' => $playlist]
        );
    }

    public function addVideoToPlaylist(User $user, string $playlistSlug, string $videoSlug): JsonResponse
    {
        $playlist = Playlist::query()->where('slug', $playlistSlug)->first();
        $video = Video::query()->where('slug', $videoSlug)->first();

        if (!$playlist || !$video) {
            return $this->notFoundResponse();
        }

        if ($playlist->user_id !== $user->id) {
            return $this->forbiddenResponse();
        }

        $playlist->videos()->attach($video->id);

        return ApiResponse::success(
            'playlists',
            'video_added',
            ['video' => $video]
        );
    }

    public function deleteVideoFromPlaylist(User $user, string $playlistSlug, string $videoSlug): JsonResponse
    {
        $playlist = $this->findUserPlaylist($user, $playlistSlug);
        $video = $this->findVideoBySlug($videoSlug);

        if (!$playlist || !$video) {
            return $this->notFoundResponse();
        }

        if ($playlist->user_id !== $user->id) {
            return $this->forbiddenResponse();
        }

        if (!$playlist->videos()->where('video_id', $video->id)->exists()) {
            return ApiResponse::error(
                'playlists',
                'video_not_in_playlist',
                404
            );
        }

        $playlist->videos()->detach($video->id);

        return ApiResponse::success(
            'playlists',
            'video_removed'
        );
    }

    public function deleteVideosFromPlaylist(User $user, string $playlistSlug, array $videoIds): JsonResponse
    {
        $playlist = $this->findUserPlaylist($user, $playlistSlug);

        if (!$playlist) {
            return $this->notFoundResponse();
        }

        if ($playlist->user_id !== $user->id) {
            return $this->forbiddenResponse();
        }

        $playlist->videos()->detach($videoIds);

        return ApiResponse::success(
            'playlists',
            'videos_removed'
        );
    }


    public function getVideos(User $user, string $playlistSlug, int $page): VideoCollection
    {
        $playlist = $this->findUserPlaylist($user, $playlistSlug);

        if (!$playlist) {
            abort(404);
        }

        $videos = $playlist->videos()->paginate(
            perPage: 18,
            page: $page
        );

        $totalPages = $videos->lastPage();

        return new VideoCollection($videos, $totalPages);
    }

    public function getUserPlaylists(User $user): JsonResponse
    {
        $playlists = $user->playlists;
        $lastVideos = $this->getLastVideosForPlaylists($user->id);

        $playlists->each(function ($playlist) use ($lastVideos) {
            $lastVideo = $lastVideos->firstWhere('playlist_id', $playlist->id);
            if ($lastVideo) {
                $playlist->preview = env('SERVER_URL') . $lastVideo->preview;
            }
        });

        return ApiResponse::success(
            'playlists',
            'retrieved',
            ['playlists' => $playlists]
        );
    }

    public function getVideoPlaylists(User $user, Video $video): JsonResponse
    {
        $playlists = DB::select(
            "SELECT playlists.id FROM playlists
            JOIN playlist_video ON playlists.id = playlist_video.playlist_id
            WHERE playlists.user_id = :userId AND playlist_video.video_id = :videoId",
            ['userId' => $user->id, 'videoId' => $video->id]
        );

        return ApiResponse::success(
            'playlists',
            'retrieved',
            ['playlists' => $playlists]
        );
    }


    private function findUserPlaylist(User $user, string $slug): Model|null
    {
        return $user->playlists()->where('slug', $slug)->first();
    }

    private function notFoundResponse(): JsonResponse
    {
        return ApiResponse::error(
            'playlists',
            'playlist_not_found',
            404
        );
    }

    private function forbiddenResponse(): JsonResponse
    {
        return ApiResponse::error(
            'playlists',
            'forbidden',
            403
        );
    }

    private function findVideoBySlug(string $slug): ?Video
    {
        return Video::query()->where('slug', $slug)->first();
    }

    private function getLastVideosForPlaylists(int $userId)
    {
        return DB::select(
            "SELECT v.preview, x.name, x.playlist_id
             FROM (SELECT max.name, data.*
                   FROM playlist_video AS data
                   JOIN (
                       SELECT p.id, playlist_id, p.name, MAX(playlist_video.created_at) AS created_at
                       FROM playlist_video
                       JOIN playlists p ON playlist_video.playlist_id = p.id
                       WHERE p.user_id = ?
                       GROUP BY playlist_id
                   ) AS max USING (playlist_id, created_at)) as x
             LEFT JOIN videos v ON x.video_id = v.id",
            [$userId]
        );
    }


    public function getSinglePlaylist(User $user, string $slug): JsonResponse
    {
        $playlist = $user->playlists()
            ->withCount('videos')
            ->where('slug', $slug)
            ->first();

        if (!$playlist) {
            return $this->notFoundResponse();
        }

        return ApiResponse::success(
            'playlists',
            'retrieved',
            ['playlist' => $playlist]
        );
    }

}
