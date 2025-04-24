<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteVideoRequest;
use App\Http\Resources\VideoCollection;

use App\Http\Responses\ApiResponse;
use App\Services\ManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ManagerController extends Controller
{
    public function __construct(
        private readonly ManagerService $managerService
    )
    {
        $this->middleware('auth:api');
    }

    public function delete(DeleteVideoRequest $request): JsonResponse
    {
        $this->managerService->deleteUserVideo(
            auth()->user(),
            $request->id
        );

        return ApiResponse::success(
            'manager',
            'video_deleted',
            ['id' => $request->id]
        );
    }
}
