<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('alerts')->onDelete('cascade');
            $table->string('path');
            $table->string('url')->nullable();
            $table->integer('width')->default(720);
            $table->integer('height')->default(960);
            $table->string('fit')->default('CONTAIN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_images');
    }
};
