<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\UserGridCollection;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchService $searchService
    ) {}

    public function __invoke(SearchRequest $request): JsonResponse
    {
        $results = $this->searchService->search(
            $request->search,
            $request->input('video_limit', 10),
            $request->input('user_limit', 10),
            $request->input('tag_limit', 15),
            $request->input('isSearchPage', false),
            auth()->user()
        );

        return ApiResponse::success(
            'search',
            'results_retrieved',
            $results
        );
    }
}
