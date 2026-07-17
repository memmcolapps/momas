<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $database = DB::getDatabaseName();

        DB::statement(
            "ALTER DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        $tables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
              AND TABLE_TYPE = 'BASE TABLE'
        ", [$database]);

        foreach ($tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table->TABLE_NAME}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }
    }

    public function down(): void
    {
        $database = DB::getDatabaseName();

        DB::statement(
            "ALTER DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
        );

        $tables = DB::select("
            SELECT TABLE_NAME
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
              AND TABLE_TYPE = 'BASE TABLE'
        ", [$database]);

        foreach ($tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table->TABLE_NAME}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
            );
        }
    }
};
