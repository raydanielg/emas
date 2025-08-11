<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_subject_assignments')) {
            Schema::create('user_subject_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('subject_id');
                $table->timestamps();

                $table->unique(['user_id','school_id','subject_id'], 'usa_unique');
                $table->index(['school_id','subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subject_assignments');
    }
};
