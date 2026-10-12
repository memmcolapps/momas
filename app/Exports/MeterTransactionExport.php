<?php

namespace App\Exports;

use App\Models\CreditToken;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MeterTransactionExport implements FromCollection, WithHeadings
{



    protected $meterNo;
    protected $estate_id;
    protected $from_date;
    protected $to_date;

    public function __construct($meterNo = null, $estate_id = null, $from_date = null, $to_date=null)
    {
        $this->meterNo = $meterNo;
        $this->estate_id = $estate_id;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
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

        if ($this->from_date && $this->to_date) {
            $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
        }

        $tokens = $query->orderBy('created_at', 'desc')->get();

        $totalAmount = 0;
        $totalVat = 0;
        $totalUnits = 0;
        $totalFee = 0;

        $rows = $tokens->map(function ($token) use (&$totalAmount, &$totalVat, &$totalUnits, &$totalFee) {
                $amount = (float) $token->amount;
                $vatAmount = (float) ($token->vatAmount ?? 0);
                $unitsKwh = (float) ($token->unitkwh ?? 0);
                $fee = (float) ($token->fee ?? 0);

                $totalAmount += $amount;
                $totalVat += $vatAmount;
                $totalUnits += $unitsKwh;
                $totalFee += $fee;

                return [
                    'trx_id' => $token->trx_id,
                    'meter_no' => (string) $token->meterNo ?? 'N/A',
                    'customer' => ($token->user->first_name ?? 'N/A')." ".($token->user->last_name ?? ''),
                    'email' => $token->user->email ?? 'N/A',
                    'phone' => $token->user->phone ?? 'N/A',
                    'estate' => $token->estate->title ?? 'N/A',
                    'amount' => $token->amount,
                    'vat_amount' => $vatAmount,
                    'units_kwh' => $unitsKwh,
                    'fixed_charges' => $fee,
                    'status' => $token->status == 2 ? 'Completed' : ($token->status == 1 ? 'Pending' : 'Failed'),
                    'date' => $token->created_at->format('d/m/Y H:i'), // Format the date if needed
                ];
            });

        $rows->push([
            'trx_id' => 'TOTAL',
            'meter_no' => '',
            'customer' => '',
            'email' => '',
            'phone' => '',
            'estate' => '',
            'amount' => round($totalAmount, 2),
            'vat_amount' => round($totalVat, 2),
            'units_kwh' => round($totalUnits, 2),
            'fixed_charges' => round($totalFee, 2),
            'status' => '',
            'date' => '',
        ]);

        return $rows;
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
