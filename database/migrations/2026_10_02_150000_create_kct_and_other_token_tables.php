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
        if (! Schema::hasTable('kct_tokens')) {
            Schema::create('kct_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('trx_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('meterNo')->nullable()->index();
                $table->decimal('amount', 14, 2)->default(0);
                $table->decimal('amount_charged', 14, 2)->default(0);
                $table->decimal('fee', 14, 2)->default(0);
                $table->decimal('vat', 14, 2)->default(0);
                $table->string('estate_name')->nullable();
                $table->unsignedBigInteger('estate_id')->nullable()->index();
                $table->string('tariff_id')->nullable();
                $table->decimal('tariff_amount', 14, 2)->default(0);
                $table->decimal('vatAmount', 14, 2)->default(0);
                $table->decimal('costOfUnit', 14, 2)->default(0);
                $table->decimal('unitkwh', 14, 2)->default(0);
                $table->decimal('tariffPerKWatt', 14, 2)->default(0);
                $table->string('kct_token1')->nullable();
                $table->string('kct_token2')->nullable();
                $table->text('kct_tokens')->nullable();
                $table->string('need_kct')->nullable();
                $table->string('token')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=pending, 2=success, 3=failed');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tamper_tokens')) {
            Schema::create('tamper_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('trx_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('meterNo')->nullable()->index();
                $table->decimal('amount', 14, 2)->default(0);
                $table->decimal('amount_charged', 14, 2)->default(0);
                $table->decimal('fee', 14, 2)->default(0);
                $table->decimal('vat', 14, 2)->default(0);
                $table->string('estate_name')->nullable();
                $table->unsignedBigInteger('estate_id')->nullable()->index();
                $table->string('tariff_id')->nullable();
                $table->decimal('tariff_amount', 14, 2)->default(0);
                $table->decimal('vatAmount', 14, 2)->default(0);
                $table->decimal('costOfUnit', 14, 2)->default(0);
                $table->decimal('unitkwh', 14, 2)->default(0);
                $table->decimal('tariffPerKWatt', 14, 2)->default(0);
                $table->string('token')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=pending, 2=success, 3=failed');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('clearcredit_tokens')) {
            Schema::create('clearcredit_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('trx_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('meterNo')->nullable()->index();
                $table->decimal('amount', 14, 2)->default(0);
                $table->decimal('amount_charged', 14, 2)->default(0);
                $table->decimal('fee', 14, 2)->default(0);
                $table->decimal('vat', 14, 2)->default(0);
                $table->string('estate_name')->nullable();
                $table->unsignedBigInteger('estate_id')->nullable()->index();
                $table->string('tariff_id')->nullable();
                $table->decimal('tariff_amount', 14, 2)->default(0);
                $table->decimal('vatAmount', 14, 2)->default(0);
                $table->decimal('costOfUnit', 14, 2)->default(0);
                $table->decimal('unitkwh', 14, 2)->default(0);
                $table->decimal('tariffPerKWatt', 14, 2)->default(0);
                $table->string('token')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=pending, 2=success, 3=failed');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('compensation_tokens')) {
            Schema::create('compensation_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('trx_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('meterNo')->nullable()->index();
                $table->decimal('amount', 14, 2)->default(0);
                $table->decimal('amount_charged', 14, 2)->default(0);
                $table->decimal('fee', 14, 2)->default(0);
                $table->decimal('vat', 14, 2)->default(0);
                $table->string('estate_name')->nullable();
                $table->unsignedBigInteger('estate_id')->nullable()->index();
                $table->string('tariff_id')->nullable();
                $table->decimal('tariff_amount', 14, 2)->default(0);
                $table->decimal('vatAmount', 14, 2)->default(0);
                $table->decimal('costOfUnit', 14, 2)->default(0);
                $table->decimal('unitkwh', 14, 2)->default(0);
                $table->decimal('tariffPerKWatt', 14, 2)->default(0);
                $table->string('token')->nullable();
                $table->tinyInteger('status')->default(0)->comment('0=pending, 2=success, 3=failed');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kct_tokens');
        Schema::dropIfExists('tamper_tokens');
        Schema::dropIfExists('clearcredit_tokens');
        Schema::dropIfExists('compensation_tokens');
    }
};
