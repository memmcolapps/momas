<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix the broken timestamp defaults FIRST (raw SQL bypasses strict mode issues)
        DB::statement("
            ALTER TABLE users
            MODIFY created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");

        // Now safely add the new column
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('password_update_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password_update_count');
        });

        // Restore original (bad) defaults if you need to roll back
        DB::statement("
            ALTER TABLE users
            MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            MODIFY updated_at TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
        ");
    }
};
