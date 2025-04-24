<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoGridCollection;
use App\Http\Resources\VideoResource;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VideoController extends Controller
{
    public function __construct(
        private readonly VideoService $videoService
    ) {
        $this->middleware('auth:api', ['except' => [
            'getVideoInfo',
            'getVideo',
            'getRandomShorts',
            'getCategoryShorts',
            'incrementViews'
        ]]);
    }

    public function getRandomShorts(Request $request): VideoCollection
    {
        $videos = $this->videoService->getRandomVideos(
            $request->input('seed'),
            $request->input('page', 1)
        );

        return new VideoCollection($videos);
    }

    public function getVideoInfo(string $slug): JsonResponse
    {
        $video = $this->videoService->getVideoDetails($slug);

        return ApiResponse::success(
            'video',
            'info_retrieved',
            ['video' => $video]
        );
    }

    public function getVideo(string $slug): VideoResource|JsonResponse
    {
        $video = $this->videoService->getVideoBySlug($slug);
        return new VideoResource($video);
    }

    public function delete(string $slug): JsonResponse
    {
        $this->videoService->deleteVideo(
            $slug,
            auth()->id()
        );

        return ApiResponse::success(
            'video',
            'deleted_successfully'
        );
    }

    public function incrementViews(string $slug): JsonResponse
    {
        $this->videoService->incrementVideoViews($slug);
        return ApiResponse::success('video', 'views_incremented');
    }

    public function getCommentsByVideoId(Request $request, string $slug): CommentCollection|JsonResponse
    {
        $comments = $this->videoService->getVideoComments(
            $slug,
            $request->input('page', 1)
        );

        return new CommentCollection($comments);
    }

    public function setLike(string $slug): JsonResponse
    {
        $action = $this->videoService->toggleVideoLike(
            $slug,
            auth()->id()
        );

        return ApiResponse::success(
            'video',
            $action === 'liked' ? 'video_liked' : 'video_unliked'
        );
    }

    public function getCategoryShorts(VideoRequest $request): VideoCollection|VideoGridCollection|JsonResponse
    {
        $result = $this->videoService->getSortedVideos(
            $request->input('sort'),
            $request->input('type', 'grid'),
            $request->input('page', 1),
            $request->input('seed', time()),
            $request->input('username'),
            $request->input('tag')
        );

        if ($result['error'] ?? false) {
            return ApiResponse::error('video', $result['error'], $result['code']);
        }

        return $request->input('type', 'grid') === 'feed'
            ? new VideoCollection($result['data'])
            : new VideoGridCollection($result['data']);
    }
}
