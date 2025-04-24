<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRequest;
use App\Http\Resources\VideoGridCollection;
use App\Services\FeedService;
use Illuminate\Routing\Controller;

class FeedController extends Controller
{
    public function __construct(
        private readonly FeedService $feedService
    )
    {}

    public function getVideos(FeedRequest $request): VideoGridCollection
    {
        $videos = $this->feedService->getVideos(
            $request->sort,
            $request->input('page', 1)
        );

        return new VideoGridCollection($videos);
    }
}
