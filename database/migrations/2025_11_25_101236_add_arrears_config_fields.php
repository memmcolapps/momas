<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add arrears payment percentage to estates (per-estate config)
        Schema::table('estates', function (Blueprint $table) {
            $table->integer('arrears_payment_percentage')->default(100)
                ->after('estate_vat')
                ->comment('0-100: Percentage of arrears to deduct per transaction. 100=full payment, 0=optional');
        });

        // Add service charge amount to users (per-customer charge)
        Schema::table('users', function (Blueprint $table) {
            $table->double('service_charge_amount', 14, 2)->default(0.00)
                ->after('main_wallet')
                ->comment('Per-customer service charge deducted on every transaction');
        });

        // Add global default to settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('default_arrears_percentage')->default(100)
                ->after('admin_fee')
                ->comment('Default arrears payment percentage for new estates');
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropColumn('arrears_payment_percentage');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('service_charge_amount');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('default_arrears_percentage');
        });
    }
};
