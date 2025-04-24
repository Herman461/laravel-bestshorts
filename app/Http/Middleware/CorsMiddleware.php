<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
//    public function handle(Request $request, Closure $next): Response
//    {
//        if ($request->getMethod() === "OPTIONS") {
//            return response()->json([], 200)
//                ->header('Access-Control-Allow-Origin', env('CLIENT_URL'))
//                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
//        }
//
//        return $next($request)
//            ->header('Access-Control-Allow-Origin', env('CLIENT_URL')) // Укажите ваш фронтенд домен
//            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
//    }
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            env('CLIENT_URL'),
            'https://www.xbestshorts.com',
            'https://testik.xbestshorts.com'
        ];

        $origin = $request->headers->get('Origin');

        if ($request->getMethod() === "OPTIONS") {
            return response()->json([], 200)
                ->header('Access-Control-Allow-Origin', in_array($origin, $allowedOrigins) ? $origin : null)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        $response = $next($request);
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }

        return $response
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
