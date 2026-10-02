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
        if (! Schema::hasTable('merchants')) {
            Schema::create('merchants', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone_no')->nullable();
                $table->string('serial_no')->nullable()->index();
                $table->string('tid')->nullable()->index();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->tinyInteger('status')->default(2)->comment('0=inactive, 2=active, 3=blocked');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_logs')) {
            Schema::create('pos_logs', function (Blueprint $table) {
                $table->id();
                $table->string('rrn')->nullable()->index();
                $table->unsignedBigInteger('estate_id')->nullable()->index();
                $table->unsignedBigInteger('merchant_id')->nullable()->index();
                $table->string('cardName')->nullable();
                $table->decimal('amount', 14, 2)->nullable();
                $table->string('STAN')->nullable();
                $table->string('serialNO')->nullable()->index();
                $table->string('expireDate')->nullable();
                $table->string('responseMessage')->nullable();
                $table->string('pan')->nullable();
                $table->string('responseCode')->nullable();
                $table->string('terminalID')->nullable()->index();
                $table->string('trx_date')->nullable();
                $table->string('trx_time')->nullable();
                $table->string('token')->nullable();
                $table->decimal('vending_amount', 14, 2)->nullable();
                $table->decimal('vat_amount', 14, 2)->nullable();
                $table->decimal('vend_amount_kw_per_naira', 14, 2)->nullable();
                $table->string('meter_no')->nullable()->index();
                $table->string('address')->nullable();
                $table->string('name')->nullable();
                $table->string('accountBalance')->nullable();
                $table->string('log_status')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamp('createdAt')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_logs');
        Schema::dropIfExists('merchants');
    }
};
