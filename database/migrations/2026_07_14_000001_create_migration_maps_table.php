<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_maps', function (Blueprint $table) {
            $table->id();
            $table->string('map_name', 50);     // 'estateMap', 'tariffMap', 'exported_buids', etc.
            $table->string('legacy_key', 255);  // BUID, MeterNo, TariffID_BUID, etc.
            $table->string('mapped_value', 255); // MySQL ID or value
            $table->timestamps();

            $table->unique(['map_name', 'legacy_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_maps');
    }
};
