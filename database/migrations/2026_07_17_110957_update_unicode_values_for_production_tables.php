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
            $hasCreatedAt = Schema::hasColumn($tableName, 'created_at');
            $hasUpdatedAt = Schema::hasColumn($tableName, 'updated_at');

            if ($hasCreatedAt || $hasUpdatedAt) {
                $modifications = [];
                if ($hasCreatedAt) {
                    $modifications[] = "MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL";
                }
                if ($hasUpdatedAt) {
                    $modifications[] = "MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL";
                }

                DB::statement("
                    ALTER TABLE `{$tableName}`
                    " . implode(', ', $modifications) . "
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
