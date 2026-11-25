<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditlogs', function (Blueprint $table) {
            if (! Schema::hasColumn('auditlogs', 'estate_id')) {
                $table->string('estate_id')->nullable()->index();
            }

            if (! Schema::hasColumn('auditlogs', 'user_type')) {
                $table->string('user_type')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'user_name')) {
                $table->string('user_name')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'entity_type')) {
                $table->string('entity_type')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'entity_id')) {
                $table->string('entity_id')->nullable()->index();
            }

            if (! Schema::hasColumn('auditlogs', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }

            if (! Schema::hasColumn('auditlogs', 'action_identifier')) {
                $table->string('action_identifier')->nullable()->index();
            }
        });

        if (Schema::hasColumn('auditlogs', 'user_type')) {
            DB::table('auditlogs')
                ->whereNull('user_type')
                ->whereNotNull('user_id')
                ->update(['user_type' => \App\Models\User::class]);
        }

        if (Schema::hasColumn('auditlogs', 'name') && Schema::hasColumn('auditlogs', 'user_name')) {
            DB::table('auditlogs')->whereNull('user_name')->update([
                'user_name' => DB::raw('name'),
            ]);

            Schema::table('auditlogs', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }

    public function down(): void
    {
        Schema::table('auditlogs', function (Blueprint $table) {
            if (! Schema::hasColumn('auditlogs', 'name')) {
                $table->string('name')->nullable();
            }
        });

        if (Schema::hasColumn('auditlogs', 'name') && Schema::hasColumn('auditlogs', 'user_name')) {
            DB::table('auditlogs')->whereNull('name')->update([
                'name' => DB::raw('user_name'),
            ]);
        }

        Schema::table('auditlogs', function (Blueprint $table) {
            foreach (['estate_id', 'user_type', 'user_name', 'entity_type', 'entity_id', 'description', 'ip_address', 'user_agent', 'action_identifier'] as $column) {
                if (Schema::hasColumn('auditlogs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
