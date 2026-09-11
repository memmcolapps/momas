<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->string('name')->nullable()->after('bankName');
            $table->string('slug')->nullable()->after('name');
            $table->string('paystack_code')->nullable()->after('slug');
            $table->string('remita_code')->nullable()->after('paystack_code');
            $table->string('gateway')->nullable()->after('remita_code');
            $table->boolean('supports_transfer')->default(true)->after('gateway');
            $table->boolean('active')->default(true)->after('supports_transfer');
            $table->string('country')->nullable()->after('active');
            $table->string('currency')->nullable()->after('country');
            $table->string('type')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'slug', 'paystack_code', 'remita_code',
                'gateway', 'supports_transfer', 'active',
                'country', 'currency', 'type',
            ]);
        });
    }
};
