<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MeterTransactionExport;
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
}
