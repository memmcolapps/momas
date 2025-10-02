<?php

namespace App\Imports;

use App\Models\Meter;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class MeterImport implements ToModel, WithHeadingRow, WithLimit
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function limit(): int
    {
        return 100; // Limit to 100 rows
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // This is a fallback for the old modal upload
        // Most uploads should use the new client-side preview system

        $defaults = [
            'status' => 0,
            'NewSGC' => '999962',
            'OldSGC' => '999962',
            'NewSGCDual' => '999962',
            'OldSGCDual' => '999962',
            'KRN1' => 'STS6',
            'KRN2' => 'STS6',
            'NeedKCT' => 0,
            'CreditTypeID' => 'electricity',
            'TransformerID' => null,
            'NewTariffID' => null,
            'OldTariffID' => null,
            'NewTariffDualID' => null,
            'OldTariffDualID' => null,
        ];

        return new Meter(array_merge($defaults, [
            'meterNo' => $row['meterno'],
            'MeterSIMNo' => $row['metersimno'] ?? null,
            'meterModel' => $row['metermodel'],
            'AccountNo' => $row['accountno'],
            'isDualTariff' => $row['isdualtariff'],
            'estate_id' => Auth::user()->role == 3 ? Auth::user()->estate_id : $this->id,
        ]));
    }
}