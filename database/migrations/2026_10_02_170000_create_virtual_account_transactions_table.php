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
        if (! Schema::hasTable('virtual_account_transactions')) {
            Schema::create('virtual_account_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('v_account_no')->nullable()->index();
                $table->string('v_account_name')->nullable();
                $table->string('v_bank_name')->nullable();
                $table->decimal('amount', 14, 2)->default(0);
                $table->string('type')->nullable()->comment('admin_fee, wallet_funding, withdrawal');
                $table->string('session_id')->nullable();
                $table->integer('status')->default(0)->comment('0=pending, 2=completed, 4=withdrawal, 5=funding');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_account_transactions');
    }
};
