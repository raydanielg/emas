<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_school_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('form')->nullable(); // if user limited to a specific class
            $table->timestamps();
            $table->unique(['user_id','school_id','form']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_school_assignments');
    }
};
