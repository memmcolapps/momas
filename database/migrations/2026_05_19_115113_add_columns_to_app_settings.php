<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('key')->unique()->after('title');
            $table->json('value')->after('key');
            $table->text('description')->nullable()->after('value');
            $table->unsignedBigInteger('module_id')->nullable()->after('description');
            $table->string('type')->nullable()->after('module_id');
            $table->string('group')->nullable()->after('type');
            $table->boolean('is_active')->default(true)->after('group');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['title', 'key', 'value', 'description', 'module_id', 'type', 'group', 'is_active']);
        });
    }
};
