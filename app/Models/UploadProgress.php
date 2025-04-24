<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadProgress extends Model
{
    use HasFactory;

    protected $fillable = ['progress', 'session_id'];
}
