<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $users = User::factory(10)->create();
        $videos = Video::factory(200)->create();
        $tags = Tag::factory(50)->create();

        $videos->each(function ($video) use ($tags) {
            $video->tags()->attach($tags->random(random_int(1, 20))->pluck('id')->toArray());
        });

        $users->each(function ($user) {
            // У пользователей по умолчанию есть плейлист Watch later
            $playlist = Playlist::query()->make(['name' => 'Watch later']);
            $user->playlists()->save($playlist);

            // Установка подписчиков
            $followers = User::query()
                ->where('id', '!=', $user->id)
                ->inRandomOrder()
                ->take(random_int(1, 8))
                ->get()
                ->map(function ($follower) {
                    return $follower->id;
                });

            $user->followers()->attach($followers);
        });
    }
}
