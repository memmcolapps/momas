<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('estates', 'bank_id')) {
            Schema::table('estates', function (Blueprint $table) {
                $table->integer('bank_id')->nullable()->after('bank');
            });
        }
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
        });
    }
};
