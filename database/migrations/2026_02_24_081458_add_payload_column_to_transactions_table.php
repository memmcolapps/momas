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
        Schema::table('transactions', function (Blueprint $table) {
            $table->json('action_payload')->nullable();
            $table->timestamp('service_rendered_at')->nullable();
            $table->index('trx_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('action_payload');
            $table->dropColumn('service_rendered_at');
            $table->dropIndex('trx_id');
            $table->dropIndex('user_id');
        });
    }
};
