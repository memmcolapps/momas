<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postpaid_accumulation_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estate_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 14, 2);
            $table->string('trx_ref');
            $table->string('paystack_reference')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=success, 2=failed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postpaid_accumulation_payments');
    }
};
