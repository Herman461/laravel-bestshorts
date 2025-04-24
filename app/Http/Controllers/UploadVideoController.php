<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoUploadRequest;
use App\Http\Responses\ApiResponse;
use App\Services\VideoUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UploadVideoController extends Controller
{
    public function __construct(
        private readonly VideoUploadService $uploadService
    ) {
        $this->middleware('auth:api');
    }

    public function upload(VideoUploadRequest $request): JsonResponse
    {
        try
        {
            $videoData = $this->uploadService->processUpload(
                $request->file('video'),
                $request->input('from_sec'),
                $request->input('to_sec'),
                $request->only(['title', 'description']),
                auth()->user()
            );

            return ApiResponse::success(
                'upload',
                'video_uploaded',
                $videoData
            );
        } catch (\InvalidArgumentException) {
            return ApiResponse::error(
                'upload',
                'duration_exceeded',
                422
            );
        }

    }
}
