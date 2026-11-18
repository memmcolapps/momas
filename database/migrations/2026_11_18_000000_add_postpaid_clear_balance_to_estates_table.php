<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->decimal('postpaid_clear_balance', 14, 2)->default(0)->after('transaction_fee');
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropColumn('postpaid_clear_balance');
        });
    }
};
