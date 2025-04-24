<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistCreateRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use App\Services\PlaylistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlaylistController extends Controller
{
    public function __construct(
        private readonly PlaylistService $playlistService
    ) {
        $this->middleware('auth:api');
    }

    public function create(PlaylistCreateRequest $request): JsonResponse
    {
        return $this->playlistService->createPlaylist(
            auth()->user(),
            $request->name
        );
    }

    public function update(PlaylistCreateRequest $request, string $slug): JsonResponse
    {
        return $this->playlistService->updatePlaylist(
            auth()->user(),
            $slug,
            $request->name
        );
    }

    public function delete(string $slug): JsonResponse
    {
        return $this->playlistService->deletePlaylist(
            auth()->user(),
            $slug
        );
    }

    public function addVideoToPlaylist(string $playlistId, string $slug): JsonResponse
    {
        return $this->playlistService->addVideoToPlaylist(
            auth()->user(),
            $playlistId,
            $slug
        );
    }

    public function deleteVideoFromPlaylist(string $playlistId, string $slug): JsonResponse
    {
        return $this->playlistService->deleteVideoFromPlaylist(
            auth()->user(),
            $playlistId,
            $slug
        );
    }



    public function deleteVideosFromPlaylist(Request $request, string $playlistId): JsonResponse
    {
        return $this->playlistService->deleteVideosFromPlaylist(
            auth()->user(),
            $playlistId,
            $request->input('videos')
        );
    }

    public function getVideos(Request $request, string $playlistSlug): VideoCollection
    {
        return $this->playlistService->getVideos(
            auth()->user(),
            $playlistSlug,
            $request->page
        );
    }

    public function getUserPlaylists(): JsonResponse
    {
        return $this->playlistService->getUserPlaylists(auth()->user());
    }

    public function getVideoPlaylists(Video $video): JsonResponse
    {
        return $this->playlistService->getVideoPlaylists(
            auth()->user(),
            $video
        );
    }

    public function getSinglePlaylist(string $playlist): JsonResponse
    {
        return $this->playlistService->getSinglePlaylist(
            auth()->user(),
            $playlist
        );
    }

}
