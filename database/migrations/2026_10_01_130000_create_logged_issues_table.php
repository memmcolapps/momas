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
        Schema::create('logged_issues', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique();
            $table->unsignedBigInteger('estate_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('meter_no')->nullable();
            $table->string('house_no')->nullable();
            $table->string('category')->default('General');
            $table->string('priority')->default('medium');
            $table->string('title');
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Pending/Open, 1: In Progress, 2: Resolved, 3: Closed
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logged_issues');
    }
};
