<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->string('type', 30)->default('text_notice')->index()->after('title');
            $table->text('text')->nullable()->after('type');
            $table->dropColumn(['image_url', 'is_urgent']);
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('title');
            $table->boolean('is_urgent')->default(false)->after('displays_at');
            $table->dropColumn(['type', 'text']);
        });
    }
};
