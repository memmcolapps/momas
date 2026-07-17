<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();

        $tables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
            AND TABLE_TYPE = 'BASE TABLE'
        ", [$database]);

        foreach ($tables as $table) {

            $tableName = $table->TABLE_NAME;

            if (Schema::hasColumn($tableName, 'created_at')) {
                DB::statement("
                    ALTER TABLE `{$tableName}`
                    MODIFY `created_at`
                    TIMESTAMP NULL DEFAULT NULL
                ");
            }

            if (Schema::hasColumn($tableName, 'updated_at')) {
                DB::statement("
                    ALTER TABLE `{$tableName}`
                    MODIFY `updated_at`
                    TIMESTAMP NULL DEFAULT NULL
                ");
            }

            DB::statement("
                ALTER TABLE `{$tableName}`
                CONVERT TO CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci
            ");
        }
    }

    public function down(): void
    {
        //
    }
};
