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
        Schema::create('alert_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('cta_url');
            $table->unsignedBigInteger('alert_id');
            $table->string('text');

            $table->index('alert_id', 'alert_buttons_alert_id');
            $table->index(['alert_id', 'id'], 'alert_buttons_id_alert_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_buttons');
    }
};
