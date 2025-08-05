<?php

namespace App\Exports;

use App\Models\UtilitiesPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class UtilitiesPaymentsExport implements FromCollection, WithHeadings
{
    protected $userId;
    protected $customerId;

    public function __construct($userId, $estateId)
    {
        $this->userId = $userId;
        $this->estateId = $estateId;
    }

    public function collection()
    {
        $payments = UtilitiesPayment::with(['user', 'estate'])
            ->where('user_id', $this->userId)
            ->where('estate_id', $this->estateId)
            ->latest()
            ->get();

        return $payments->map(function ($payment) {
            return [
                'Estate Name'    => optional($payment->estate)->title,
                'User Name'      => optional($payment->user)->first_name." ".optional($payment->user)->last_name,
                'Amount'         => $payment->amount,
                'Total Amount'   => $payment->total_amount,
                'Duration'       => $payment->duration,
                'Next Due Date'  => $payment->next_due_date,
                'Type'           => $payment->type,
                'Created At'     => $payment->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Estate Name',
            'User Name',
            'Amount',
            'Total Amount',
            'Duration',
            'Next Due Date',
            'Type',
            'Created At',
        ];
    }

}

