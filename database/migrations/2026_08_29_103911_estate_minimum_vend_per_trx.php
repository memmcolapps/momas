<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->boolean('minimum_vend_per_transaction')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropColumn('minimum_vend_per_transaction');
        });
    }
};
