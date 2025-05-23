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

        $array = ['яблоко', 'банан', 'апельсин', 'виноград'];
        $randomKey = array_rand($array);

        return [
            'user_id' => mt_rand(1, 2),
            'views' => random_int(0, 1500),
            'title' => mt_rand(0, 1) ? self::$num . ' - ' . $title : null,
            'slug' => $slug,
            'preview' =>  $randomNum
                ? '/storage/videos/14-05-2025/4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08.webp'
                : '/storage/videos/14-05-2025/4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08.webp',
            'description' => mt_rand(0, 1) ? fake()->sentence(15, true) : null,
            'filename' => $randomNum
                ? '4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08'
                : '4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08',
            'fullpath' =>  $randomNum
                ? '/storage/videos/14-05-2025/4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08.m3u8'
                : '/storage/videos/14-05-2025/4620b53718e304930972acf53eeaf100910b5d08/4620b53718e304930972acf53eeaf100910b5d08.m3u8',
            'created_at' => fake()->dateTimeThisMonth()->format('Y-m-d H:i:s')
        ];
    }
}
