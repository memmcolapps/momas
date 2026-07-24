<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('utitlities') && !Schema::hasTable('utilities')) {
            Schema::rename('utitlities', 'utilities');
        }

        Schema::table('utilities', function (Blueprint $table) {
            $table->decimal('balance', 14, 2)->nullable()->after('amount');
            $table->dateTime('start_date')->nullable()->after('duration');
            $table->string('mode_of_payment', 50)->nullable()->after('start_date');
            $table->decimal('payment_amount', 14, 2)->nullable()->after('mode_of_payment');
            $table->boolean('activated')->default(false)->after('payment_amount');
            $table->integer('operator_id')->nullable()->after('activated');
            $table->decimal('amount_paid', 14, 2)->nullable()->after('operator_id');
            $table->decimal('percent_payment', 5, 2)->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('utilities', function (Blueprint $table) {
            $table->dropColumn([
                'balance', 'start_date', 'mode_of_payment', 'payment_amount',
                'activated', 'operator_id', 'amount_paid', 'percent_payment',
            ]);
        });

        Schema::rename('utilities', 'utitlities');
    }
};
