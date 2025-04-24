<?php


namespace App\Services;

use App\Models\Report;

class ReportService
{
    public function createReport(?int $userId, string $message, string $videoSlug): void
    {
        Report::query()->create([
            'user_slug' => $userId,
            'message' => $message,
            'video_slug' => $videoSlug
        ]);
    }
}
