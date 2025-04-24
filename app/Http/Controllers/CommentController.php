<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Video;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    )
    {
        $this->middleware('auth:api', ['except' => ['getAll']]);
    }

    public function create(CommentRequest $request): JsonResponse
    {
        $comment = $this->commentService->createComment(
            auth()->id(),
            $request->video_id,
            $request->comment
        );

        return ApiResponse::success(
            'comments',
            'created',
            ['comment' => $comment],
            201
        );
    }

    public function getAll(Video $video): JsonResponse
    {
        $comments = $this->commentService->getVideoComments($video);

        return ApiResponse::success(
            'comments',
            'retrieved',
            ['comments' => $comments]
        );
    }
}
