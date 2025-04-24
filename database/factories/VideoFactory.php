<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    public static $num = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$num += 1;
//        $files = Storage::disk('public')->allFiles("videos");
//        $randomFile = $files[rand(0, count($files) - 1)];
        $title = fake()->sentence(4, true);
        $slug = str()->slug($title);
        $randomNum = mt_rand(0, 1);
        return [
            'user_id' => mt_rand(1, 2),
            'views' => random_int(0, 1500),
            'title' => mt_rand(0, 1) ? self::$num . ' - ' . $title : null,
            'slug' => $slug,
            'preview' =>  $randomNum
                ? '/storage/videos/19-02-2025/dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5.webp'
                : '/storage/videos/19-02-2025/dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5.webp',
            'description' => mt_rand(0, 1) ? fake()->sentence(15, true) : null,
            'filename' => $randomNum
                ? 'dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5'
                : 'dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5',
            'fullpath' =>  $randomNum
                ? '/storage/videos/19-02-2025/dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5.m3u8'
                : '/storage/videos/19-02-2025/dabaa1bd2392f88f23d69886b9946fb1f6296bb5/dabaa1bd2392f88f23d69886b9946fb1f6296bb5.m3u8',
            'created_at' => fake()->dateTimeThisMonth()->format('Y-m-d H:i:s')
        ];
    }
}
