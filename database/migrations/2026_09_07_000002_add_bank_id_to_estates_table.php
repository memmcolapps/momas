<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->integer('bank_id')->nullable()->after('bank');
            $table->foreign('bank_id')->references('id')->on('banks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('estates', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
        });
    }
};
