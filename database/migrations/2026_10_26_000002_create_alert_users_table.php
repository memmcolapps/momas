<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alert_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->unique(['alert_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_users');
    }
};
