<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{
    protected $estate_id;
    protected $status;
    protected $transaction_type;
    protected $from_date;
    protected $to_date;
    protected $rrn;

    public function __construct($estate_id = null, $status = null, $transaction_type = null, $from_date = null, $to_date = null, $rrn = null)
    {
        // dd($estate_id, $status, $transaction_type, $from_date, $to_date, $rrn);
        $this->estate_id = $estate_id;
        $this->status = $status;
        $this->transaction_type = $transaction_type;
        $this->from_date = $from_date ? Carbon::parse($from_date) : null;
        $this->to_date = $to_date ? Carbon::parse($to_date) : null;
        $this->rrn = $rrn;
    }

    public function collection()
    {
        $query = Transaction::query();

        // Filter by RRN/Transaction ID if provided
        if ($this->rrn) {
            $query->where('trx_id', $this->rrn);
        }

        // Filter by estate if provided
        if ($this->estate_id && $this->estate_id != 'all') {
            $query->where('estate_id', $this->estate_id);
        }

        // Filter by status if provided
        if ($this->status !== null && $this->status !== '') {
            $query->where('status', $this->status);
        }

        // Filter by transaction type if provided
        if ($this->transaction_type) {
            $query->where('service_type', $this->transaction_type);
        }

        // Filter by date range if provided
        if ($this->from_date && $this->to_date) {
            $query->where('created_at', '>=', $this->from_date->startOfDay())
                ->where('created_at', '<=', $this->to_date->endOfDay());
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $totalAmount = 0;
        $totalTariffAmount = 0;
        $totalServiceFee = 0;
        $totalVatAmount = 0;
        $totalVendingAmount = 0;

        $rows = $transactions->map(function ($transaction) use (&$totalAmount, &$totalTariffAmount, &$totalServiceFee, &$totalVatAmount, &$totalVendingAmount) {
                $statusText = 'Unknown';
                if ($transaction->status == 2) {
                    $statusText = 'Approved';
                } elseif ($transaction->status == 0) {
                    $statusText = 'Pending';
                } elseif ($transaction->status == 3) {
                    $statusText = 'Failed';
                } elseif ($transaction->status == 4) {
                    $statusText = 'Completed';
                }

                $transactionType = $transaction->service_type ?? 'N/A';
                if ($transaction->service_type == 'credit_token') {
                    $transactionType = 'Credit Token';
                } elseif ($transaction->service_type == 'compensation_token') {
                    $transactionType = 'Compensation Token';
                } elseif ($transaction->service_type == 'kct_token') {
                    $transactionType = 'KCT Token';
                } elseif ($transaction->service_type == 'tamper_token') {
                    $transactionType = 'Tamper Token';
                } elseif ($transaction->service_type == 'clear_credit_token') {
                    $transactionType = 'Clear Credit Token';
                } elseif ($transaction->service_type == 'arrears') {
                    $transactionType = 'Arrears';
                } elseif ($transaction->service_type == 'utility_payment') {
                    $transactionType = 'Utility Payment';
                } elseif ($transaction->service_type == 'vtu') {
                    $transactionType = 'VAS';
                }

                $breakdown = $transaction->breakdown ?? [];

                $tariffAmount = isset($breakdown['tariffAmount']) ? (float) $breakdown['tariffAmount'] : null;
                $serviceFee = isset($breakdown['serviceFee']) ? (float) $breakdown['serviceFee'] : null;
                $vatAmount = isset($breakdown['vatAmount']) ? (float) $breakdown['vatAmount'] : null;
                $vendingAmount = isset($breakdown['vending_amount'])
                    ? (float) $breakdown['vending_amount']
                    : (isset($breakdown['vendingAmount']) ? (float) $breakdown['vendingAmount'] : null);
                $unit = $breakdown['unit'] ?? null;

                $amount = (float) $transaction->amount;

                if ($tariffAmount !== null) {
                    $totalTariffAmount += $tariffAmount;
                }
                if ($serviceFee !== null) {
                    $totalServiceFee += $serviceFee;
                }
                if ($vatAmount !== null) {
                    $totalVatAmount += $vatAmount;
                }
                if ($vendingAmount !== null) {
                    $totalVendingAmount += $vendingAmount;
                }
                $totalAmount += $amount;

                return [
                    'trx_id' => $transaction->trx_id,
                    'transaction_type' => $transactionType,
                    'meter_no' => $transaction->creditToken->meterNo ?? 'N/A',
                    'customer' => ($transaction->user->first_name ?? 'N/A') . " " . ($transaction->user->last_name ?? ''),
                    'email' => $transaction->user->email ?? 'N/A',
                    'phone' => $transaction->user->phone ?? 'N/A',
                    'estate' => $transaction->estate->title ?? 'N/A',
                    'amount' => $transaction->amount,
                    'tariff_amount' => $tariffAmount !== null ? round($tariffAmount, 2) : '',
                    'service_fee' => $serviceFee !== null ? round($serviceFee, 2) : '',
                    'vat_amount' => $vatAmount !== null ? round($vatAmount, 2) : '',
                    'vending_amount' => $vendingAmount !== null ? round($vendingAmount, 2) : '',
                    'unit' => $unit !== null ? round((float) $unit, 2) : '',
                    'status' => $statusText,
                    'date' => $transaction->created_at->format('d/m/Y H:i'),
                ];
            });

        $rows->push([
            'trx_id' => 'TOTAL',
            'transaction_type' => '',
            'meter_no' => '',
            'customer' => '',
            'email' => '',
            'phone' => '',
            'estate' => '',
            'amount' => round($totalAmount, 2),
            'tariff_amount' => round($totalTariffAmount, 2),
            'service_fee' => round($totalServiceFee, 2),
            'vat_amount' => round($totalVatAmount, 2),
            'vending_amount' => round($totalVendingAmount, 2),
            'unit' => '',
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
            'Transaction Type',
            'Meter No',
            'Customer',
            'Email',
            'Phone',
            'Estate',
            'Amount (NGN)',
            'Tariff Amount (NGN)',
            'Service Fee (NGN)',
            'VAT Amount (NGN)',
            'Vending Amount (NGN)',
            'Unit (kWh)',
            'Status',
            'Transaction Date',
        ];
    }
}
