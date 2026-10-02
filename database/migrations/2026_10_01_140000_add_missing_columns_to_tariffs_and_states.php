<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            if (!Schema::hasColumn('tariffs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('estate_id');
            }
            if (!Schema::hasColumn('tariffs', 'tariff_index')) {
                $table->integer('tariff_index')->nullable()->after('title');
            }
            if (!Schema::hasColumn('tariffs', 'status')) {
                $table->tinyInteger('status')->default(2)->after('tariff_index');
            }
            if (!Schema::hasColumn('tariffs', 'fixed_charge')) {
                $table->decimal('fixed_charge', 12, 2)->default(0.00)->after('status');
            }
        });

        Schema::table('tarrif_states', function (Blueprint $table) {
            if (!Schema::hasColumn('tarrif_states', 'status')) {
                $table->tinyInteger('status')->default(2)->after('tariff_id');
            }
            if (!Schema::hasColumn('tarrif_states', 't_index')) {
                $table->integer('t_index')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tarrif_states', 'vat')) {
                $table->decimal('vat', 8, 4)->default(0.0000)->after('amount');
            }
            if (!Schema::hasColumn('tarrif_states', 'fixed_charge')) {
                $table->decimal('fixed_charge', 12, 2)->default(0.00)->after('vat');
            }
            if (!Schema::hasColumn('tarrif_states', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('fixed_charge');
            }
            if (!Schema::hasColumn('tarrif_states', 'effective_to')) {
                $table->date('effective_to')->nullable()->after('effective_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $columns = [];
            foreach (['user_id', 'tariff_index', 'status', 'fixed_charge'] as $col) {
                if (Schema::hasColumn('tariffs', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('tarrif_states', function (Blueprint $table) {
            $columns = [];
            foreach (['status', 't_index', 'vat', 'fixed_charge', 'effective_from', 'effective_to'] as $col) {
                if (Schema::hasColumn('tarrif_states', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
