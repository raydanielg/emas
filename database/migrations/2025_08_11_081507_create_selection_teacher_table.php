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
        Schema::create('selection_teacher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('selection_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('status', 20)->default('selected'); // selected, rejected, pending
            $table->string('assigned_as', 100)->nullable(); // e.g., Mathematics Marker, Chief Invigilator, etc
            $table->string('role', 50)->nullable(); // marker or enterer
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['selection_id']);
            $table->index(['teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selection_teacher');
    }
};
