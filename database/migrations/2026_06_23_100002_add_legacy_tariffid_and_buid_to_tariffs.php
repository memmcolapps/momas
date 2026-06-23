<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix legacy timestamp first
        DB::statement("
            ALTER TABLE tariffs
            MODIFY updated_at TIMESTAMP NULL DEFAULT NULL
        ");

        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('legacy_tariffid', 100)->nullable()->after('id');
            $table->string('legacy_buid', 100)->nullable()->after('legacy_tariffid');
            $table->index('legacy_tariffid');
            $table->index('legacy_buid');
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropIndex(['legacy_tariffid']);
            $table->dropIndex(['legacy_buid']);
            $table->dropColumn(['legacy_tariffid', 'legacy_buid']);
        });
    }
};
