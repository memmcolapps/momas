<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('service_title');
                $table->timestamps();
            });

            // Seed default service categories
            $defaultServices = [
                ['service_title' => 'Electrician', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Plumber', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Carpenter', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Security', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Waste Management', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Cleaning Service', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Generator Technician', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'AC & Refrigeration', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Painting & Masonry', 'created_at' => now(), 'updated_at' => now()],
                ['service_title' => 'Gardening & Landscaping', 'created_at' => now(), 'updated_at' => now()],
            ];
            DB::table('services')->insert($defaultServices);
        }

        if (!Schema::hasTable('estate_services')) {
            Schema::create('estate_services', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('estate_id')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('professional_name')->nullable();
                $table->string('professional_email')->nullable();
                $table->string('professional_phone')->nullable();
                $table->string('service_title')->nullable();
                $table->decimal('rating', 3, 1)->default(0.0);
                $table->tinyInteger('status')->default(2);
                $table->timestamps();

                $table->index('estate_id');
                $table->index('service_id');
            });
        }

        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id')->nullable();
                $table->unsignedBigInteger('estate_service_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->text('comment')->nullable();
                $table->tinyInteger('rate')->default(0);
                $table->integer('count')->default(0);
                $table->timestamps();

                $table->index('estate_service_id');
                $table->index('job_id');
            });
        }

        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->integer('count')->default(0);
                $table->integer('rate')->default(0);
                $table->timestamps();

                $table->index('job_id');
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->decimal('rating', 3, 1)->default(0.0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('estate_services');
        Schema::dropIfExists('services');
    }
};
