<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->string('legacy_buid', 100)->nullable()->after('id');
            $table->index('legacy_buid');
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropIndex(['legacy_buid']);
            $table->dropColumn('legacy_buid');
        });
    }
};
