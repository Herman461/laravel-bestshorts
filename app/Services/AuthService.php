<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials): array
    {
        $credentials = $this->normalizeCredentials($credentials);

        if (!$token = auth()->attempt($credentials, true)) {
            return ['error' => 'invalid_credentials', 'code' => 401];
        }

        return [
            'data' => [
                'user' => auth()->user(),
                'auth' => [
                    'token' => $token,
                    'type' => 'Bearer',
                ]
            ]
        ];
    }

    public function register(array $data): array
    {
        $user = User::query()->create([
            'avatar' => null,
            'username' => str()->slug($data['username']),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = auth()->login($user, true);

        $user->playlists()->create(['name' => 'Watch later']);

        return [
            'user' => $user,
            'auth' => [
                'token' => $token,
                'type' => 'Bearer',
            ]
        ];
    }


    public function logout(): void
    {
        auth()->logout();
    }

    private function normalizeCredentials(array $credentials): array
    {
        $key = $credentials['username_or_email'];
        unset($credentials['username_or_email']);

        $field = $this->isEmail($key) ? 'email' : 'username';
        $credentials[$field] = $key;

        return $credentials;
    }

    private function isEmail(string $value): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            strtolower(trim($value))) === 1;
    }
}
