<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Playlist;
use App\Models\Video;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('playlist_video', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Playlist::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Video::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->index('playlist_id');
            $table->index('video_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_video');
    }
};
