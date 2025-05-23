<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->index();
            $table->string('filename');
            $table->string('fullpath');
            $table->string('title')->nullable()->index();
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('preview');
            $table->unsignedBigInteger('views')->default(0)->index();

            $table->index('created_at');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
