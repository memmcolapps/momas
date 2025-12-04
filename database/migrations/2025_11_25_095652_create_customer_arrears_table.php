<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_arrears', function (Blueprint $table) {
            $table->id();

            // Customer & Meter Linkage
            $table->bigInteger('user_id');
            $table->string('meter_no', 200);
            $table->integer('estate_id');

            // Arrear Details
            $table->enum('arrear_type', ['fixed_charge', 'utility_admin_fee', 'utility_service', 'customer_service_charge']);
            $table->string('description')->nullable();

            // Amounts
            $table->double('amount_due', 14, 2);
            $table->double('amount_paid', 14, 2)->default(0.00);
            $table->double('balance', 14, 2);

            // Dates
            $table->string('billing_period', 20)->nullable();
            $table->date('due_date');
            $table->date('generated_date');
            $table->date('paid_date')->nullable();

            // Status: 0=unpaid, 1=partially_paid, 2=fully_paid, 3=waived
            $table->integer('status')->default(0);

            // References
            $table->string('reference_table', 50)->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->bigInteger('transaction_id')->nullable();

            // Metadata
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'meter_no']);
            $table->index('status');
            $table->index('arrear_type');
            $table->index('billing_period');
            $table->index('created_at');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('estate_id')->references('id')->on('estates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_arrears');
    }
};
