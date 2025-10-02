<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CustomersImport;
use App\Imports\MeterImport;
use App\Models\Estate;
use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MeterImportController extends Controller
{


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx|max:2048', // Max 2MB file
            'estate_id' => 'nullable|exists:estates,id'
        ]);

        if ($request->estate_id == null) {
            $id = Auth::user()->estate_id;
        } else {
            $id = $request->estate_id;
        }

        try {
            Excel::import(new MeterImport($id), $request->file('file'));

            return redirect('meter-list')->with('message', 'Meters imported successfully!');

        } catch (\Exception $th) {
            $errorMessage = $th->getMessage();

            // Handle validation errors more gracefully
            if (str_contains($errorMessage, 'unique')) {
                return back()->with('error', 'Some meter numbers already exist in the system. Please check your CSV file.');
            }

            return back()->with('error', 'Import failed: ' . $th->getMessage());
        }
    }

    public function bulk_upload_preview(Request $request)
    {
        if (Auth::user()->role == 0) {
            $data['estates'] = Estate::where('status', 2)->get();
        }

        return view('admin.meter.bulk-upload-preview', $data ?? []);
    }

    public function get_existing_meters(Request $request)
    {
        // Return existing meter numbers for client-side validation
        $meterNumbers = Meter::pluck('meterNo')->toArray();
        return response()->json($meterNumbers);
    }

    public function bulk_save_meters(Request $request)
    {
        $request->validate([
            'meters' => 'required|array|max:100',
            'estate_id' => 'required|exists:estates,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->meters as $meterData) {
                    // Default values for required fields
                    $defaults = [
                        'status' => 0,
                        'estate_id' => $request->estate_id,
                    ];

                    // Map CSV fields to database columns
                    $meterRecord = array_merge($defaults, [
                        'meterNo' => $meterData['meterno'],
                        'meterModel' => isset($meterData['metermodel']) ? strtolower($meterData['metermodel']) : null,
                        'AccountNo' => $meterData['accountno'],
                        'TransformerID' => !empty($meterData['transformer_id']) ? $meterData['transformer_id'] : null,
                        'isDualTariff' => (int) $meterData['isdualtariff'],
                        'OldSGC' => $meterData['oldsgc'] ?? '999962',
                        'NewSGC' => $meterData['newsgc'] ?? '999962',
                        'NewTariffID' => !empty($meterData['newtariffid']) ? $meterData['newtariffid'] : null,
                        'OldTariffID' => !empty($meterData['oldtariffid']) ? $meterData['oldtariffid'] : null,
                        'KRN1' => $meterData['krn1'] ?? 'STS6',
                        'KRN2' => $meterData['krn2'] ?? 'STS6',
                        'NeedKCT' => (int) ($meterData['needkct'] ?? 0),
                        'CreditTypeID' => isset($meterData['credittype']) ? strtolower($meterData['credittype']) : 'electricity',
                    ]);

                    // Handle dual tariff fields only if isDualTariff is 1
                    if ($meterData['isdualtariff'] == 1) {
                        $meterRecord['NewSGCDual'] = $meterData['newsgc'] ?? '999962';
                        $meterRecord['OldSGCDual'] = $meterData['oldsgc'] ?? '999962';
                        $meterRecord['NewTariffDualID'] = !empty($meterData['newtariffdual']) ? $meterData['newtariffdual'] : null;
                        $meterRecord['OldTariffDualID'] = !empty($meterData['oldtariffdual']) ? $meterData['oldtariffdual'] : null;
                    } else {
                        // Set dual tariff fields to same as single tariff when not dual
                        $meterRecord['NewSGCDual'] = $meterData['newsgc'] ?? '999962';
                        $meterRecord['OldSGCDual'] = $meterData['oldsgc'] ?? '999962';
                        $meterRecord['NewTariffDualID'] = null;
                        $meterRecord['OldTariffDualID'] = null;
                    }

                    Meter::create($meterRecord);
                }
            });

            return response()->json(['success' => true, 'message' => 'Meters saved successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

}
