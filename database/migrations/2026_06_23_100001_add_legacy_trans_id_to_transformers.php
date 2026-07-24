<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transformers', function (Blueprint $table) {
            $table->string('legacy_trans_id', 100)->nullable()->after('id');
            $table->index('legacy_trans_id');
        });
    }

    public function down(): void
    {
        Schema::table('transformers', function (Blueprint $table) {
            $table->dropIndex(['legacy_trans_id']);
            $table->dropColumn('legacy_trans_id');
        });
    }
};
