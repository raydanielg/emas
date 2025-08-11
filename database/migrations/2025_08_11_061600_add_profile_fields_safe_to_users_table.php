<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','phone')) { $table->string('phone')->nullable()->after('email'); }
            if (!Schema::hasColumn('users','date_of_birth')) { $table->date('date_of_birth')->nullable()->after('phone'); }
            if (!Schema::hasColumn('users','bank_number')) { $table->string('bank_number')->nullable()->after('date_of_birth'); }
            if (!Schema::hasColumn('users','role')) { $table->string('role')->default('enterer')->after('bank_number'); }
            if (!Schema::hasColumn('users','avatar_path')) { $table->string('avatar_path')->nullable()->after('role'); }
            if (!Schema::hasColumn('users','institution')) { $table->string('institution')->nullable()->after('avatar_path'); }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users','institution')) { $table->dropColumn('institution'); }
            if (Schema::hasColumn('users','avatar_path')) { $table->dropColumn('avatar_path'); }
            if (Schema::hasColumn('users','role')) { $table->dropColumn('role'); }
            if (Schema::hasColumn('users','bank_number')) { $table->dropColumn('bank_number'); }
            if (Schema::hasColumn('users','date_of_birth')) { $table->dropColumn('date_of_birth'); }
            if (Schema::hasColumn('users','phone')) { $table->dropColumn('phone'); }
        });
    }
};
