<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Playlist>
 */
class PlaylistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $table = "playlists";

    public function definition(): array
    {
        $name = fake()->sentence(5, true);

        return [
            'name' => 'Watch later',
            'slug' => 'watch-later',
            'user_id' => 2
        ];
    }
}
