<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_payment_records', function (Blueprint $table) {
            $table->id();
            $table->integer('utility_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('estate_id');
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->boolean('activated')->default(true);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('utility_id')->references('id')->on('utilities')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('estate_id')->references('id')->on('estates')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_payment_records');
    }
};
