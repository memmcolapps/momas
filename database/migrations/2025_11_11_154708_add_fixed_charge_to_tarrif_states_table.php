<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tarrif_states', function (Blueprint $table) {
            $table->double('fixed_charge', 14, 2)->default(0.00)->after('vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarrif_states', function (Blueprint $table) {
            $table->dropColumn('fixed_charge');
        });
    }
};
