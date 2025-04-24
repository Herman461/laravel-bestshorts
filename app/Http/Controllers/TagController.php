<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrendingTagsRequest;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    public function __construct(
        private readonly TagService $tagService
    ) {}

    public function getTrendingTags(TrendingTagsRequest $request): JsonResponse
    {
        return response()->json(
            $this->tagService->getTrendingTags($request->limit)
        );
    }

    public function getTags(Request $request): JsonResponse
    {
        return response()->json(
            $this->tagService->getAllTags()
        );
    }

    public function search(Request $request): JsonResponse
    {
        return response()->json([
            'tags' => $this->tagService->searchTags($request->search)
        ]);
    }
}
