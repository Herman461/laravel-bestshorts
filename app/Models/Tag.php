<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;


    public $timestamps = false;
    protected $hidden = ['pivot'];

    protected $fillable = ['name'];


    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = str()->slug($model->name);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = str()->slug($model->name);
            }
        });
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class);
    }

}
