<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alert_id')->unique();
            $table->boolean('all_users')->default(false);
            $table->json('users')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_groups');
    }
};
