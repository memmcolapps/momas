<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->boolean('is_global')->default(true)->after('is_active');
            $table->unsignedBigInteger('estate_id')->nullable()->after('is_global');

            $table->dropUnique('app_settings_key_unique');
            $table->unique(['key', 'estate_id']);
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropUnique('app_settings_key_estate_id_unique');
            $table->unique('key');
            $table->dropColumn(['is_global', 'estate_id']);
        });
    }
};