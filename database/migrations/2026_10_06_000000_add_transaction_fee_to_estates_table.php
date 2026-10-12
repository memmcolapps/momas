<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->string('transaction_fee_type')->default('flat');
            $table->decimal('transaction_fee', 8, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropColumn(['transaction_fee_type', 'transaction_fee']);
        });
    }
};
