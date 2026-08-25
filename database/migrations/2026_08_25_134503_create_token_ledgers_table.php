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
        Schema::create('token_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('trx_id');
            $table->unsignedBigInteger('user_id');
            $table->string('meterNo');
            $table->unsignedBigInteger('credit_token_id');
            $table->decimal('trx_amount', 14, 2);
            $table->decimal('expected_fee', 14, 2);
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('receiver_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_ledgers');
    }
};
