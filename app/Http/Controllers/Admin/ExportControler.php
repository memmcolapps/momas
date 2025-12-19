<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MeterTransactionExport;
use App\Exports\TransactionExport;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportControler extends Controller
{
    public function exportmetertransactions(request $request)
    {
        $meterNo = $request->meterNo ?? null;
        $estate_id = null;

        // For estate admin, only export their estate's transactions
        if (Auth::user()->role == 3) {
            $estate_id = Auth::user()->estate_id;
        }

        $excelFile = Excel::raw(new MeterTransactionExport($meterNo, $estate_id), \Maatwebsite\Excel\Excel::XLSX);

        return response($excelFile)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="meter_transactions_' . date('Y-m-d_H-i-s') . '.xlsx"');

    }

    public function exporttransactions(request $request)
    {
        $estate_id = $request->estate_id ?? null;
        $status = $request->status ?? null;
        $transaction_type = $request->transaction_type ?? null;
        $from_date = $request->from ?? null;
        $to_date = $request->to ?? null;
        $rrn = $request->rrn ?? null;

        // For estate admin, only export their estate's transactions
        if (Auth::user()->role == 3) {
            $estate_id = Auth::user()->estate_id;
        }

        $excelFile = Excel::raw(new TransactionExport($estate_id, $status, $transaction_type, $from_date, $to_date, $rrn), \Maatwebsite\Excel\Excel::XLSX);

        return response($excelFile)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="transactions_' . date('Y-m-d_H-i-s') . '.xlsx"');

    }
}
