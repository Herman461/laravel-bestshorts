<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function __invoke(ReportRequest $request): JsonResponse
    {
        $this->reportService->createReport(
            userId: auth()->id(),
            message: $request->message,
            videoSlug: $request->video_slug
        );

        return ApiResponse::success(
            'report',
            'report_sent',
        );
    }
}
