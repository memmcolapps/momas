<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerImportController extends Controller
{
    /**
     * Show the bulk upload preview page
     */
    public function bulk_upload_preview(Request $request)
    {
        $data = [];

        // Super admins can select estate, estate admins use their own estate
        if (Auth::user()->role == 0) {
            $data['estates'] = Estate::where('status', 2)->get();
        }

        return view('estate.customer.bulk-upload-preview', $data);
    }

    /**
     * Get existing customer emails and phones for validation
     */
    public function get_existing_customers(Request $request)
    {
        // Super admins pass estate_id, estate admins use their own
        $estateId = $request->input('estate_id', Auth::user()->estate_id);

        if (!$estateId) {
            return response()->json(['error' => 'Estate ID required'], 400);
        }

        // Return emails and phone numbers that already exist in this estate
        $customers = User::where('estate_id', $estateId)
            ->where('role', 2)
            ->get(['email', 'phone'])
            ->toArray();

        return response()->json($customers);
    }

    /**
     * Get unassigned meters for this estate
     */
    public function get_available_meters(Request $request)
    {
        // Super admins pass estate_id, estate admins use their own
        $estateId = $request->input('estate_id', Auth::user()->estate_id);

        if (!$estateId) {
            return response()->json(['error' => 'Estate ID required'], 400);
        }

        // Return meters in this estate that don't have a user assigned
        $meters = Meter::where('estate_id', $estateId)
            ->whereNull('user_id')
            ->get(['id', 'meterNo', 'AccountNo', 'meterModel'])
            ->toArray();

        return response()->json($meters);
    }

    /**
     * Get assigned meters (for validation)
     */
    public function get_assigned_meters(Request $request)
    {
        // Super admins pass estate_id, estate admins use their own
        $estateId = $request->input('estate_id', Auth::user()->estate_id);

        if (!$estateId) {
            return response()->json(['error' => 'Estate ID required'], 400);
        }

        // Return meter numbers that are already assigned in this estate
        $assignedMeters = Meter::where('estate_id', $estateId)
            ->whereNotNull('user_id')
            ->with('user:id,first_name,last_name,meterNo')
            ->get(['id', 'meterNo', 'user_id'])
            ->map(function ($meter) {
                return [
                    'meterNo' => $meter->meterNo,
                    'assigned_to' => $meter->user ? $meter->user->first_name . ' ' . $meter->user->last_name : 'Unknown'
                ];
            })
            ->toArray();

        return response()->json($assignedMeters);
    }

    /**
     * Bulk save customers with meter assignment
     */
    public function bulk_save_customers(Request $request)
    {
        $request->validate([
            'customers' => 'required|array|max:100',
            'estate_id' => 'nullable|exists:estates,id'
        ]);

        // Super admins pass estate_id, estate admins use their own
        $estateId = $request->input('estate_id', Auth::user()->estate_id);

        if (!$estateId) {
            return response()->json([
                'success' => false,
                'message' => 'Estate ID is required'
            ], 400);
        }

        // Get estate name
        $estate = Estate::find($estateId);
        $estateName = $estate ? $estate->title : Auth::user()->estate_name;
        $savedCount = 0;
        $errors = [];

        try {
            DB::transaction(function () use ($request, $estateId, $estateName, &$savedCount, &$errors) {
                foreach ($request->customers as $index => $customerData) {
                    try {
                        // Create the customer user
                        $user = User::create([
                            'first_name' => $customerData['first_name'],
                            'last_name' => $customerData['last_name'],
                            'email' => $customerData['email'],
                            'phone' => $customerData['phone'],
                            'password' => Hash::make('password123'), // Default password
                            'role' => 2, // Customer role
                            'estate_id' => $estateId,
                            'estate_name' => $estateName,
                            'status' => 2, // Active
                            'can_login' => 0, // Customers typically don't login to web
                            'address' => $customerData['address'] ?? null,
                            'city' => $customerData['city'] ?? null,
                            'state' => $customerData['state'] ?? null,
                            'account_no' => $customerData['account_no'] ?? null,
                            'hno' => $customerData['house_no'] ?? null,
                        ]);

                        // If meter number is provided, assign the meter
                        if (!empty($customerData['meterno'])) {
                            $meter = Meter::where('estate_id', $estateId)
                                ->where('meterNo', $customerData['meterno'])
                                ->whereNull('user_id')
                                ->first();

                            if ($meter) {
                                // Bidirectional assignment
                                $meter->user_id = $user->id;
                                $meter->save();

                                // Update user with meter info
                                $user->meterid = $meter->id;
                                $user->meterNo = $meter->meterNo;
                                $user->save();
                            }
                        }

                        $savedCount++;

                    } catch (\Exception $e) {
                        $errors[] = [
                            'row' => $index + 1,
                            'email' => $customerData['email'] ?? 'N/A',
                            'error' => $e->getMessage()
                        ];
                    }
                }
            });

            if (count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some customers could not be saved',
                    'saved_count' => $savedCount,
                    'errors' => $errors
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully saved {$savedCount} customers",
                'saved_count' => $savedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 400);
        }
    }
}
