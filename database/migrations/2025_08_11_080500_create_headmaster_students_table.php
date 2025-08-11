<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('headmaster_students')) {
            Schema::create('headmaster_students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // owner headmaster
                $table->string('full_name');
                $table->string('admission_no')->nullable();
                $table->string('form_level')->nullable(); // e.g., Form 1, Form 2
                $table->string('stream')->nullable();
                $table->string('gender', 10)->nullable();
                $table->date('dob')->nullable();
                $table->timestamps();
                $table->index(['user_id','form_level']);
            });
        }
    }

    public function down(): void
    {
        // Non-destructive
    }
};
