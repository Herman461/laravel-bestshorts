<?php
//
//namespace App\Http\Controllers;
//
//use App\Http\Requests\UserRegisterRequest;
//use App\Models\User;
//use Illuminate\Auth\Events\Registered;
//use Illuminate\Http\JsonResponse;
//use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Str;
//use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

//class AuthController extends Controller
//{
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login','register']]);
//    }
//
//    private function isEmail($value): bool
//    {
//        return preg_match(
//                '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
//                strtolower(trim($value))
//            ) === 1;
//    }
//
//    public function login(Request $request)
//    {
//        $credentials = $request->only('username_or_email', 'password');
//
//        // Проверяем, является ли входным значением email или username
//        if (self::isEmail($credentials['username_or_email'])) {
//            $credentials['email'] = $credentials['username_or_email'];
//            unset($credentials['username_or_email']);
//        } else {
//            $credentials['username'] = $credentials['username_or_email'];
//            unset($credentials['username_or_email']);
//        }
//
//        try {
//            $token = Auth::attempt($credentials, true);
//            if (!$token) {
//                return response()->json(['error' => 'invalid_credentials'], 401);
//            }
//        } catch (JWTException $e) {
//            return response()->json(['error' => 'could_not_create_token'], 500);
//        }
//        $user = Auth::user();
//        return response()->json([
//            'status' => 'success',
//            'message' => 'Successfully login!',
//            'data' => [
//                'user' => $user,
//                'auth' => [
//                    'token' => $token,
//                    'type' => 'Bearer',
//                ]
//            ]
//        ]);
//    }
//    public function register (UserRegisterRequest $request): JsonResponse
//    {
//        $user = User::query()->create([
//            'username' => Str::slug($request->username),
//            'email' => $request->email,
//            'password' => Hash::make($request->password),
//        ]);
//
//        $token = Auth::login($user, true);
//
//        event(new Registered($user));
//
//        $user->playlists()->create([
//            'name' => 'Watch later',
//        ]);
//
//        return response()->json([
//            'status' => 'success',
//            'message' => 'Account created successfully!',
//            'data' => [
//                'user' => $user,
//                'auth' => [
//                    'token' => $token,
//                    'type' => 'Bearer',
//                ]
//            ]
//
//        ]);
//    }
//
//    public function verify(Request $request)
//    {
//        $request->user()->sendEmailVerificationNotification();
//    }
//
//    public function logout(): JsonResponse
//    {
//        Auth::logout();
//        return response()->json([
//            'status' => 'success',
//            'message' => 'Successfully logged out',
//        ]);
//    }
//}


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
