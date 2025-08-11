<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('school_result_reports')) {
            Schema::create('school_result_reports', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('exam')->nullable(); // e.g., MOCK, PRE, NECTA
                $table->string('term')->nullable(); // e.g., Term I, II
                $table->string('year', 4)->nullable();
                $table->string('school_code')->index(); // scope per school
                $table->string('pdf_path'); // storage public disk relative path
                $table->string('status')->default('ready'); // pending|ready|published
                $table->json('meta')->nullable(); // extra fields if needed later
                $table->timestamps();

                $table->index(['year','exam']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_result_reports');
    }
};
