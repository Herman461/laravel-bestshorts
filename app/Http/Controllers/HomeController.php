<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Services\HomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->homeService->getHomeData(auth()->user());

        return ApiResponse::success(
            file: 'home',
            messageKey: 'data_loaded',
            data: $data
        );
    }
}
