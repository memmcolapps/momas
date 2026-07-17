<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('estate_id');
            $table->date('monthly_end_date')->nullable()->after('start_date');
            $table->integer('payment_months')->nullable()->after('monthly_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'monthly_end_date', 'payment_months']);
        });
    }
};
