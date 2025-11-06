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
            'file' => 'required|file|max:2048', // Max 2MB
            'estate_id' => 'nullable|exists:estates,id'
        ]);

        // Additional CSV file validation
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'csv') {
            return back()->with('error', 'Only CSV files are allowed. Please upload a file with .csv extension.');
        }

        // Validate MIME type (CSV files can have text/csv or text/plain)
        $mimeType = $file->getMimeType();
        $allowedMimeTypes = ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return back()->with('error', 'Invalid file type. Only CSV files are accepted.');
        }

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

                    // Map tariff INDICES to tariff IDs
                    $newTariffId = null;
                    $oldTariffId = null;
                    $newTariffDualId = null;
                    $oldTariffDualId = null;

                    if (!empty($meterData['newtariffid'])) {
                        $tariff = \App\Models\Tariff::where('estate_id', $request->estate_id)
                            ->where('tariff_index', $meterData['newtariffid'])
                            ->where('type', 'nepa')
                            ->first();
                        $newTariffId = $tariff ? $tariff->id : null;
                    }

                    if (!empty($meterData['oldtariffid'])) {
                        $tariff = \App\Models\Tariff::where('estate_id', $request->estate_id)
                            ->where('tariff_index', $meterData['oldtariffid'])
                            ->where('type', 'nepa')
                            ->first();
                        $oldTariffId = $tariff ? $tariff->id : null;
                    }

                    // Validate and map transformer ID - must belong to the estate
                    $transformerId = null;
                    if (!empty($meterData['transformer_id'])) {
                        $transformer = \App\Models\Transformer::where('id', $meterData['transformer_id'])
                            ->where('estate_id', $request->estate_id)
                            ->first();

                        if ($transformer) {
                            // Transformer exists and belongs to this estate
                            $transformerId = $transformer->id;
                        } else {
                            // Transformer doesn't belong to this estate, use first available transformer
                            $firstTransformer = \App\Models\Transformer::where('estate_id', $request->estate_id)
                                ->where('status', 2)
                                ->first();
                            $transformerId = $firstTransformer ? $firstTransformer->id : null;
                        }
                    }

                    // Map CSV fields to database columns
                    $meterRecord = array_merge($defaults, [
                        'meterNo' => $meterData['meterno'],
                        'meterModel' => isset($meterData['metermodel']) ? strtolower($meterData['metermodel']) : null,
                        'AccountNo' => $meterData['accountno'],
                        'TransformerID' => $transformerId,
                        'isDualTariff' => (int) $meterData['isdualtariff'],
                        'OldSGC' => $meterData['oldsgc'] ?? '999962',
                        'NewSGC' => $meterData['newsgc'] ?? '999962',
                        'NewTariffID' => $newTariffId,
                        'OldTariffID' => $oldTariffId,
                        'KRN1' => $meterData['krn1'] ?? 'STS6',
                        'KRN2' => $meterData['krn2'] ?? 'STS6',
                        'NeedKCT' => (int) ($meterData['needkct'] ?? 0),
                        'CreditTypeID' => isset($meterData['credittype']) ? strtolower($meterData['credittype']) : 'electricity',
                    ]);

                    // Handle dual tariff fields only if isDualTariff is 1
                    if ($meterData['isdualtariff'] == 1) {
                        // Map generator tariff indices to IDs
                        if (!empty($meterData['newtariffdual'])) {
                            $tariff = \App\Models\Tariff::where('estate_id', $request->estate_id)
                                ->where('tariff_index', $meterData['newtariffdual'])
                                ->where('type', 'gen')
                                ->first();
                            $newTariffDualId = $tariff ? $tariff->id : null;
                        }

                        if (!empty($meterData['oldtariffdual'])) {
                            $tariff = \App\Models\Tariff::where('estate_id', $request->estate_id)
                                ->where('tariff_index', $meterData['oldtariffdual'])
                                ->where('type', 'gen')
                                ->first();
                            $oldTariffDualId = $tariff ? $tariff->id : null;
                        }

                        // Use separate Generator SGC fields for dual tariff meters
                        $meterRecord['NewSGCDual'] = $meterData['newsgcdual'] ?? '999962';
                        $meterRecord['OldSGCDual'] = $meterData['oldsgcdual'] ?? '999962';
                        $meterRecord['NewTariffDualID'] = $newTariffDualId;
                        $meterRecord['OldTariffDualID'] = $oldTariffDualId;
                    } else {
                        // Set dual tariff fields to defaults when not dual
                        $meterRecord['NewSGCDual'] = '999962';
                        $meterRecord['OldSGCDual'] = '999962';
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
