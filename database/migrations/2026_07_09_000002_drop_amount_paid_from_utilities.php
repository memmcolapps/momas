<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            if (Schema::hasColumn('utilities', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            $table->decimal('amount_paid', 14, 2)->nullable()->after('operator_id');
        });
    }
};
