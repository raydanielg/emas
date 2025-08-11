<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('support_messages')) {
            Schema::create('support_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('role', 20)->default('user');
                $table->text('message')->nullable();
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->string('attachment_mime')->nullable();
                $table->unsignedBigInteger('attachment_size')->nullable();
                $table->string('attachment_type', 20)->nullable(); // image, audio, file
                $table->timestamps();
                $table->index(['user_id','created_at']);
            });
        } else {
            Schema::table('support_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('support_messages','role')) $table->string('role', 20)->default('user')->after('user_id');
                if (!Schema::hasColumn('support_messages','message')) $table->text('message')->nullable()->after('role');
                if (!Schema::hasColumn('support_messages','attachment_path')) $table->string('attachment_path')->nullable()->after('message');
                if (!Schema::hasColumn('support_messages','attachment_name')) $table->string('attachment_name')->nullable()->after('attachment_path');
                if (!Schema::hasColumn('support_messages','attachment_mime')) $table->string('attachment_mime')->nullable()->after('attachment_name');
                if (!Schema::hasColumn('support_messages','attachment_size')) $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_mime');
                if (!Schema::hasColumn('support_messages','attachment_type')) $table->string('attachment_type', 20)->nullable()->after('attachment_size');
            });
        }
    }

    public function down(): void
    {
        // Non-destructive: do not drop table by default
    }
};
