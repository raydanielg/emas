<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_settings')) {
            Schema::create('user_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('key', 100);
                $table->text('value')->nullable();
                $table->timestamps();
                $table->unique(['user_id','key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
