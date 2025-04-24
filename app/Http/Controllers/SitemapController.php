<?php

namespace App\Http\Controllers;


use Illuminate\Routing\Controller;
use App\Models\User;

class SitemapController extends Controller
{
    public function index()
    {

        $users = User::query()->where('is_model', '1')->get();

        $formattedUsers = $users->map(function ($user) {
            return [
                'username' => $user->username,
                'modify' => $user->updated_at->toISOString(),
            ];
        });

        return response()->json(['users' => $formattedUsers]);
    }
}
