<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('token_ledgers', function (Blueprint $table) {
            $table->string('token_type')->default('credit_token')->after('credit_token_id')->index();
        });

        $allowedTypes = [
            'credit_token',
            'credit_token_others',
            'tamper_token',
            'clear_credit_token',
            'kct_token',
        ];

        DB::table('token_ledgers')
            ->select('id', 'trx_id')
            ->orderBy('id')
            ->chunkById(200, function ($ledgers) use ($allowedTypes) {
                foreach ($ledgers as $ledger) {
                    $serviceType = DB::table('transactions')
                        ->where('trx_id', $ledger->trx_id)
                        ->value('service_type');

                    if (in_array($serviceType, $allowedTypes, true)) {
                        DB::table('token_ledgers')
                            ->where('id', $ledger->id)
                            ->update(['token_type' => $serviceType]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('token_ledgers', function (Blueprint $table) {
            $table->dropIndex(['token_type']);
            $table->dropColumn('token_type');
        });
    }
};
