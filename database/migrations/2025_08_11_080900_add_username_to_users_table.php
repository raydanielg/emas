<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
        });
    }
    public function down(): void
    {
        // Keep column (non-destructive)
    }
};
