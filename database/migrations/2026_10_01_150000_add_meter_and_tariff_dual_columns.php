<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            if (!Schema::hasColumn('tariffs', 'isDualTariff')) {
                $table->string('isDualTariff')->nullable()->after('type');
            }
            if (!Schema::hasColumn('tariffs', 'NewTariffDual')) {
                $table->unsignedBigInteger('NewTariffDual')->nullable()->after('isDualTariff');
            }
            if (!Schema::hasColumn('tariffs', 'OldTariffDual')) {
                $table->unsignedBigInteger('OldTariffDual')->nullable()->after('NewTariffDual');
            }
            if (!Schema::hasColumn('tariffs', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable()->after('OldTariffDual');
            }
        });

        Schema::table('meters', function (Blueprint $table) {
            if (!Schema::hasColumn('meters', 'meterNo')) {
                $table->string('meterNo')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('meters', 'meterType')) {
                $table->string('meterType')->nullable()->after('meterNo');
            }
            if (!Schema::hasColumn('meters', 'payType')) {
                $table->string('payType')->nullable()->after('meterType');
            }
            if (!Schema::hasColumn('meters', 'meterModel')) {
                $table->string('meterModel')->nullable()->after('payType');
            }
            if (!Schema::hasColumn('meters', 'AccountNo')) {
                $table->string('AccountNo')->nullable()->after('meterModel');
            }
            if (!Schema::hasColumn('meters', 'TransformerID')) {
                $table->unsignedBigInteger('TransformerID')->nullable()->after('AccountNo');
            }
            if (!Schema::hasColumn('meters', 'isDualTariff')) {
                $table->string('isDualTariff')->nullable()->after('TransformerID');
            }
            if (!Schema::hasColumn('meters', 'NewSGC')) {
                $table->string('NewSGC')->nullable()->after('isDualTariff');
            }
            if (!Schema::hasColumn('meters', 'OldSGC')) {
                $table->string('OldSGC')->nullable()->after('NewSGC');
            }
            if (!Schema::hasColumn('meters', 'OldTariffID')) {
                $table->unsignedBigInteger('OldTariffID')->nullable()->after('NewTariffID');
            }
            if (!Schema::hasColumn('meters', 'NewTariffDualID')) {
                $table->unsignedBigInteger('NewTariffDualID')->nullable()->after('OldTariffID');
            }
            if (!Schema::hasColumn('meters', 'OldTariffDualID')) {
                $table->unsignedBigInteger('OldTariffDualID')->nullable()->after('NewTariffDualID');
            }
            if (!Schema::hasColumn('meters', 'NewSGCDual')) {
                $table->string('NewSGCDual')->nullable()->after('OldTariffDualID');
            }
            if (!Schema::hasColumn('meters', 'OldSGCDual')) {
                $table->string('OldSGCDual')->nullable()->after('NewSGCDual');
            }
            if (!Schema::hasColumn('meters', 'OldTariffDual')) {
                $table->string('OldTariffDual')->nullable()->after('OldSGCDual');
            }
            if (!Schema::hasColumn('meters', 'KRN1')) {
                $table->string('KRN1')->nullable()->after('OldTariffDual');
            }
            if (!Schema::hasColumn('meters', 'KRN2')) {
                $table->string('KRN2')->nullable()->after('KRN1');
            }
            if (!Schema::hasColumn('meters', 'NeedKCT')) {
                $table->tinyInteger('NeedKCT')->default(0)->after('KRN2');
            }
            if (!Schema::hasColumn('meters', 'CreditTypeID')) {
                $table->string('CreditTypeID')->nullable()->after('NeedKCT');
            }
            if (!Schema::hasColumn('meters', 'AddedBy')) {
                $table->string('AddedBy')->nullable()->after('CreditTypeID');
            }
            if (!Schema::hasColumn('meters', 'status')) {
                $table->tinyInteger('status')->default(2)->after('AddedBy');
            }
        });

        Schema::table('transformers', function (Blueprint $table) {
            if (!Schema::hasColumn('transformers', 'Title')) {
                $table->string('Title')->nullable()->after('transformer_name');
            }
            if (!Schema::hasColumn('transformers', 'Capacity')) {
                $table->string('Capacity')->nullable()->after('Title');
            }
            if (!Schema::hasColumn('transformers', 'Estate_id')) {
                $table->unsignedBigInteger('Estate_id')->nullable()->after('Capacity');
            }
            if (!Schema::hasColumn('transformers', 'MDMeterSN')) {
                $table->string('MDMeterSN')->nullable()->after('Estate_id');
            }
            if (!Schema::hasColumn('transformers', 'CTRatio')) {
                $table->string('CTRatio')->nullable()->after('MDMeterSN');
            }
            if (!Schema::hasColumn('transformers', 'Multiplier')) {
                $table->string('Multiplier')->nullable()->after('CTRatio');
            }
            if (!Schema::hasColumn('transformers', 'Location')) {
                $table->string('Location')->nullable()->after('Multiplier');
            }
            if (!Schema::hasColumn('transformers', 'City')) {
                $table->string('City')->nullable()->after('Location');
            }
            if (!Schema::hasColumn('transformers', 'State')) {
                $table->string('State')->nullable()->after('City');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $cols = ['isDualTariff', 'NewTariffDual', 'OldTariffDual', 'amount'];
            $drop = array_filter($cols, fn($c) => Schema::hasColumn('tariffs', $c));
            if (!empty($drop)) {
                $table->dropColumn(array_values($drop));
            }
        });

        Schema::table('meters', function (Blueprint $table) {
            $cols = [
                'meterNo', 'meterType', 'payType', 'meterModel', 'AccountNo',
                'TransformerID', 'isDualTariff', 'NewSGC', 'OldSGC', 'OldTariffID',
                'NewTariffDualID', 'OldTariffDualID', 'NewSGCDual', 'OldSGCDual',
                'OldTariffDual', 'KRN1', 'KRN2', 'NeedKCT', 'CreditTypeID',
                'AddedBy', 'status',
            ];
            $drop = array_filter($cols, fn($c) => Schema::hasColumn('meters', $c));
            if (!empty($drop)) {
                $table->dropColumn(array_values($drop));
            }
        });

        Schema::table('transformers', function (Blueprint $table) {
            $cols = [
                'Title', 'Capacity', 'Estate_id', 'MDMeterSN', 'CTRatio',
                'Multiplier', 'Location', 'City', 'State',
            ];
            $drop = array_filter($cols, fn($c) => Schema::hasColumn('transformers', $c));
            if (!empty($drop)) {
                $table->dropColumn(array_values($drop));
            }
        });
    }
};
