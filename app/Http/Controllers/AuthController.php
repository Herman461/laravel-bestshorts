<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    )
    {
        $this->middleware('auth:api', ['only' => ['logout']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('username_or_email', 'password');
        $result = $this->authService->login($credentials);

        return isset($result['error'])
            ? ApiResponse::error('auth', $result['error'], $result['code'])
            : ApiResponse::success('auth', 'login', $result['data']);
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        return ApiResponse::success('auth', 'register', $result);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return ApiResponse::success('auth', 'logout');
    }
}
