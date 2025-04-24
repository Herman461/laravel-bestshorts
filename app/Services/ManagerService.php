<?php

namespace App\Services;

use App\Models\User;

class ManagerService
{

    public function deleteUserVideo(User $user, int $videoId): void
    {
        $video = $user->videos()
            ->where('id', $videoId)
            ->firstOrFail();

        $video->delete();
    }
}
