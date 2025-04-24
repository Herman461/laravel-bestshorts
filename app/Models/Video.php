<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'views', 'filename', 'preview', 'fullpath', 'slug'];
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $appends = ['preview', 'fullpath'];

    public function getPreviewAttribute()
    {
        return env('SERVER_URL') . $this->attributes['preview'];
    }
    public function getFullpathAttribute()
    {
        return env('SERVER_URL') . $this->attributes['fullpath'];
    }

    public function generateSlug()
    {
        // Получаем все slugs тегов в одном запросе
        $tags = $this->tags()->pluck('slug')->take(3)->toArray();

        // Генерируем начальный slug
        $slug = implode('-', $tags);

        // Уникальность slug
        return $this->makeUniqueSlug($slug);
    }

    // Оптимизированный метод для проверки уникальности slug
    protected function makeUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 1;

        $existingSlugs = self::where('slug', 'LIKE', $originalSlug . '%')->pluck('slug')->toArray();


        while (in_array($slug, $existingSlugs)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    protected $hidden = ['pivot'];

//    protected static function boot() {
//        parent::boot();
//
//        static::creating(function ($video) {
//            $video->slug = str()->slug($video->title);
//        });
//    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function isAuthUserLikedPost(){
        return $this->likes()->where('user_id', '=',  auth()->id())->exists();
    }

    public function isAuthUserCommentedPost(){
        return $this->comments()->where('user_id', '=', auth()->id())->exists();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class)->withTimestamps();
    }
}
