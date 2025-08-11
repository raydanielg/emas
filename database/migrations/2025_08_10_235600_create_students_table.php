<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('sex', ['M','F'])->nullable();
            $table->unsignedTinyInteger('form')->nullable(); // 1..6 etc
            $table->string('exam_number')->nullable();
            $table->boolean('admitted')->default(true);
            $table->timestamps();
            $table->index(['school_id','form']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('students');
    }
};
