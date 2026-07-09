<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_utilities', function (Blueprint $table) {
            $table->id();
            $table->integer('utility_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('estate_id');
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->boolean('activated')->default(false);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->unique(['utility_id', 'user_id', 'estate_id']);
            $table->index('user_id');
            $table->index('utility_id');
            $table->index('estate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_utilities');
    }
};
