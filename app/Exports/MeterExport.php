<?php

namespace App\Exports;

use App\Models\Meter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeterExport implements FromCollection, WithHeadings
{
    protected $userId;
    protected $estateId;

    public function __construct($userId = null, $estateId = null)
    {
        $this->userId = $userId;
        $this->estateId = $estateId;
    }

    public function collection()
    {
        $query = Meter::with(['user', 'estate'])->latest();

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->estateId) {
            $query->where('estate_id', $this->estateId);
        }

        return $query->get()->map(function ($meter) {
            return [
                'Customer Name'     => optional($meter->user)->first_name." ".optional($meter->user)->last_name,
                'Estate Name'   => optional($meter->estate)->title,
                'Meter No'      => $meter->meterNo,
                'Status'        => $meter->status,
                'Account No'    => $meter->AccountNo,
                'Meter Model'   => $meter->meterModel,
                'Created At'    => $meter->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Estate Name',
            'Meter No',
            'Status',
            'Account No',
            'Meter Model',
            'Created At',
        ];
    }
}
