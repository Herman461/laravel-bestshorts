<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_slug', 'video_slug', 'message'
    ];
//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
//
//    public function video(): BelongsTo
//    {
//        return $this->belongsTo(Video::class);
//    }
}
