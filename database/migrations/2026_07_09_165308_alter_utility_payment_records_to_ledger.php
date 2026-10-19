<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing FK constraints first
        Schema::table('utility_payment_records', function (Blueprint $table) {
            $table->dropForeign(['utility_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['estate_id']);
        });

        // Add ledger columns, rename amount to utility_amount
        Schema::table('utility_payment_records', function (Blueprint $table) {
            $table->unsignedBigInteger('user_utility_id')->nullable()->after('id');
            $table->string('trx_id')->nullable()->after('status');
            $table->renameColumn('amount', 'utility_amount');
        });

        // Data migration: copy existing records into UserUtility, backfill user_utility_id
        DB::transaction(function () {
            foreach (DB::table('utility_payment_records')->get() as $record) {
                $uuId = DB::table('user_utilities')->insertGetId([
                    'utility_id'  => $record->utility_id,
                    'user_id'     => $record->user_id,
                    'estate_id'   => $record->estate_id,
                    'amount'      => $record->utility_amount,
                    'amount_paid' => $record->amount_paid,
                    'activated'   => $record->activated,
                    'status'      => $record->status,
                    'created_at'  => $record->created_at ?? now(),
                    'updated_at'  => $record->updated_at ?? now(),
                ]);

                DB::table('utility_payment_records')
                    ->where('id', $record->id)
                    ->update(['user_utility_id' => $uuId]);
            }
        });

        // Finalize: make user_utility_id not-null, add indexes
        Schema::table('utility_payment_records', function (Blueprint $table) {
            $table->unsignedBigInteger('user_utility_id')->nullable(false)->change();
            $table->index('user_utility_id');
            $table->index('utility_id');
            $table->index('user_id');
            $table->index('estate_id');
        });
    }

    public function down(): void
    {
        Schema::table('utility_payment_records', function (Blueprint $table) {
            $table->dropIndex(['user_utility_id']);
            $table->dropIndex(['utility_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['estate_id']);
            $table->dropColumn('user_utility_id');
            $table->dropColumn('trx_id');
            $table->renameColumn('utility_amount', 'amount');
        });

        // Restore FK constraints
        Schema::table('utility_payment_records', function (Blueprint $table) {
            $table->foreign('utility_id')->references('id')->on('utilities')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('estate_id')->references('id')->on('estates')->cascadeOnDelete();
        });
    }
};
