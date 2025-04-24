<?php

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
        Schema::table('socials', function (Blueprint $table) {
            // Удаляем ненужные столбцы
            $table->dropColumn(['tiktok']);

            // Добавляем новые столбцы
            $table->string('onlyfans')->nullable();
            $table->string('reddit')->nullable();
            $table->string('fansly')->nullable();
            $table->string('website')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('socials', function (Blueprint $table) {
            // Восстанавливаем удаленные столбцы
            $table->string('tiktok')->nullable();

            // Удаляем вновь добавленные столбцы
            $table->dropColumn(['reddit', 'onlyfans', 'website']);
        });
    }
};
