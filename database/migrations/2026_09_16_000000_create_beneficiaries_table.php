<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estate_id');
            $table->string('line_items_id')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->string('beneficiary_account');
            $table->string('bank_code');
            $table->tinyInteger('deduct_fee_from')->default(0);
            $table->tinyInteger('status')->default(0)->comment('0=inactive, 2=active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
