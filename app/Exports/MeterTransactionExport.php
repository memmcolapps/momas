<?php

namespace App\Exports;

use App\Models\CreditToken;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeterTransactionExport implements FromCollection, WithHeadings
{



    protected $meterNo;
    protected $estate_id;

    public function __construct($meterNo = null, $estate_id = null)
    {
        $this->meterNo = $meterNo;
        $this->estate_id = $estate_id;
    }


    public function collection()
    {
        $query = CreditToken::where('status', 2);

        // Filter by meter number if provided
        if ($this->meterNo) {
            $query->where('meterNo', $this->meterNo);
        }

        // Filter by estate if provided (for estate admin)
        if ($this->estate_id) {
            $query->where('estate_id', $this->estate_id);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'trx_id' => $token->trx_id,
                    'meter_no' => $token->meterNo ?? 'N/A',
                    'customer' => ($token->user->first_name ?? 'N/A')." ".($token->user->last_name ?? ''),
                    'email' => $token->user->email ?? 'N/A',
                    'phone' => $token->user->phone ?? 'N/A',
                    'estate' => $token->estate->title ?? 'N/A',
                    'amount' => $token->amount,
                    'vat_amount' => $token->vatAmount ?? 0,
                    'units_kwh' => $token->unitkwh ?? 0,
                    'fixed_charges' => $token->fee ?? 0,
                    'status' => $token->status == 2 ? 'Completed' : ($token->status == 1 ? 'Pending' : 'Failed'),
                    'date' => $token->created_at->format('d/m/Y H:i'), // Format the date if needed
                ];
            });

    }

    /**
     * Return the headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Meter No',
            'Customer',
            'Email',
            'Phone',
            'Estate',
            'Amount (NGN)',
            'VAT Amount (NGN)',
            'Units (kWh)',
            'Fixed Charges (NGN)',
            'Status',
            'Transaction Date',
        ];
    }
}
