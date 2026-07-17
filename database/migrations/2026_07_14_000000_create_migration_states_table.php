<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_states', function (Blueprint $table) {
            $table->id();
            $table->string('context', 20);  // 'export' | 'import'
            $table->string('module', 50);   // 'estate', 'transformer', 'exported_estates', etc.
            $table->string('status', 20)->default('pending'); // 'completed' | 'pending' | 'failed'
            $table->json('stats')->nullable();
            $table->timestamps();

            $table->unique(['context', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_states');
    }
};
