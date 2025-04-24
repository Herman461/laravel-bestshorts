<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($playlist) {
            $playlist->slug = str()->slug($playlist->name);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class)->withTimestamps();
    }
}
