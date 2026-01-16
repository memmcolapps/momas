<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClearcreditToken;
use App\Models\CompensationToken;
use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\KctToken;
use App\Models\Meter;
use App\Models\Setting;
use App\Models\TamperToken;
use App\Models\TarrifState;
use App\Models\Tariff;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Services\VatCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Exception;
use Illuminate\Support\Facades\Log;


class TokenController extends Controller
{


    public function credit_token_index()
    {


        if (Auth::user()->role == 0) {


            $data['estate'] = Estate::all();
            $data['tariff'] = TarrifState::where('estate_id', user()->estate_id)->get();
            $data['preview'] = null;
            $data['credit_tokens'] = CreditToken::latest()->paginate('50');

            Log::info('credit_token_index data prepared', [
                'user_id' => Auth::id(),
                'estate_count' => $data['estate']->count(),
                'tariff_count' => $data['tariff']->count(),
                'preview' => $data['preview'],
                'credit_tokens' => [
                    'current_page' => $data['credit_tokens']->currentPage(),
                    'per_page' => $data['credit_tokens']->perPage(),
                    'total' => $data['credit_tokens']->total(),
                ],
            ]);

            return view('admin.token.credit-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = null;
            $data['tariff'] = TarrifState::where('estate_id', user()->estate_id)->get();
            $data['credit_tokens'] = CreditToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');

            return view('admin.token.credit-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    public function clear_credit_token_index()
    {


        if (Auth::user()->role == 0) {


            $data['estate'] = Estate::all();
            $data['preview'] = null;
            $data['credit_tokens'] = ClearcreditToken::latest()->paginate('50');
            $data['amount'] = Setting::where('id', 1)->first()->clear_credit_fee;

            return view('admin.token.clear-credit-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = null;
            $data['credit_tokens'] = ClearcreditToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');
            $data['amount'] = Setting::where('id', 1)->first()->clear_credit_fee;


            return view('admin.token.clear-credit-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    public function compensation_index()
    {


        if (Auth::user()->role == 0) {


            $data['estate'] = Estate::all();
            $data['preview'] = null;
            $data['credit_tokens'] = CompensationToken::latest()->paginate('50');

            return view('admin.token.compensation-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = null;
            $data['credit_tokens'] = CompensationToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');

            return view('admin.token.compensation-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function tamper_index()
    {


        if (Auth::user()->role == 0) {


            $data['estate'] = Estate::all();
            $data['preview'] = null;
            $data['tamper_amount'] = Setting::where('id', 1)->first()->clear_tamper_fee;
            $data['credit_tokens'] = TamperToken::latest()->paginate('50');


            return view('admin.token.tamper-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['estate_id'] = Auth::user()->estate_id;
            $data['tariff'] = TarrifState::where('estate_id', user()->estate_id)->get();
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = null;
            $data['tamper_amount'] = Setting::where('id', 1)->first()->clear_tamper_fee;
            $data['tamper_tokens'] = TamperToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');

            return view('admin.token.tamper-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }




    public function kct_token_index()
    {


        if (Auth::user()->role == 0) {


            $data['estate'] = Estate::all();
            $data['preview'] = null;
            $data['credit_tokens'] = KctToken::latest()->paginate('50');
            $data['kct_amount'] = Setting::where('id', 1)->first()->kct_fee;


            return view('admin.token.kct-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = null;
            $data['tariff'] = TarrifState::where('estate_id', user()->estate_id)->get();
            $data['kct_amount'] = Setting::where('id', 1)->first()->kct_fee;
            $data['credit_tokens'] = KctToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');

            return view('admin.token.kct-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    // Check if meter has dual tariff capability
    public function check_meter_dual_tariff(Request $request)
    {
        $meterNo = $request->meterNo;
        $meter = Meter::where('meterNo', $meterNo)->first();

        if (!$meter) {
            return response()->json(['isDualTariff' => false]);
        }

        // Check if meter has dual tariff (on/true) or not
        $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

        return response()->json(['isDualTariff' => $isDualTariff]);
    }


    //Validate
    public function validate_compensation_meter(request $request)
    {
        Log::info('validate_compensation_meter', [
            'request' => $request->all(),
        ]);

        if (Auth::user()->role == 0) {

            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            if($vat  == 0 ){
                $vatAmount = 0;
                $costOfUnit = $request->amount;
            }else{
                $vatAmount = $calculator->calculateVatAmount($params);
                $costOfUnit = $calculator->calculateCostOfUnit($params);
            }

            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "on";
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['credit_tokens'] = CreditToken::latest()->paginate('50');

            // Get tariff_index from Tariff model
            try {
                $tariff = Tariff::find($request->tariff_id);
                $data['tarrif_index'] = $tariff ? $tariff->tariff_index : null;
                $data['tariff_id'] = $request->tariff_id;
                if ($data['tarrif_index'] === null) {
                    return back()->with('error', 'Tariff Index is not set for the selected tariff. Please contact admin to set it in tariff configuration.');
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Error retrieving tariff index: ' . $e->getMessage());
            }

            Log::info('CompensationToken data returned', $data);

            return view('admin.token.compensation-token-view', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_name'] = Auth::user()->estate_id; // Estate Admin uses their assigned estate ID
            $data['credit_tokens'] = CreditToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');
            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = "on";

            // Get tariff_index from Tariff model
            try {
                $tariff = Tariff::find($request->tariff_id);
                $data['tarrif_index'] = $tariff ? $tariff->tariff_index : null;
                $data['tariff_id'] = $request->tariff_id;
                if ($data['tarrif_index'] === null) {
                    return back()->with('error', 'Tariff Index is not set for the selected tariff. Please contact admin to set it in tariff configuration.');
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Error retrieving tariff index: ' . $e->getMessage());
            }


            Log::info('CompensationToken data returned', $data);
            return view('admin.token.compensation-token-view', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function validate_clear_credit_meter(request $request)
    {


        if (Auth::user()->role == 0) {


            $estate_id = $request->estate_id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "clear_credit";
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['credit_tokens'] = ClearcreditToken::latest()->paginate('50');


            return view('admin.token.clear-credit-preview', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {



            $estate_id = $request->estate_id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['tarrif_amount'] = $tariffAmount;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_name'] = $request->estate_id;
            $data['tariff_id'] = $request->tariff_id;
            $data['credit_tokens'] = ClearcreditToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');
            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['preview'] = "clear_credit";


            return view('admin.token.clear-credit-preview', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function validate_meter(request $request)
    {

        Log::info('validate_meter called', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role ?? null,
            'request' => $request->all(),
        ]);
        
        if (Auth::user()->role == 0) {


            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }

            $tariffState = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first();
            $tariffAmount = $tariffState->amount ?? 0;
            $vat = $tariffState->vat ?? 0;
            $fixedCharge = $tariffState->fixed_charge ?? 0;

            // NEW CALCULATION FLOW:
            // [1] 2.5% Service Fee
            $percn = (2.5 / 100) * (int)$request->amount;
            $afterServiceFee = $request->amount - $percn;

            // [2] Estate Service Charge
            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee_flat != null) {
                $estateFee = $est->charge_fee_flat;
            } else if ($est->charge_fee_precent != null) {
                $estateFee = ($est->charge_fee_precent / 100) * (int)$request->amount;
            } else {
                $estateFee = 0;
            }
            $afterEstateFee = $afterServiceFee - $estateFee;

            // [3] Tariff Fixed Charge
            $afterFixedCharge = $afterEstateFee - $fixedCharge;

            // Validate that amount after deductions is not negative or too small
            if ($afterFixedCharge <= 0) {
                $minimumRequired = $percn + $estateFee + $fixedCharge + 10; // Adding small buffer
                return back()->with('error',
                    'Amount too small! After deducting service fee (NGN ' . number_format($percn, 2) .
                    '), estate fee (NGN ' . number_format($estateFee, 2) .
                    '), and fixed charge (NGN ' . number_format($fixedCharge, 2) .
                    '), the remaining amount would be NGN ' . number_format($afterFixedCharge, 2) .
                    '. Please enter at least NGN ' . number_format($minimumRequired, 2) . ' to proceed.');
            }

            // [4] VAT Calculation on remaining amount
            $calculator = new VatCalculator();
            $params = [
                'amountText' => $afterFixedCharge,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = $est;
            $data['preview'] = "on";
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = $tariffAmount;

            // Get tariff_index from Tariff model
            $tariff = Tariff::find($request->tariff_id);
            $data['tarrif_index'] = $tariff ? $tariff->tariff_index : null;
            $data['tariff_id'] = $request->tariff_id;

            if ($data['tarrif_index'] === null) {
                return back()->with('error', 'Tariff Index is not set for the selected tariff. Please contact admin to set it (1-99) in tariff configuration.');
            }

            $data['credit_tokens'] = CreditToken::latest()->paginate('50');
            $data['estateFee'] = $estateFee;
            $data['fixedCharge'] = $fixedCharge;
            $data['serviceFee'] = $percn;

            // Log all data before returning the view
            Log::info('Credit token preview data', $data);

            return view('admin.token.credit-token-preview', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {

            $estate_id = Estate::where('id', Auth::user()->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;
            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }


            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }

            // Estate Admin already has $estate_id from line 634 (Auth::user()->estate_id)
            // No need to get from request

            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }
            if ($request->amount < 1000) {
                return back()->with('error', 'Amount can not be less than NGN 1,000');
            }

            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            $tariffState = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first();
            $tariffAmount = $tariffState->amount ?? 0;
            $vat = $tariffState->vat ?? 0;
            $fixedCharge = $tariffState->fixed_charge ?? 0;

            // NEW CALCULATION FLOW:
            // [1] 2.5% Service Fee
            $percn = (2.5 / 100) * (int)$request->amount;
            $afterServiceFee = $request->amount - $percn;

            // [2] Estate Service Charge
            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee_flat != null) {
                $estateFee = $est->charge_fee_flat;
            } else if ($est->charge_fee_precent != null) {
                $estateFee = ($est->charge_fee_precent / 100) * (int)$request->amount;
            } else {
                $estateFee = 0;
            }
            $afterEstateFee = $afterServiceFee - $estateFee;

            // [3] Tariff Fixed Charge
            $afterFixedCharge = $afterEstateFee - $fixedCharge;

            // Validate that amount after deductions is not negative or too small
            if ($afterFixedCharge <= 0) {
                $minimumRequired = $percn + $estateFee + $fixedCharge + 10; // Adding small buffer
                return back()->with('error',
                    'Amount too small! After deducting service fee (NGN ' . number_format($percn, 2) .
                    '), estate fee (NGN ' . number_format($estateFee, 2) .
                    '), and fixed charge (NGN ' . number_format($fixedCharge, 2) .
                    '), the remaining amount would be NGN ' . number_format($afterFixedCharge, 2) .
                    '. Please enter at least NGN ' . number_format($minimumRequired, 2) . ' to proceed.');
            }

            // [4] VAT Calculation on remaining amount
            $calculator = new VatCalculator();
            $params = [
                'amountText' => $afterFixedCharge,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = $est;
            $data['preview'] = "on";
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $estate_id; // Estate Admin uses their assigned estate ID
            $data['tarrif_amount'] = $tariffAmount;

            // Get tariff_index from Tariff model
            $tariff = Tariff::find($request->tariff_id);
            $data['tarrif_index'] = $tariff ? $tariff->tariff_index : null;
            $data['tariff_id'] = $request->tariff_id;

            if ($data['tarrif_index'] === null) {
                return back()->with('error', 'Tariff Index is not set for the selected tariff. Please contact admin to set it (1-99) in tariff configuration.');
            }

            $data['credit_tokens'] = CreditToken::latest()->paginate('50');
            $data['estateFee'] = $estateFee;
            $data['fixedCharge'] = $fixedCharge;
            $data['serviceFee'] = $percn;

            return view('admin.token.credit-token-preview', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function validate_kct_meter(request $request)
    {


        if (Auth::user()->role == 0) {


            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter->NeedKCT == null || $ck_meter->NeedKCT == 0) {
                return back()->with('error', 'NeedKCT is not activated for meter');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }


            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            // Determine tariff type based on meter's dual tariff capability
            $tariff_type = $request->tariff_type ?? 'nepa';  // Default to nepa if not specified

            // Determine which tariff IDs to use
            $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

            if ($isDualTariff && $tariff_type === 'gen') {
                // Use Generator (Dual) tariff IDs
                $newTariffID = $meter->NewTariffDualID;
                $oldTariffID = $meter->OldTariffDualID;
            } else {
                // Use NEPA tariff IDs (default)
                $newTariffID = $meter->NewTariffID;
                $oldTariffID = $meter->OldTariffID;
            }

            // Get tariff amount from TarrifState using the determined tariff ID
            $tariffState = TarrifState::where('tariff_id', $newTariffID ?? $oldTariffID)->where('estate_id', $estate_id)->where('status', 2)->first();
            $tariffAmount = $tariffState->amount ?? 0;
            $vat = $tariffState->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);


            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee < 0) {

                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $request->amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "kct_token";
            $data['amount'] = $request->amount + $fee;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = $tariffAmount;
            $data['tariff_type'] = $tariff_type;  // Pass tariff type to the view
            $data['credit_tokens'] = KctToken::latest()->paginate('50');


            return view('admin.token.kct-preview', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $estate_id = Estate::where('id', Auth::user()->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }


            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            // Determine tariff type based on meter's dual tariff capability
            $tariff_type = $request->tariff_type ?? 'nepa';  // Default to nepa if not specified

            // Determine which tariff IDs to use
            $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

            if ($isDualTariff && $tariff_type === 'gen') {
                // Use Generator (Dual) tariff IDs
                $newTariffID = $meter->NewTariffDualID;
                $oldTariffID = $meter->OldTariffDualID;
            } else {
                // Use NEPA tariff IDs (default)
                $newTariffID = $meter->NewTariffID;
                $oldTariffID = $meter->OldTariffID;
            }

            // Get tariff amount from TarrifState using the determined tariff ID
            $tariffState = TarrifState::where('tariff_id', $newTariffID ?? $oldTariffID)->where('estate_id', $estate_id)->where('status', 2)->first();
            $tariffAmount = $tariffState->amount ?? 0;
            $vat = $tariffState->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $request->amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee < 0) {

                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $request->amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "kct_token";
            $data['amount'] = $request->amount + $fee;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = $tariffAmount;
            $data['tariff_type'] = $tariff_type;  // Pass tariff type to the view
            $data['credit_tokens'] = KctToken::latest()->paginate('50');


            return view('admin.token.kct-preview', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function validate_tamper_meter(request $request)
    {


        if (Auth::user()->role == 0) {


            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;
            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }


            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            // Get tamper amount from settings (set by super admin)
            $tamper_amount = Setting::where('id', 1)->first()->clear_tamper_fee ?? 0;

            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $tamper_amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);


            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee < 0) {

                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $tamper_amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "on";
            $data['amount'] = $tamper_amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
            $data['tariff_id'] = $request->tariff_id;
            $data['credit_tokens'] = TamperToken::latest()->paginate('50');
            $data['preview'] = "clear_tamper";

            return view('admin.token.tamper-preview', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $estate_id = Estate::where('id', Auth::user()->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;
            $ck_meter = Meter::where('MeterNo', $request->meterNo)->first() ?? null;
            $ck_user_id = Meter::where('MeterNo', $request->meterNo)->first()->user_id ?? null;

            if ($user == null && $ck_meter == null) {
                return back()->with('error', 'Meter has not properly configured to user');
            }

            if ($ck_meter != null && $ck_user_id == null) {
                Meter::where('MeterNo', $request->meterNo)->update(['user_id' => $user->id]);
            }


            if ($meter == null) {
                return back()->with('error', 'Meter not found on our system');
            }
            if ($meter->estate_id != $estate_id) {
                return back()->with('error', 'Meter not does not belong to estate selected');
            }


            if ($user == null) {
                return back()->with('error', 'Meter has not been attached to any customer');
            }


            // Get tamper amount from settings (set by super admin)
            $tamper_amount = Setting::where('id', 1)->first()->clear_tamper_fee ?? 0;

            $tariffAmount = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->amount ?? 0;
            $vat = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->vat ?? 0;


            $calculator = new VatCalculator();
            $params = [
                'amountText' => $tamper_amount,
                'tariffAmount' => $tariffAmount,
                'utilitiesAmount' => 0,
                'vat' => $vat,
            ];

            $vatAmount = $calculator->calculateVatAmount($params);
            $costOfUnit = $calculator->calculateCostOfUnit($params);
            $tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

            $est = Estate::where('id', $estate_id)->first();
            if ($est->charge_fee < 0) {

                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $tamper_amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }


            $data['vatAmount'] = $vatAmount;
            $data['costOfUnit'] = $costOfUnit;
            $data['tariffPerKWatt'] = $tariffPerKWatt;
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['preview'] = "on";
            $data['amount'] = $tamper_amount;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
            $data['tariff_id'] = $request->tariff_id;
            $data['credit_tokens'] = TamperToken::latest()->paginate('50');
            $data['preview'] = "clear_tamper";

            return view('admin.token.tamper-preview', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    //Generate
    public function generate_credit_meter_token(request $request)
    {
        Log::info('generate_credit_meter_token called', [
            'request' => $request->all(),
        ]);

        $est = Estate::where('id', $request->estate_name)->first();

        if ($est->charge_fee_flat == null) {
            $fee = ($est->charge_fee_precent / 100) * (int)$request->amount;
        } else {
            $fee = $est->charge_fee_flat;
        }


        $customer_email = User::where('meterNo', $request->meterNo)->first()->email;

        $amount = $request->amount - $fee;
        $trx_id = "TRX" . random_int(000000000, 9999999999);
        $estate_id = Estate::where('id', $request->estate_name)->first()->id;

        $cdt = new CreditToken();
        $cdt->user_id = $request->user_id;
        $cdt->trx_id = $trx_id;
        $cdt->meterNo = $request->meterNo;
        $cdt->amount = $amount;
        $cdt->amount_charged = $request->amount;
        $cdt->customer_email = $customer_email;
        $cdt->fee = $fee;
        $cdt->vat = $request->vat;
        $cdt->estate_name = Estate::where('id', $request->estate_name)->first()->title;;
        $cdt->estate_id = $estate_id;
        $cdt->tariff_id = $request->t_index;     //Tariff index used 
        $cdt->tariff_amount = $request->tariff_amount;
        $cdt->vatAmount = $request->vatAmount;
        $cdt->costOfUnit = $request->costOfUnit;
        $cdt->unitkwh = $request->unit;
        $cdt->tariffPerKWatt = $request->tariffPerKWatt;

        // Log before saving
        Log::info('CreditToken about to be saved', $cdt->attributesToArray());

        $cdt->save();


        try {

            if ($request->pay_type == 'flutterwave') {


                $estate_id = $request->estate_id;
                $est = Estate::where('id', $estate_id)->first();
                if ($est->charge_fee_flat == null) {
                    $fee = ($est->charge_fee_precent / 100) * (int)$request->amount;
                } else {
                    $fee = $est->charge_fee_flat;
                }


                $email = Auth::user()->email;
                $phone = Auth::user()->phone ?? "012345678";
                $fl = Setting::where('id', 1)->first();
                $secretKey = $fl->flutterwave_secret;
                $fpublic = $fl->flutterwave_public;
                $url = url('');

                $databody = array(
                    'title' => 'Payment for services',
                    'amount' => $request->amount,
                    'currency' => 'NGN',
                    'redirect_url' => $url . "/admin/pay-flutter-web",
                    'customer' => [
                        'email' => $customer_email,
                        'phonenumber' => $phone,
                        'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                    ],
                    'tx_ref' => $trx_id,

                );

                $body = json_encode($databody);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $secretKey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status ?? null;


                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->estate_id = Auth::user()->estate_id;
                $trx->pay_type = "flutterwave";
                $trx->service_type = $request->service;
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->save();

                if ($status == "success") {
                    return redirect()->away($var->data->link);

                }


            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        // --- PAYMENT BYPASS / TEST MODE ---
        if ($request->pay_type == 'test_bypass') {

            // 1. Create a successful transaction record
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->estate_id = $estate_id;
            $trx->pay_type = "bypass_test";
            $trx->service_type = $request->service ?? 'credit_token';
            $trx->amount = $request->amount;
            $trx->fee = $fee;
            $trx->trx_id = $trx_id;
            $trx->status = 2; // 2 = Successful
            $trx->save();

            // 2. Get meter details
            $meter = Meter::where('meterNo', $request->meterNo)->first();

            if (!$meter) {
                return back()->with('error', 'Meter not found');
            }

            // 3. Get tariff_index from the selected tariff
            try {
                $tariff_index = $this->getTariffIndexWithValidation($request->tariff_id);
            } catch (\Exception $e) {
                return back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
            }

            // 4. Prepare token generation API payload using UNITS (not costOfUnit)
            $unitsKwh = $request->unit ?? 0;

            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo'   => $meter->meterNo,
                'sgc'       => (int)$meter->NewSGC,
                'ti'        => $tariff_index,
                'amount'    => (float)$unitsKwh, // USING UNITS HERE - with decimals
            ];

            try {
                // 4. Call Token Generation API
                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 15,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);

                if ($response->successful()) {
                    $json_response = $response->json();
                    $decoded_data = json_decode($json_response, true);
                    $status = $decoded_data['code'] ?? null;

                    if ($status == "SUCCESS") {
                        $generated_token = $decoded_data['tokens'][0];

                        // 5. Update Credit Token Record
                        CreditToken::where('trx_id', $trx_id)->update([
                            'token' => $generated_token,
                            'status' => 2
                        ]);

                        // 6. Redirect to Receipt
                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {
                        return back()->with('error', "Payment Bypass Failed: " . ($decoded_data['msg'] ?? 'Token generation failed'));
                    }
                } else {
                    return back()->with('error', "Payment Bypass: Failed to connect to Token Server");
                }
            } catch (\Exception $e) {
                return back()->with('error', "Payment Bypass Exception: " . $e->getMessage());
            }
        }
        // --- END PAYMENT BYPASS ---


        if ($request->pay_type == 'paystack') {

        try {
            $estate_id = $request->estate_id ?? null;
            if ($estate_id === null) {
                $estate_id = Auth::user()->estate_id;
            }
            $est = Estate::where('id', $estate_id)->first();


            if ($est->charge_fee_flat == null) {
                $fee = ($est->charge_fee_precent / 100) * (int)$request->amount;
            } else {
                $fee = $est->charge_fee_flat;
            }


            $fl = Setting::where('id', 1)->first();
            $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
            $flkey['flutterwave_public'] = $fl->flutterwave_public;
            $paystackkey = $fl->paystack_secret;
            $pkkey['paystack_public'] = $fl->paystack_public;

            if ($request->utility_amount < 0) {
                $get_utility_id = UtilitiesPayment::where('user_id', Auth::id())->where('type', 'utilities')->first()->id;
            }

            //"email" => strtolower(trim($customer_email))

            $databody = array(
                "amount" => $request->amount * 100,
                "email" => strtolower(trim($customer_email)),
                "ref" => $trx_id,
                'callback_url' => url('') . "/admin/paystack-check-web",
                'subaccount' => $est->paystack_subaccount,
                'metadata' => ["ref" => $trx_id],
            );

            $body = json_encode($databody);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $paystackkey,
                ),
            ));

            $responseRaw = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($responseRaw);
            // $status = $var->status;

            // Log the decoded object as JSON for readability
            Log::info('Paystack decoded response', ['response' => $var]);

            // Access status safely
            $status = $var->status ?? null;

            // Added null and false checks
            if ($status === null) {
                return redirect('/admin/credit-token')->with(
                    'error',
                    "Paystack error: No response. Check internet connection."
                );
            }

            if ($status === false) {
                return redirect('/admin/credit-token')->with(
                    'error',
                    $var->message ?? "Payment not available at the moment, kindly select another payment option"
                );
            }


            if ($status === true) {
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->estate_id = $estate_id;
                $trx->pay_type = "paystack";
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->utility_id = $get_utility_id ?? null;
                // $trx->payment_ref = $var->data->access_code ?? null;
                $trx->payment_ref = $var->data->reference ?? null;
                $trx->service_type = "credit_token";

                // Log before saving
                Log::info('Transaction about to be saved', $trx->attributesToArray());
                Log::info('Transaction about to be saved', [
                'user_id' => Auth::id(),
                'user_id2' => Auth::user()->id,    
                ]);

                $trx->save();

                return redirect()->away($var->data->authorization_url);
            }

            // $code = 422;
            // $message = "Payment not available at the moment, Kindly select other payment option";
            // return error($message, $code);

            // return redirect('/admin/credit-token')->with(
            //     'error',
            //     $var->message ?? "Payment not available at the moment, kindly select another payment option"
            // );

           } catch (Exception $e) {
            //    return back()->with('error', $e);
                Log::error('Paystack transaction error', ['exception' => $e]);
                return redirect('/admin/credit-token')->with('error', $e->getMessage());
           }

        }


        try {

            if ($request->pay_type == 'remita') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "remita";
                $trx->service_type = "fund";
                $trx->amount = $request->amount;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => true,
                    'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
                ], 200);
            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        try {
            if ($request->pay_type == 'wallet') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                if (Auth::user()->main_wallet < $request->amount) {
                    $code = 422;
                    $message = "Insufficient Funds";
                    return error($message, $code);
                }


                User::where('id', Auth::id())->decrement('main_wallet', $request->amount);

                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "wallet";
                $trx->amount = $request->amount;
                $trx->service_type = $request->service;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => "success",
                    'ref' => $trx_id,
                ], 200);

            }


        } catch (Exception $e) {
            return back()->with('error', $e);
        }


    }

    public function generate_tamper_meter_token(request $request)
    {

         // 1. Log ALL input data to see exactly what the form submitted
        Log::info('--- START TAMPER TOKEN GENERATION ---');
        Log::info('Full Request Data:', $request->all());


        $est = Estate::where('id', $request->estate_name)->first();

        if ($request->amount > 0) {

            if ($est->charge_fee < 0) {
                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $request->amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }

        }


        $amount = $request->amount ?? 0;
        $trx_id = "TRX" . random_int(000000000, 9999999999);
        $estate_id = Estate::where('id', $request->estate_name)->first()->id;

        $cdt = new TamperToken();
        $cdt->user_id = $request->user_id;
        $cdt->trx_id = $trx_id;
        $cdt->meterNo = $request->meterNo;
        $cdt->amount = $amount ?? 0;
        $cdt->amount_charged = $request->amount ?? 0;
        $cdt->fee = 0;
        $cdt->vat = $request->vat ?? 0;
        $cdt->estate_name = Estate::where('id', $request->estate_name)->first()->title;;
        $cdt->estate_id = $estate_id;
        $cdt->tariff_id = $request->t_index;
        $cdt->tariff_amount = $request->tariff_amount;
        $cdt->vatAmount = $request->vatAmount;
        $cdt->costOfUnit = $request->costOfUnit;
        $cdt->unitkwh = $request->unit;
        $cdt->tariffPerKWatt = $request->tariffPerKWatt;
        $cdt->save();


        try {

            if ($request->pay_type == 'flutterwave') {


                $estate_id = $request->estate_id;
                $est = Estate::where('id', $estate_id)->first();
                if ($est->charge_fee < 0) {

                    $fee_in_percent = $est->charge_fee_percent;
                    $fee = ($fee_in_percent / $request->amount) * 100;
                } else {
                    $fee = $est->charge_fee;
                }


                $email = Auth::user()->email;
                $phone = Auth::user()->phone ?? "012345678";
                $fl = Setting::where('id', 1)->first();
                $secretKey = $fl->flutterwave_secret;
                $fpublic = $fl->flutterwave_public;
                $url = url('');

                $databody = array(
                    'title' => 'Payment for services',
                    'amount' => $request->amount,
                    'currency' => 'NGN',
                    'redirect_url' => $url . "/admin/flutter-verify-tamper",
                    'customer' => [
                        'email' => $email,
                        'phonenumber' => $phone,
                        'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                    ],
                    'tx_ref' => $trx_id,

                );

                $body = json_encode($databody);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $secretKey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status ?? null;


                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->estate_id = Auth::user()->estate_id;
                $trx->pay_type = "flutterwave";
                $trx->service_type = "tamper_token";
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->save();

                if ($status == "success") {
                    return redirect()->away($var->data->link);

                }
            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        if ($request->pay_type == 'paystack') {

            try {

                $estate_id = $request->estate_id ?? null;
                if ($estate_id === null) {
                    $estate_id = Auth::user()->estate_id;
                }
                $est = Estate::where('id', $estate_id)->first();
                if ($est->charge_fee < 0) {

                    $fee_in_percent = $est->charge_fee_percent;
                    $fee = ($fee_in_percent / $request->amount) * 100;
                } else {
                    $fee = $est->charge_fee;
                }


                $fl = Setting::where('id', 1)->first();
                $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
                $flkey['flutterwave_public'] = $fl->flutterwave_public;
                $paystackkey = $fl->paystack_secret;
                $pkkey['paystack_public'] = $fl->paystack_public;

                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-check-web-tamper",
                    'subaccount' => $est->paystack_subaccount,
                    'metadata' => ["ref" => $trx_id],
                );

                $body = json_encode($databody);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $paystackkey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);

                Log::info('Tamper Paystack Response', [
                    'raw_response' => $var,
                    'trx_id' => $trx_id,
                    'amount' => $request->amount,
                ]);

                $status = $var->status ?? false;


                if ($status == true) {
                    $trx = new Transaction();
                    $trx->user_id = Auth::id();
                    $trx->estate_id = Auth::user()->estate_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "tamper_token";
                    $trx->save();

                    return redirect()->away($var->data->authorization_url);

                }

                Log::error('Tamper Paystack Failed', [
                    'response' => $var,
                    'message' => $var->message ?? 'Unknown error',
                ]);

                $code = 422;
                $message = $var->message ?? "Payment not available at the moment, Kindly select other payment option";
                return back()->with('error', $message);

            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }

        }


        try {

            if ($request->pay_type == 'remita') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "remita";
                $trx->service_type = "fund";
                $trx->amount = $request->amount;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => true,
                    'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
                ], 200);
            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        try {
            if ($request->pay_type == 'wallet') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                if (Auth::user()->main_wallet < $request->amount) {
                    $code = 422;
                    $message = "Insufficient Funds";
                    return error($message, $code);
                }


                User::where('id', Auth::id())->decrement('main_wallet', $request->amount);

                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "wallet";
                $trx->amount = $request->amount;
                $trx->service_type = $request->service;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => "success",
                    'ref' => $trx_id,
                ], 200);

            }


        } catch (Exception $e) {
            return back()->with('error', $e);
        }




        if ($request->pay_type == 'vend') {


            try {


                $meterNo = $request->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $trx_id)->first();
                $traff_id = TamperToken::where('trx_id', $trx_id)->first();

                

                // Get tariff_index from Tariff model
                // try {
                //     $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                // } catch (\Exception $e) {
                //     return redirect('admin/tamper-token')->with('error', 'Tariff Index Error: ' . $e->getMessage());
                // }

                    $tariff_index = $request->t_index;
                    Log::info("Clear tamper Tariff index: $tariff_index");
                    Log::info("Clear tamper SGC: $meter->NewSGC");

                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'sbc' => 5,
                    'amount' => 10, // Amount not needed for tamper tokens
                ];

                Log::info('Tamper Token data body', ['request body' => $databody]);

                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;
                    
                    Log::info('Clear tamper response Body:', $no_kct_data);
                    

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        TamperToken::where('trx_id', $trx_id)->update([
                            'token' => $no_kct_token,
                            'status' => 2
                        ]);

                        $trx_id = $trx_id;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;

                        send_email_kct_token($email, $token, $meterNo);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "TAMPER TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->t_index,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)
                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                        return redirect('admin/tamper-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));
                    }


                    $code = 422;
                    $message = "Payment not available at the moment, Kindly select other payment option";
                    return error($message, $code);


                }
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }
    }

    public function generate_kctclear_token(request $request)
    {


        $est = Estate::where('id', $request->estate_name)->first();

        // Initialize fee and amount
        $fee = 0;
        $amount = $request->amount;

        if ($request->amount != 0) {
            if ($est->charge_fee < 0) {
                $fee_in_percent = $est->charge_fee_percent;
                $fee = ($fee_in_percent / $request->amount) * 100;
            } else {
                $fee = $est->charge_fee;
            }
            $amount = $request->amount - $fee;

        }


        $trx_id = "TRX" . random_int(000000000, 9999999999);
        $estate_id = Estate::where('id', $request->estate_name)->first()->id;

        // Get the meter to determine tariff type
        $meter = Meter::where('meterNo', $request->meterNo)->first();
        $tariff_type = $request->tariff_type ?? 'nepa';
        $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

        // Get appropriate tariff_id based on tariff type
        if ($isDualTariff && $tariff_type === 'gen') {
            $tariffId = $meter->NewTariffDualID ?? $meter->OldTariffDualID;
        } else {
            $tariffId = $meter->NewTariffID ?? $meter->OldTariffID;
        }

        $cdt = new KctToken();
        $cdt->user_id = $request->user_id;
        $cdt->trx_id = $trx_id;
        $cdt->meterNo = $request->meterNo;
        $cdt->amount = $amount ?? 0;
        $cdt->amount_charged = $request->amount ?? 0;
        $cdt->fee = $fee ?? 0;
        $cdt->vat = $request->vat ?? 0;
        $cdt->estate_name = Estate::where('id', $request->estate_name)->first()->title;;
        $cdt->estate_id = $estate_id;
        $cdt->tariff_id = $tariffId;
        $cdt->tariff_amount = $request->tariff_amount ?? 0;
        $cdt->vatAmount = $request->vatAmount ?? 0;
        $cdt->costOfUnit = $request->costOfUnit ?? 0;
        $cdt->unitkwh = $request->unit ?? 0;
        $cdt->tariffPerKWatt = $request->tariffPerKWatt ?? 0;
        $cdt->save();


        $chk_kct = Meter::where('meterNo', $request->meterNo)->first()->NeedKCT;
        if ($chk_kct == 0) {
            return back()->with('error', "Meter is not configured to vend KCT");
        }


        // --- PAYMENT BYPASS / TEST MODE FOR KCT ---
        if ($request->pay_type == 'test_bypass') {

            // 1. Create a successful transaction record
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->estate_id = $estate_id;
            $trx->pay_type = "bypass_test";
            $trx->service_type = "kct_token";
            $trx->amount = $request->amount;
            $trx->fee = $fee ?? 0;
            $trx->trx_id = $trx_id;
            $trx->status = 2; // 2 = Successful
            $trx->save();

            // 2. Get meter details
            $meter = Meter::where('meterNo', $request->meterNo)->first();

            if (!$meter) {
                return back()->with('error', 'Meter not found');
            }

            // Determine tariff type and get appropriate tariff IDs
            $tariff_type = $request->tariff_type ?? 'nepa';
            $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

            Log::info("=== KCT Token Generation - Meter Details ===", [
                'MeterNo' => $request->meterNo,
                'TariffType_Requested' => $tariff_type,
                'isDualTariff' => $isDualTariff ? 'YES' : 'NO',
                'Meter_NewTariffID' => $meter->NewTariffID,
                'Meter_OldTariffID' => $meter->OldTariffID,
                'Meter_NewTariffDualID' => $meter->NewTariffDualID,
                'Meter_OldTariffDualID' => $meter->OldTariffDualID,
            ]);

            // Get tariff_index values for KCT token (old and new tariff)
            try {
                if ($isDualTariff && $tariff_type === 'gen') {
                    // Use Generator (Dual) tariff indices
                    $ti = $this->getTariffIndexWithValidation($meter->OldTariffDualID);
                    $toti = $this->getTariffIndexWithValidation($meter->NewTariffDualID);
                    $sgc = (int)$meter->OldSGCDual;
                    $tosgc = (int)$meter->NewSGCDual;

                    Log::info("=== Using GENERATOR Tariff Indices ===", [
                        'ti (tariff_index from OldTariffDualID)' => $ti,
                        'toti (tariff_index from NewTariffDualID)' => $toti,
                        'sgc (OldSGCDual)' => $sgc,
                        'tosgc (NewSGCDual)' => $tosgc,
                    ]);
                } else {
                    // Use NEPA tariff indices (default)
                    $ti = $this->getTariffIndexWithValidation($meter->OldTariffID);
                    $toti = $this->getTariffIndexWithValidation($meter->NewTariffID);
                    $sgc = (int)$meter->OldSGC;
                    $tosgc = (int)$meter->NewSGC;

                    Log::info("=== Using NEPA Tariff Indices ===", [
                        'ti (tariff_index from OldTariffID)' => $ti,
                        'toti (tariff_index from NewTariffID)' => $toti,
                        'sgc (OldSGC)' => $sgc,
                        'tosgc (NewSGC)' => $tosgc,
                    ]);
                }
            } catch (\Exception $e) {
                Transaction::where('trx_id', $trx_id)->update(['status' => 0]);
                KctToken::where('trx_id', $trx_id)->update(['status' => 0]);
                return back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
            }

            

            // 3. Prepare KCT token generation payload
            $kctdatabody = [
                'meterType' => $meter->KRN1,
                'tometerType' => $meter->KRN2,
                'meterNo' => $request->meterNo,
                'sgc' => $sgc,
                'tosgc' => $tosgc,
                'ti' => $ti,
                'toti' => $toti,
                'allow' => false,
                'allowkrn' => true,
            ];

            // 4. Generate KCT token
            $kct_response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

            if ($kct_response->successful()) {
                $kct = $kct_response->json();
                $kct_data = json_decode($kct, true);
                $status = $kct_data['code'] ?? null;

                Log::info('KCT Request Body:', $kct_data);


                if ($status == "SUCCESS") {
                    // 5. Update KCT token record with generated tokens
                    KctToken::where('trx_id', $trx_id)->update([
                        'kct_token1' => $kct_data['tokens'][0],
                        'kct_token2' => $kct_data['tokens'][1],
                        'status' => 2
                    ]);

                    Transaction::where('trx_id', $trx_id)->update([
                        'status' => 2,
                    ]);

                    // 6. Redirect to receipt page
                    $token = "kct_token";
                    return redirect("admin/recepit?trx_id=$trx_id&type=$token");

                } else {
                    // Token generation failed
                    Transaction::where('trx_id', $trx_id)->update([
                        'status' => 3,
                        'note' => json_encode($kct_data) . "|" . json_encode($kctdatabody)
                    ]);

                    KctToken::where('trx_id', $trx_id)->update([
                        'status' => 3
                    ]);

                    return back()->with('error', 'KCT Token generation failed: ' . ($kct_data['message'] ?? 'Unknown error'));
                }
            } else {
                // API call failed
                Transaction::where('trx_id', $trx_id)->update([
                    'status' => 0,
                ]);

                KctToken::where('trx_id', $trx_id)->update([
                    'status' => 0
                ]);

                return back()->with('error', 'Failed to connect to token generation service');
            }
        }


        try {

            if ($request->pay_type == 'flutterwave') {

                // Get estate from request (form sends estate_id with the ID value)
                $estate_id = $request->estate_id ?? $request->estate_name;
                $est = Estate::where('id', $estate_id)->first();

                // Recalculate fee based on estate settings
                if ($request->amount != 0) {
                    if ($est->charge_fee < 0) {
                        $fee_in_percent = $est->charge_fee_percent;
                        $fee = ($fee_in_percent / $request->amount) * 100;
                    } else {
                        $fee = $est->charge_fee;
                    }
                } else {
                    $fee = 0;
                }

                $email = Auth::user()->email;
                $phone = Auth::user()->phone ?? "012345678";
                $fl = Setting::where('id', 1)->first();
                $secretKey = $fl->flutterwave_secret;
                $fpublic = $fl->flutterwave_public;
                $url = url('');

                $databody = array(
                    'title' => 'Payment for services',
                    'amount' => $request->amount,
                    'currency' => 'NGN',
                    'redirect_url' => $url . "/admin/flutter-verify-kct",
                    'customer' => [
                        'email' => $email,
                        'phonenumber' => $phone,
                        'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                    ],
                    'tx_ref' => $trx_id,

                );

                $body = json_encode($databody);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $secretKey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status ?? null;


                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->estate_id = $estate_id;
                $trx->pay_type = "flutterwave";
                $trx->service_type = "kct_token";
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->save();

                if ($status == "success") {
                    return redirect()->away($var->data->link);

                }


            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        if ($request->pay_type == 'paystack') {

            try {

                // Get estate from request (form sends estate_id with the ID value)
                $estate_id = $request->estate_id ?? $request->estate_name;
                $est = Estate::where('id', $estate_id)->first();

                // Recalculate fee based on estate settings
                if ($request->amount != 0) {
                    if ($est->charge_fee < 0) {
                        $fee_in_percent = $est->charge_fee_percent;
                        $fee = ($fee_in_percent / $request->amount) * 100;
                    } else {
                        $fee = $est->charge_fee;
                    }
                } else {
                    $fee = 0;
                }

                $fl = Setting::where('id', 1)->first();
                $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
                $flkey['flutterwave_public'] = $fl->flutterwave_public;
                $paystackkey = $fl->paystack_secret;
                $pkkey['paystack_public'] = $fl->paystack_public;

                // Use the existing $trx_id created at the beginning of the function
                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-check-kct",
                    'subaccount' => $est->paystack_subaccount,
                    'metadata' => ["ref" => $trx_id],
                );

                $body = json_encode($databody);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $paystackkey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status ?? false;

                if ($status == true) {
                    $trx = new Transaction();
                    $trx->user_id = $request->user_id;
                    $trx->estate_id = $estate_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "kct_token";
                    $trx->status = 0; // 0 = Pending payment verification
                    $trx->save();

                    return redirect()->away($var->data->authorization_url);

                }

                return back()->with('error', 'Payment not available at the moment, Kindly select other payment option');

            } catch (Exception $e) {
                return back()->with('error', $e);
            }

        }


        try {

            if ($request->pay_type == 'remita') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "remita";
                $trx->service_type = "fund";
                $trx->amount = $request->amount;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => true,
                    'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
                ], 200);
            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        try {
            if ($request->pay_type == 'wallet') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                if (Auth::user()->main_wallet < $request->amount) {
                    $code = 422;
                    $message = "Insufficient Funds";
                    return error($message, $code);
                }


                User::where('id', Auth::id())->decrement('main_wallet', $request->amount);

                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "wallet";
                $trx->amount = $request->amount;
                $trx->service_type = $request->service;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => "success",
                    'ref' => $trx_id,
                ], 200);

            }


        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        if ($request->pay_type == 'vend') {


            try {

                $meterNo = KctToken::where('trx_id', $trx_id)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = KctToken::where('trx_id', $trx_id)->first();
                $traff_id = KctToken::where('trx_id', $trx_id)->first();

                if ($meter != null && $meter->NeedKCT == "on") {
                    $databody = [
                        'meterType' => $meter->KRN1,
                        'meterNo' => $meterNo,
                        'sgc' => (int)$meter->OldSGC,
                        'ti' => $trx->tariff_id, //TRARRRIF INDEX
                        'amount' => (float)$trx->tariffPerKWatt,
                    ];


                    $kctdatabody = [
                        'meterType' => $meter->KRN1,
                        'tometerType' => $meter->KRN1,
                        'meterNo' => $meterNo,
                        'sgc' => (int)$meter->OldSGC,
                        'tosgc' => (int)$meter->NewSGC,
                        'ti' => $trx->tariff_id,
                        'toti' => 1,
                        'allow' => false,
                        'allowkrn' => true,
                    ];

                    $kct_response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);


                    if ($kct_response->successful()) {
                        $kct = $kct_response->json();
                        $kct_data = json_decode($kct, true);
                        $status = $kct_data['code'] ?? null;


                        if ($status == "SUCCESS") {

                            KctToken::where('trx_id', $trx_id)->update([
                                'kct_token1' => $kct_data['tokens'][0],
                                'kct_token2' => $kct_data['tokens'][1],
                                'status' => 2
                            ]);


                            Transaction::where('trx_id', $trx_id)->update([
                                'status' => 2,
                            ]);

                            $token = "kct_token";
                            return redirect("admin/recepit?trx_id=$trx_id&type=$token");


                        } else {

                            Transaction::where('trx_id', $trx_id)->update([
                                'service' => "METER PURCHASE",
                                'service_type' => "meter",
                                'status' => 0,
                                'tariff_id' => $request->tariff_id,
                                'note' => json_encode($kct_data) . "|" . json_encode($databody)


                            ]);


                            return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $kct_response->json() . " | " . json_encode($databody));

                        }


                    }
                }


            } catch (Exception $e) {
                return back()->with('error', $e);

            }
        }


    }

    public function generate_clear_credit_meter_token(request $request)
    {
        Log::info('generate_clear_credit_meter_token called', [
            'request' => $request->all(),
        ]);
       
        $trx_id = "TRX" . random_int(000000000, 9999999999);
        $estate_id = TarrifState::where('estate_id', $request->estate_name)->first()->id;
        $cdt = new ClearcreditToken();
        $cdt->user_id = $request->user_id;
        $cdt->trx_id = $trx_id;
        $cdt->meterNo = $request->meterNo;
        $cdt->amount = $request->amount;
        $cdt->vat = $request->vat;
        $cdt->estate_name = $request->estate_name;
        $cdt->estate_id = $request->estate_name;
        $cdt->tariff_id = $request->t_index; 
        $cdt->vatAmount = $request->vatAmount;
        $cdt->costOfUnit = $request->costOfUnit;
        $cdt->tariffPerKWatt = $request->tariffPerKWatt;
        $cdt->save();


        try {

            if ($request->pay_type == 'vend') {

                    Transaction::where('trx_id', $trx_id)->update(['status' => 2]);
                    $meterNo = ClearcreditToken::where('trx_id', $trx_id)->first()->meterNo;
                    $meter = Meter::where('meterNo', $meterNo)->first();
                    $trx = ClearcreditToken::where('trx_id', $trx_id)->first();
                    $traff_id = ClearcreditToken::where('trx_id', $trx_id)->first();
                    $amount = (float)number_format((float)$trx->tariffPerKWatt, 2, '.', '');

                    // UPDATED: Get tariff_index from Tariff model using helper method
                    // try {
                    //     $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                    // } catch (\Exception $e) {
                    //     ClearcreditToken::where('trx_id', $trx_id)->update(['status' => 0]);
                    //     Transaction::where('trx_id', $trx_id)->update(['status' => 0]);
                    //     return redirect('admin/clear-credit-token')->with('error', 'Tariff Index Error: ' . $e->getMessage());
                    // }

                    $tariff_index = $request->t_index;
                    Log::info("Clear credit Tariff index: $tariff_index");
                    Log::info("Clear credit SGC: $meter->NewSGC");

                    $databody = [
                        'meterType' => $meter->KRN2,
                        'meterNo' => $meter->meterNo,
                        'sgc' => (int)$meter->NewSGC,
                        'ti' => $tariff_index, // UPDATED: Use tariff_index instead of tariff_id
                        'sbc' => 1,
                        'amount' => 10, // Amount not needed for clear credit tokens
                    ];


                    $no_kct_response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                    $error = $no_kct_response->json() ?? null;


                    if ($no_kct_response->successful()) {
                        $no_kct = $no_kct_response->json();
                        $no_kct_data = json_decode($no_kct, true);
                        $status = $no_kct_data['code'] ?? null;


                        if ($status == "SUCCESS") {

                            $no_kct_token = $no_kct_data['tokens'][0];
                            ClearcreditToken::where('trx_id', $trx_id)->update([

                                'token' => $no_kct_token,
                                'status' => 2

                            ]);

                            $trx_id = $trx_id;
                            $user = User::where('id', $trx->user_id)->first();
                            $email = $user->email;
                            $token = $no_kct_token;
                            $title = "Clear Credit Token";


                            send_email_token_others($email, $token, $meterNo, $title);


                            Transaction::where('trx_id', $trx_id)->update([
                                'status' => 2,
                            ]);

                            $type = "clear_credit";
                            return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                        } else {

                            Transaction::where('trx_id', $trx_id)->update([
                                'service' => "CLEAR CREDIT TOKEN PURCHASE",
                                'service_type' => "meter",
                                'status' => 0,
                                'tariff_id' => $request->t_index,
                                'note' => json_encode($no_kct_data) . "|" . json_encode($databody)
                            ]);

                            User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                            return redirect('admin/clear-credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                        }


                    }


                    return redirect('admin/clear-credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            }

        }catch (Exception $e) {
                return back()->with('error', $e);
            }


            try {

            if ($request->pay_type == 'flutterwave') {


                $estate_id = $request->estate_id;
                $est = Estate::where('id', $estate_id)->first();
                if ($est->charge_fee < 0) {

                    $fee_in_percent = $est->charge_fee_percent;
                    $fee = ($fee_in_percent / $request->amount) * 100;
                } else {
                    $fee = $est->charge_fee;
                }


                $email = Auth::user()->email;
                $phone = Auth::user()->phone ?? "012345678";
                $fl = Setting::where('id', 1)->first();
                $secretKey = $fl->flutterwave_secret;
                $fpublic = $fl->flutterwave_public;
                $url = url('');

                $databody = array(
                    'title' => 'Payment for services',
                    'amount' => $request->amount,
                    'currency' => 'NGN',
                    'redirect_url' => $url . "/admin/flutter-verify-clear-credit",
                    'customer' => [
                        'email' => $email,
                        'phonenumber' => $phone,
                        'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                    ],
                    'tx_ref' => $trx_id,

                );

                $body = json_encode($databody);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $secretKey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status ?? null;


                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->estate_id = Auth::user()->estate_id;
                $trx->pay_type = "flutterwave";
                $trx->service_type = $request->service;
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->save();

                if ($status == "success") {
                    return redirect()->away($var->data->link);

                }


            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        if ($request->pay_type == 'paystack') {

            try {

                $estate_id = $request->estate_id ?? null;
                if ($estate_id === null) {
                    $estate_id = Auth::user()->estate_id;
                }
                $est = Estate::where('id', $estate_id)->first();
                if ($est->charge_fee < 0) {

                    $fee_in_percent = $est->charge_fee_percent;
                    $fee = ($fee_in_percent / $request->amount) * 100;
                } else {
                    $fee = $est->charge_fee;
                }


                $fl = Setting::where('id', 1)->first();
                $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
                $flkey['flutterwave_public'] = $fl->flutterwave_public;
                $paystackkey = $fl->paystack_secret;
                $pkkey['paystack_public'] = $fl->paystack_public;

                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-clear-credit",
                    'subaccount' => $est->paystack_subaccount,
                    'metadata' => ["ref" => $trx_id],
                );

                $body = json_encode($databody);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $paystackkey,
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                $status = $var->status;


                if ($status == true) {
                    $trx = new Transaction();
                    $trx->user_id = Auth::id();
                    $trx->estate_id = $estate_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "clear_credit_token";
                    $trx->save();


                    return redirect()->away($var->data->authorization_url);

                }

                $code = 422;
                $message = "Payment not available at the moment, Kindly select other payment option";
                return error($message, $code);

            } catch (Exception $e) {
                return back()->with('error', $e);
            }

        }


        try {

            if ($request->pay_type == 'remita') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "remita";
                $trx->service_type = "fund";
                $trx->amount = $request->amount;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => true,
                    'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
                ], 200);
            }

        } catch (Exception $e) {
            return back()->with('error', $e);
        }


        try {
            if ($request->pay_type == 'wallet') {
                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                if (Auth::user()->main_wallet < $request->amount) {
                    $code = 422;
                    $message = "Insufficient Funds";
                    return error($message, $code);
                }


                User::where('id', Auth::id())->decrement('main_wallet', $request->amount);

                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "wallet";
                $trx->amount = $request->amount;
                $trx->service_type = $request->service;
                $trx->trx_id = $trx_id;
                $trx->save();

                return response()->json([
                    'status' => "success",
                    'ref' => $trx_id,
                ], 200);

            }


        } catch (Exception $e) {
            return back()->with('error', $e);
        }


    }


    public function generate_compensation_meter_token(request $request)
    {
        try 
            {

                Log::info('generate_compensation_meter_token called', [
                    'request' => $request->all(),
                ]);

                $trx_id = "COMP" . random_int(000000, 999999);
                $estate_id = $request->estate_id;
                $cdt = new CompensationToken();
                $cdt->user_id = $request->user_id;
                $cdt->trx_id = $trx_id;
                $cdt->meterNo = $request->meterNo;
                $cdt->amount = $request->amount;
                $cdt->vat = $request->vat;
                $cdt->estate_name = $request->estate_name;
                $cdt->estate_id = $estate_id;
                $cdt->tariff_id = $request->t_index;
                $cdt->vatAmount = $request->vatAmount;
                $cdt->costOfUnit = $request->costOfUnit;
                $cdt->tariffPerKWatt = $request->tariffPerKWatt;
                // Log before saving
                Log::info('CompensationToken about to be saved', $cdt->attributesToArray());

                $cdt->save();


                $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
                if ($meter == null) {
                    return back()->with('error', "meter not found");
                }

                // Get tariff_index from the selected tariff
                // try {
                //     $tariff_index = $this->getTariffIndexWithValidation($request->tariff_id);
                // } catch (\Exception $e) {
                //     return back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
                // }

                $tariff_index = $request->t_index;

                $databody = [
                    'meterType' => $meter->KRN1 ?? "STS6",
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC ?? 901102,
                    'ti' => $tariff_index,
                    'amount' => (int)$request->amount,
                ];
                Log::info('CompensationToken data body', ['request body' => $databody]);


                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);
                $error = $response->json() ?? null;


                if ($response->successful()) {

                    $get_token = $response->json();
                    $token_data = json_decode($get_token, true);

                    Log::info('CompensationToken response', ['response' => $token_data]);

                    $status = $token_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $token = $token_data['tokens'][0];
                        $user = User::where('id', $meter->user_id)->first();
                        $email = $user->email;
                        $amount = $request->amount;
                        send_email_token($email, $token, $amount);


                        $trx = new Transaction();
                        $trx->trx_id = $trx_id;
                        $trx->user_id = $meter->user_id;
                        $trx->estate_id = $estate_id;
                        $trx->pay_type = null;
                        $trx->service_type = "compensation_token";
                        $trx->tariff_id = $request->t_index;
                        $trx->payment_ref = "Meter Token";
                        $trx->amount = $request->amount;
                        $trx->unit_amount = $request->costOfUnit;

                        // Log before saving
                        Log::info('Transaction to be saved', $trx->attributesToArray());

                        $trx->save();

                        CompensationToken::where('id', $cdt->id)->update(['token' => $token, 'status' => 2]);

                        return redirect("admin/recepit?trx_id=$trx->trx_id&type=compensation");


                    } else {

                        $trx = new Transaction();
                        $trx->trx_id = "COMP" . random_int(000000, 999999);
                        $trx->user_id = $meter->user_id;
                        $trx->estate_id = $estate_id;
                        $trx->pay_type = null;
                        $trx->service_type = "compensation_token";
                        $trx->tariff_id = $request->t_index;
                        $trx->payment_ref = "Meter Token";
                        $trx->amount = $request->amount;
                        $trx->unit_amount = $request->costOfUnit;
                        // Log before saving
                        Log::info('Transaction to be saved', $trx->attributesToArray());
                        $trx->save();

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->t_index,
                            'note' => json_encode($get_token) . "|" . json_encode($databody)

                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                        return redirect('admin/compensation-token')->with('error', $error['errors'][0]['title'] ?? $error . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/compensation-token')->with('error', $error['errors'][0]['title'] ?? $error . " | " . json_encode($databody));

            } 
        catch (Exception $e) {
            //    return back()->with('error', $e); 
                Log::error('Paystack validation error', ['exception' => $e]);
                return back()->with('error', $e->getMessage()); 
                // return redirect('/admin/credit-token')->with('error', $e->getMessage());
            }
    }

        // Paystack verification for web
        // Added try catch block for error handling

    public function paystack_verify_web(request $request)
    {
        try {


        Log::info('paystack_verify_web called', [
            'request' => $request->all(),
        ]);

        $fl = Setting::where('id', 1)->first();
        $pksecret = $fl->paystack_secret;
        $transactionId = $request->reference;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $pksecret",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

         // Log the decoded object as JSON for readability
        Log::info('Paystack verify response', ['response' => $var]);

        $status = $var->data->status ?? null;
        $ref = $var->data->reference ?? null;
        $trx_id = $var->data->metadata->ref ?? null;
        

        // Added null and false checks
        if ($status === null) {
            return redirect('/admin/credit-token')->with(
                'error',
                "Paystack error: No response. Check internet connection."
            );
        }

        if ($status === false) {
            CreditToken::where('trx_id', $trx_id)->update(['status' => 3]);
            Transaction::where('trx_id', $trx_id)->update(['status' => 3]);

            return redirect('/admin/credit-token')->with(
                'error',
                $var->message ?? "Payment not available at the moment, kindly select another payment option"
            );
        }


        $ck_transaction = Transaction::where('trx_id', $var->data->metadata->ref)->first()->status ?? null;
        if ($ck_transaction === null) {


            if ($status === 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2, 'payment_ref' => $ref]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                


                // Get tariff_index from Tariff model
                // try {
                //     $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                // } catch (\Exception $e) {
                //     CreditToken::where('trx_id', $var->data->metadata->ref)->update(['status' => 3]);
                //     Transaction::where('trx_id', $trx->trx_id)->update(['status' => 3]);
                //     return redirect()->back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
                // }

                $tariff_index = $trx->tariff_id;   //Tariff index directly from CreditToken table

                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'amount' => (float)$trx->unitkwh,
                ];

                 Log::info('Credit token data body', ['request body' => $databody]);

                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);

                    Log::info('Credit token response', ['response' => $no_kct_data]);

                    $status = $no_kct_data['code'] ?? null;


                    if ($status === "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($error['errors'][0]['title'] ?? $no_kct_response->json()) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                        return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }

        if ($ck_transaction === 0) {

            if ($status === 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $user = User::where('meterNo', $meterNo)->first();

                // Get tariff_index from Tariff model
                // try {
                //     $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                // } catch (\Exception $e) {
                //     CreditToken::where('trx_id', $var->data->metadata->ref)->update(['status' => 3]);
                //     Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 3]);
                //     return redirect()->back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
                // }

                $tariff_index = $trx->tariff_id;   //Tariff index directly from CreditToken table

                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'amount' => (float)$trx->unitkwh,
                ];

                Log::info('Credit token data body', ['request body' => $databody]);

                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);

                    Log::info('Credit token response', ['response' => $no_kct_data]);

                    $status = $no_kct_data['code'] ?? null;

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                        return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                    }


                }


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }
           } catch (Exception $e) {
            //    return back()->with('error', $e); 
                Log::error('Paystack validation error', ['exception' => $e]);
                return redirect('/admin/credit-token')->with('error', $e->getMessage());
           }

    }

    public function retry_generate_credit_token(request $request)
    {
    try {
        Log::info('retry_generate_credit_token called', [
            'request' => $request->all(),
        ]);

        $get_trx =  Transaction::where('trx_id', $request->trx_id)->first() ?? null;

        if($get_trx){

            if($get_trx->pay_type == "paystack"){

                $fl = Setting::where('id', 1)->first();
                $pksecret = $fl->paystack_secret;
                $transactionId = $get_trx->payment_ref;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer $pksecret",
                        "Cache-Control: no-cache",
                    ),
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);
                Log::info('Paystack verify response', ['response' => $var]);

                $status = $var->data->status ?? null;

                $ref =  $var->data->reference ?? null;
                $trx_id = $var->data->metadata->ref ?? null;


                $ck_transaction = Transaction::where('trx_id', $trx_id)->first()->status ?? null;



                if ($ck_transaction === null) {
                    if ($status === 'success') {
                        Transaction::where('trx_id', $trx_id)->update(['status' => 2, 'payment_ref' => $ref]);
                        $meterNo = CreditToken::where('trx_id', $trx_id)->first()->meterNo;
                        $meter = Meter::where('meterNo', $meterNo)->first();
                        $trx = CreditToken::where('trx_id', $trx_id)->first();
                        $traff_id = CreditToken::where('trx_id', $trx_id)->first();

                        $databody = [
                            'meterType' => $meter->KRN2,
                            'meterNo' => $meter->meterNo,
                            'sgc' => (int)$meter->NewSGC,
                            'ti' => $trx->tariff_id,
                            'amount' => (float)$trx->unitkwh,
                        ];
                        Log::info('Credit token data body', ['request body' => $databody]);

                        $url = "http://169.239.189.91:19071/tokenGen";

                        $no_kct_response = Http::withOptions([
                            'verify' => false,
                            'timeout' => 10,
                        ])->post($url, $databody);
                        $error = $no_kct_response->json() ?? null;


                        if ($no_kct_response->successful()) {
                            $no_kct = $no_kct_response->json();
                            $no_kct_data = json_decode($no_kct, true);
                            Log::info('Credit token response', ['response' => $no_kct_data]);
                            $status1 = $no_kct_data['code'] ?? null;


                            if ($status1 === "SUCCESS") {

                                $no_kct_token = $no_kct_data['tokens'][0];
                                CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                                    'token' => $no_kct_token,
                                    'status' => 2

                                ]);

                                $trx_id = $var->data->metadata->ref;
                                $user = User::where('id', $trx->user_id)->first();
                                $email = $user->email;
                                $token = $no_kct_token;
                                $amount = $trx->amount;


                                send_email_token($email, $token, $amount);


                                Transaction::where('trx_id', $trx_id)->update([
                                    'status' => 2,
                                ]);

                                $type = "credit_token";
                                return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                            } else {

                                Transaction::where('trx_id', $trx_id)->update([
                                    'service' => "METER PURCHASE",
                                    'service_type' => "meter",
                                    'status' => 3,
                                    'tariff_id' => $request->tariff_id,
                                    'note' => json_encode($error['errors'][0]['title'] ?? $no_kct_response->json()) . "|" . json_encode($databody)


                                ]);

                                User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                                return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                            }


                        }


                        return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


                    } else {
                        return back()->with('error', "Payment not found or failed on Paystack, Please try again");
                    }

                }

                if ($ck_transaction === 0) {

                    if ($status === 'success') {


                        Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                        $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                        $meter = Meter::where('meterNo', $meterNo)->first();
                        $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                        $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                        $user = User::where('meterNo', $meterNo)->first();


                        $databody = [
                            'meterType' => $meter->KRN2,
                            'meterNo' => $meter->meterNo,
                            'sgc' => (int)$meter->OldSGC,
                            'ti' => $trx->tariff_id,
                            'amount' => $trx->costOfUnit,
                        ];
                        Log::info('Credit token data body', ['request body' => $databody]);

                        $no_kct_response = Http::withOptions([
                            'verify' => false,
                            'timeout' => 10,
                        ])->post('http://169.239.189.91:19071/tokenGen', $databody);


                        if ($no_kct_response->successful()) {
                            $no_kct = $no_kct_response->json();
                            $no_kct_data = json_decode($no_kct, true);
                            Log::info('Credit token response', ['response' => $no_kct_data]);
                            $status1 = $no_kct_data['code'] ?? null;

                            if ($status1 === "SUCCESS") {

                                $no_kct_token = $no_kct_data['tokens'][0];
                                CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                                    'token' => $no_kct_token,
                                    'status' => 2

                                ]);

                                $trx_id = $var->data->metadata->ref;
                                $user = User::where('id', $trx->user_id)->first();
                                $email = $user->email;
                                $token = $no_kct_token;
                                $amount = $trx->amount;


                                send_email_token($email, $token, $amount);


                                Transaction::where('trx_id', $trx_id)->update([
                                    'status' => 2,
                                ]);

                                $type = "credit_token";
                                return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                            } else {

                                Transaction::where('trx_id', $trx_id)->update([
                                    'service' => "METER PURCHASE",
                                    'service_type' => "meter",
                                    'status' => 3,
                                    'tariff_id' => $request->tariff_id,
                                    'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                                ]);

                                User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                                return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                            }


                        }

                    } else {
                        $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                        $url = url('') . "/payment?ref=$ref&status=failure";
                        return redirect($url);
                    }

                }

                if ($ck_transaction === 3) {
                    return back()->with('error', "Payment not found or failed on Paystack, Please initiate a new purchase");    
                } else {
                    return back()->with('error', "Payment not found or failed on Paystack, Please initiate a new purchase");    
                }
            }
        }
           } catch (Exception $e) {
            //    return back()->with('error', $e); 
                Log::error('retry_generate_credit_token error: ', ['exception' => $e]);
                return back()->with('error', $e->getMessage());
           }
    }

    public function paystack_verify_kct(request $request)
    {

        $fl = Setting::where('id', 1)->first();
        $pksecret = $fl->paystack_secret;
        $transactionId = $request->reference;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $pksecret",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;
        $reff = $var->data->reference ?? null;
        $ref = $var->data->metadata->ref ?? null;
        $trx_id = $var->data->metadata->ref ?? null;


        $ck_transaction = Transaction::where('trx_id', $ref)->first()->status ?? null;
        $service_type = Transaction::where('trx_id', $ref)->first()->service_type ?? null;

        // Route KCT tokens to proper handler when transaction status is 0 (pending)
        if ($ck_transaction == 0 && $service_type == 'kct_token' && $status == 'success') {
            $meterNo = KctToken::where('trx_id', $ref)->first()->meterNo ?? null;

            if (!$meterNo) {
                return redirect('admin/kct-token')->with('error', 'KCT Token record not found');
            }

            $meter = Meter::where('meterNo', $meterNo)->first();
            $trx = KctToken::where('trx_id', $ref)->first();

            if (!$meter) {
                return redirect('admin/kct-token')->with('error', 'Meter not found');
            }

            // Check if meter is configured for KCT (same logic as bypass)
            $chk_kct = $meter->NeedKCT ?? 0;
            if ($chk_kct == 0) {
                return redirect('admin/kct-token')->with('error', 'Meter is not configured to vend KCT');
            }

            $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);
            $isGenTariff = $isDualTariff && ($trx->tariff_id == $meter->NewTariffDualID || $trx->tariff_id == $meter->OldTariffDualID);

            try {
                if ($isGenTariff) {
                    $ti = $this->getTariffIndexWithValidation($meter->OldTariffDualID);
                    $toti = $this->getTariffIndexWithValidation($meter->NewTariffDualID);
                    $sgc = (int)$meter->OldSGCDual;
                    $tosgc = (int)$meter->NewSGCDual;
                } else {
                    $ti = $this->getTariffIndexWithValidation($meter->OldTariffID);
                    $toti = $this->getTariffIndexWithValidation($meter->NewTariffID);
                    $sgc = (int)$meter->OldSGC;
                    $tosgc = (int)$meter->NewSGC;
                }
            } catch (\Exception $e) {
                KctToken::where('trx_id', $ref)->update(['status' => 3]);
                Transaction::where('trx_id', $ref)->update(['status' => 3]);
                return redirect('admin/kct-token')->with('error', 'Tariff Index Error: ' . $e->getMessage());
            }

            $kctdatabody = [
                'meterType' => $meter->KRN1,
                'tometerType' => $meter->KRN2,
                'meterNo' => $meterNo,
                'sgc' => $sgc,
                'tosgc' => $tosgc,
                'ti' => $ti,
                'toti' => $toti,
                'allow' => false,
                'allowkrn' => true,
            ];

            $kct_response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

            if ($kct_response->successful()) {
                $kct = $kct_response->json();
                $kct_data = json_decode($kct, true);
                $status_code = $kct_data['code'] ?? null;

                if ($status_code == "SUCCESS") {
                    KctToken::where('trx_id', $ref)->update([
                        'kct_token1' => $kct_data['tokens'][0],
                        'kct_token2' => $kct_data['tokens'][1],
                        'status' => 2
                    ]);

                    Transaction::where('trx_id', $ref)->update(['status' => 2]);

                    return redirect("admin/recepit?trx_id=$ref&type=kct_token");
                } else {
                    Transaction::where('trx_id', $ref)->update([
                        'status' => 3,
                        'note' => json_encode($kct_data) . "|" . json_encode($kctdatabody)
                    ]);

                    KctToken::where('trx_id', $ref)->update(['status' => 3]);

                    return redirect('admin/kct-token')->with('error', 'KCT Token generation failed: ' . ($kct_data['message'] ?? 'Unknown error'));
                }
            } else {
                Transaction::where('trx_id', $ref)->update(['status' => 3]);
                KctToken::where('trx_id', $ref)->update(['status' => 3]);

                return redirect('admin/kct-token')->with('error', 'Failed to connect to KCT token generation service');
            }
        }

        if ($ck_transaction == 0) {

            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();


                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => $trx->costOfUnit,
                ];
                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$ref&type=kct_token");

                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                        return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                    }


                }


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function paystack_verify_web_tamper(request $request)
    {
        $fl = Setting::where('id', 1)->first();
        $pksecret = $fl->paystack_secret;
        $transactionId = $request->reference;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $pksecret",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;
        $ref = $var->data->reference ?? null;


        $ck_transaction = Transaction::where('trx_id', $var->data->reference)->first()->status ?? null;
        if ($ck_transaction == null) {
            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = TamperToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);

                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'sbc' => 5,
                    'amount' => 10,
                ];


                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        TamperToken::where('trx_id', $var->data->metadata->ref)->update([
                            'token' => $no_kct_token,
                            'status' => 2
                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "TAMPER TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)
                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                        return redirect('admin/tamper-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/tamper-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }

        if ($ck_transaction == 0) {

            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = TamperToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'sbc' => 5,
                    'amount' => 10,
                ];
                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        TamperToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "CLEAR TAMPER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                        return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                    }


                }


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function paystack_clear_credit(request $request)
    {
        $fl = Setting::where('id', 1)->first();
        $pksecret = $fl->paystack_secret;
        $transactionId = $request->reference;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $pksecret",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;
        $ref = $var->data->reference ?? null;
        $trx_id = $var->data->metadata->ref ?? null;


        $ck_transaction = Transaction::where('trx_id', $var->data->reference)->first()->status ?? null;
        if ($ck_transaction == null || $ck_transaction == 0) {
            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first();

                // UPDATED: Get tariff_index from Tariff model using helper method
                try {
                    $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                } catch (\Exception $e) {
                    ClearcreditToken::where('trx_id', $var->data->metadata->ref)->update(['status' => 3]);
                    Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 3]);
                    return redirect('admin/clear-credit-token')->with('error', 'Tariff Index Error: ' . $e->getMessage());
                }
                Log::info("Paystack clear credit - Tariff index: $tariff_index");
                Log::info("Paystack clear credit - SGC: " . $meter->NewSGC);

                $databody = [
                    'meterType' => $meter->KRN2, // UPDATED: Use KRN2 for clear credit
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC, // UPDATED: Use NewSGC
                    'ti' => $tariff_index, // UPDATED: Use tariff_index instead of tariff_id
                    'sbc' => 1,
                    'amount' => 10, // UPDATED: Amount not needed for clear credit tokens
                ];


                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        ClearcreditToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "clear_credit";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "CLEAR CREDIT TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                        return redirect('admin/clear-credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function flutter_verify_web(request $request)
    {

        $fl = Setting::where('id', 1)->first();
        $flsecret = $fl->flutterwave_secret;
        $flkey['flutterwave_public'] = $fl->flutterwave_public;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $flsecret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status = $var->status ?? null;
        $ref = $var->data->tx_ref ?? null;
    

        if ($status == null) {
            return redirect("admin/credit-token")->with('error', "something went wrong");
        }

        $ck_transaction = Transaction::where('trx_id', $var->data->tx_ref)->first()->status ?? null;

        if ($ck_transaction == null) {

            if ($status == 'success') {


                Transaction::where('trx_id', $ref)->update(['status' => 2]);
                $meterNo = CreditToken::where('trx_id', $ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $ref)->first();
                $traff_id = CreditToken::where('trx_id', $ref)->first();


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => (int)$trx->costOfUnit,
                ];


                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        CreditToken::where('trx_id', $ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                        return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }

        if ($ck_transaction == 0) {

            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => $trx->costOfUnit,
                ];
                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        CreditToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                        return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                    }


                }


            } else {
                $ref = Transaction::where('trx_id', $ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function flutter_verify_web_tamper(request $request)
    {


        $fl = Setting::where('id', 1)->first();
        $flsecret = $fl->flutterwave_secret;
        $flkey['flutterwave_public'] = $fl->flutterwave_public;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $flsecret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status = $var->status ?? null;
        $ref = $var->data->tx_ref ?? null;

        if ($status == null) {
            return redirect("admin/credit-token")->with('error', "something went wrong");
        }


        $ck_transaction = Transaction::where('trx_id', $var->data->tx_ref)->first()->status ?? null;


        if ($ck_transaction == null) {

            if ($status == 'success') {

                Transaction::where('trx_id', $ref)->update(['status' => 2]);
                $meterNo = TamperToken::where('trx_id', $ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $ref)->first();
                $traff_id = TamperToken::where('trx_id', $ref)->first();
                $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                $trx_id = $trx->trx_id;


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'amount' => 10,
                    "sbc" => 5,
                ];


                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        TamperToken::where('trx_id', $ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx->trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                        return redirect('admin/tamper-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/tamper-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }

        if ($ck_transaction == 0) {

            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = TamperToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = TamperToken::where('trx_id', $var->data->metadata->ref)->first();
                $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                $trx_id = $trx->trx_id;


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $tariff_index,
                    'sbc' => 5,
                    'amount' => 10,
                ];
                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;

                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        TamperToken::where('trx_id', $var->data->metadata->ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $var->data->metadata->ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);
                        return redirect('admin/credit-token')->with('error', json_encode($no_kct_data) . " | " . json_encode($databody));

                    }


                }


            } else {
                $ref = Transaction::where('trx_id', $ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function flutter_verify_clear_credit(request $request)
    {


        $fl = Setting::where('id', 1)->first();
        $flsecret = $fl->flutterwave_secret;
        $flkey['flutterwave_public'] = $fl->flutterwave_public;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $flsecret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status = $var->status ?? null;
        $ref = $var->data->tx_ref ?? null;

        if ($status == null) {
            return redirect("admin/credit-token")->with('error', "something went wrong");
        }


        $ck_transaction = Transaction::where('trx_id', $var->data->tx_ref)->first()->status ?? null;


        if ($ck_transaction == null || $ck_transaction == 0) {

            if ($status == 'success') {

                Transaction::where('trx_id', $ref)->update(['status' => 2]);
                $meterNo = ClearcreditToken::where('trx_id', $ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = ClearcreditToken::where('trx_id', $ref)->first();
                $traff_id = ClearcreditToken::where('trx_id', $ref)->first();
                $trx_id = $trx->trx_id;

                // UPDATED: Get tariff_index from Tariff model using helper method
                try {
                    $tariff_index = $this->getTariffIndexWithValidation($trx->tariff_id);
                } catch (\Exception $e) {
                    ClearcreditToken::where('trx_id', $ref)->update(['status' => 3]);
                    Transaction::where('trx_id', $ref)->update(['status' => 3]);
                    return redirect('admin/clear-credit-token')->with('error', 'Tariff Index Error: ' . $e->getMessage());
                }
                Log::info("Flutterwave clear credit - Tariff index: $tariff_index");
                Log::info("Flutterwave clear credit - SGC: " . $meter->NewSGC);

                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC, // UPDATED: Use NewSGC
                    'ti' => $tariff_index, // UPDATED: Use tariff_index instead of tariff_id
                    'sbc' => 1,
                    'amount' => 10, // UPDATED: Amount not needed for clear credit tokens
                ];


                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/msetokenGen', $databody);
                $error = $no_kct_response->json() ?? null;


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $status = $no_kct_data['code'] ?? null;


                    if ($status == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        ClearcreditToken::where('trx_id', $ref)->update([

                            'token' => $no_kct_token,
                            'status' => 2

                        ]);

                        $trx_id = $ref;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;
                        $amount = $trx->amount;


                        send_email_token($email, $token, $amount);


                        Transaction::where('trx_id', $trx_id)->update([
                            'status' => 2,
                        ]);

                        $type = "clear_credit";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx_id)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);


                        return redirect('admin/clear-credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));

                    }


                }


                return redirect('admin/clear-credit-token')->with('error', $error['errors'][0]['title'] ?? $no_kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }


    }

    public function flutter_verify_kct(request $request)
    {


        $fl = Setting::where('id', 1)->first();
        $flsecret = $fl->flutterwave_secret;
        $flkey['flutterwave_public'] = $fl->flutterwave_public;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $flsecret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status = $var->status ?? null;
        $ref = $var->data->tx_ref ?? null;

        if ($status == null) {
            return redirect("admin/credit-token")->with('error', "something went wrong");
        }

        $ck_transaction = Transaction::where('trx_id', $var->data->tx_ref)->first()->status ?? null;


        if ($ck_transaction == null || $ck_transaction == 0) {

            if ($status == 'success') {

                Transaction::where('trx_id', $ref)->update(['status' => 2]);
                $meterNo = KctToken::where('trx_id', $ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = KctToken::where('trx_id', $ref)->first();
                $traff_id = KctToken::where('trx_id', $ref)->first();

                if ($meter != null && $meter->NeedKCT == "on") {
                    // Determine tariff indices based on meter's dual tariff capability
                    $isDualTariff = ($meter->isDualTariff === 'on' || $meter->isDualTariff === true || $meter->isDualTariff === 1);

                    // Check if tariff_id matches dual tariff IDs
                    $isGenTariff = $isDualTariff && ($trx->tariff_id == $meter->NewTariffDualID || $trx->tariff_id == $meter->OldTariffDualID);

                    try {
                        if ($isGenTariff) {
                            // Use Generator (Dual) tariff indices
                            $ti = $this->getTariffIndexWithValidation($meter->OldTariffDualID);
                            $toti = $this->getTariffIndexWithValidation($meter->NewTariffDualID);
                            $sgc = (int)$meter->OldSGCDual;
                            $tosgc = (int)$meter->NewSGCDual;
                        } else {
                            // Use NEPA tariff indices (default)
                            $ti = $this->getTariffIndexWithValidation($meter->OldTariffID);
                            $toti = $this->getTariffIndexWithValidation($meter->NewTariffID);
                            $sgc = (int)$meter->OldSGC;
                            $tosgc = (int)$meter->NewSGC;
                        }
                    } catch (\Exception $e) {
                        KctToken::where('trx_id', $ref)->update(['status' => 3]);
                        Transaction::where('trx_id', $ref)->update(['status' => 3]);
                        return redirect()->back()->with('error', 'Tariff Index Error: ' . $e->getMessage());
                    }

                    $kctdatabody = [
                        'meterType' => $meter->KRN1,
                        'tometerType' => $meter->KRN2,
                        'meterNo' => $meterNo,
                        'sgc' => $sgc,
                        'tosgc' => $tosgc,
                        'ti' => $ti,
                        'toti' => $toti,
                        'allow' => false,
                        'allowkrn' => true,
                    ];

                    $kct_response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

                    if ($kct_response->successful()) {
                        $kct = $kct_response->json();
                        $kct_data = json_decode($kct, true);
                        $status = $kct_data['code'] ?? null;


                        if ($status == "SUCCESS") {

                            KctToken::where('trx_id', $ref)->update([
                                'kct_token1' => $kct_data['tokens'][0],
                                'kct_token2' => $kct_data['tokens'][1],
                                'status' => 2
                            ]);


                            Transaction::where('trx_id', $ref)->update([
                                'status' => 2,
                            ]);

                            $token = "kct_token";
                            return redirect("admin/recepit?trx_id=$ref&type=$token");


                        } else {

                            Transaction::where('trx_id', $ref)->update([
                                'service' => "KCT TOKEN PURCHASE",
                                'service_type' => "kct_token",
                                'status' => 3,
                                'tariff_id' => $request->tariff_id,
                                'note' => json_encode($kct_data) . "|" . json_encode($kctdatabody)
                            ]);

                            User::where('id', $trx->user_id)->increment('main_wallet', $trx->amount);

                            return redirect('admin/kct-token')->with('error', 'KCT Token generation failed: ' . ($kct_data['message'] ?? 'Unknown error'));

                        }

                    } else {
                        return redirect('admin/kct-token')->with('error', 'Failed to connect to KCT token generation service');
                    }

                }
            }
        }
    }

    public function payment(Request $request)
    {
        Log::info('payment', [
            'request' => $request->all(),
        ]);

    // Handle payment callback from payment gateways
        $ref = $request->ref;
        $status = $request->status;

        if (!$ref) {
            return redirect('admin/credit-token')->with('error', 'Payment reference is missing');
        }

        // Find the transaction
        $trx = Transaction::where('trx_id', $ref)->first();

        if (!$trx) {
            return redirect('admin/credit-token')->with('error', 'Transaction not found');
        }

        // Handle declined/failed payments
        if ($status == 'failure' || $status == 'declined') {
            // Update transaction status to declined/failed
            $trx->update(['status' => 3]); // 3 = failed

            return redirect('admin/credit-token')->with('error', 'Payment was declined or failed. Reference: ' . $ref);
        }

        // Handle successful payments
        if ($status == 'success' && $trx->status == 4) {
            // Transaction is already marked as successful (status=4) by payment verification
            // Get the credit token if it exists
            $creditToken = CreditToken::where('trx_id', $ref)->first();

            if ($creditToken && $creditToken->token) {
                // Token already generated, redirect to receipt
                return redirect()->to(url('') . "/admin/recepit?trx_id=$ref&type=credit_token");
            }

            // If token doesn't exist yet, redirect to dashboard with success message
            return redirect('admin/credit-token')->with('message', 'Payment successful! Token is being processed. Reference: ' . $ref);
        }

        // Handle other statuses - redirect to dashboard
        return redirect('admin/credit-token')->with('message', 'Payment processing. Reference: ' . $ref);
    }


    public function recepit(request $request)
    {

        Log::info('recepit', [
            'request' => $request->all(),
        ]);

        if ($request->trx_id == null) {
            return back()->with('error', "Ref can not be empty");
        }


        if ($request->type == "credit_token") {

            $trx_comp = CreditToken::where('trx_id', $request->trx_id)->first() ?? null;
            $user_comp = User::where('id', $trx_comp->user_id)->first() ?? null;

            if ($trx_comp != null) {
                $data['full_name'] = $user_comp->first_name . " " . $user_comp->last_name;
                $data['address'] = $user_comp->address . "," . $user_comp->city . "," . $user_comp->state;
                $data['phone'] = $user_comp->phone;
                $data['trx_id'] = $trx_comp->trx_id;
                $data['token'] = $trx_comp->token;
                $data['ref'] = $trx_comp->trx_id;
                $data['amount'] = $trx_comp->amount_charged;
                $data['vat_amount'] = $trx_comp->vatAmount;
                $data['vend_amount_kw_per_naira'] = $trx_comp->costOfUnit;
                $data['tariff_amount'] = $trx_comp->tariff_amount;
                $data['unit'] = $trx_comp->unitkwh;
                $data['title'] = "Credit Token";
                $data['date'] = date('d-m-y h:i:s');
                $data['meter_no'] = $trx_comp->meterNo;

                return view('admin/recepit.recepit', $data);
            }
        }

        if ($request->type == "tamper") {

            $trx_comp = TamperToken::where('trx_id', $request->trx_id)->first() ?? null;
            $user_comp = User::where('id', $trx_comp->user_id)->first() ?? null;


            if ($trx_comp != null) {

                $data['full_name'] = $user_comp->first_name . " " . $user_comp->last_name;
                $data['address'] = $user_comp->address . "," . $user_comp->city . "," . $user_comp->state;
                $data['phone'] = $user_comp->phone;
                $data['ref'] = $trx_comp->trx_id;
                $data['token'] = $trx_comp->token;
                $data['amount'] = $trx_comp->amount;
                $data['vat_amount'] = $trx_comp->vatAmount;
                $data['vend_amount_kw_per_naira'] = $trx_comp->costOfUnit;
                $data['tariff_amount'] = $trx_comp->tariff_amount;
                $data['unit'] = $trx_comp->unitkwh;
                $data['title'] = "Clear Tamper Token";
                $data['date'] = date('d-m-y h:i:s');
                $data['meter_no'] = $trx_comp->meterNo;

                return view('admin/recepit.recepit', $data);


            }


        }


        if ($request->type == "clear_credit") {

            $trx_comp = ClearcreditToken::where('trx_id', $request->trx_id)->first() ?? null;
            $user_comp = User::where('id', $trx_comp->user_id)->first() ?? null;


            if ($trx_comp != null) {

                $data['full_name'] = $user_comp->first_name . " " . $user_comp->last_name;
                $data['address'] = $user_comp->address . "," . $user_comp->city . "," . $user_comp->state;
                $data['phone'] = $user_comp->phone;
                $data['ref'] = $trx_comp->trx_id;
                $data['token'] = $trx_comp->token;
                // UPDATED: Don't pass VAT, tariff amount, or unit for clear credit receipts
                // $data['amount'] = $trx_comp->amount;
                // $data['vat_amount'] = $trx_comp->vatAmount;
                // $data['tariff_amount'] = $trx_comp->tariff_amount;
                // $data['vend_amount_kw_per_naira'] = $trx_comp->costOfUnit;
                $data['title'] = "Clear Credit Token";
                $data['date'] = date('d-m-y h:i:s');
                $data['meter_no'] = $trx_comp->meterNo;


                return view('admin/recepit.recepit', $data);


            }


        }


        if ($request->type == "compensation") {

            $trx_comp = CompensationToken::where('trx_id', $request->trx_id)->first() ?? null;
            $user_comp = User::where('id', $trx_comp->user_id)->first() ?? null;


            if ($trx_comp != null) {

                $data['full_name'] = $user_comp->first_name . " " . $user_comp->last_name;
                $data['address'] = $user_comp->address . "," . $user_comp->city . "," . $user_comp->state;
                $data['phone'] = $user_comp->phone;
                $data['ref'] = $trx_comp->trx_id;
                $data['token'] = $trx_comp->token;
                $data['amount'] = 0;
                $data['vat_amount'] = $trx_comp->vatAmount;
                $data['tariff_amount'] = $trx_comp->tariff_amount;
                $data['vend_amount_kw_per_naira'] = $trx_comp->costOfUnit;
                $data['title'] = "Compensation Token";
                $data['date'] = date('d-m-y h:i:s');
                $data['meter_no'] = $trx_comp->meterNo;


                return view('admin/recepit.recepit', $data);


            }


        }

        if ($request->type == "kct_token") {
            $trx = KctToken::where('trx_id', $request->trx_id)->first() ?? null;
            $user = User::where('id', $trx->user_id)->first() ?? null;

            $data['full_name'] = $user->first_name . " " . $user->last_name;
            $data['address'] = $user->address . "," . $user->city . "," . $user->state;
            $data['phone'] = $user->phone;
            $data['ref'] = $trx->trx_id;
            $data['token1'] = $trx->kct_token1;
            $data['token2'] = $trx->kct_token2;
            $data['amount'] = $trx->amount;
            $data['meter_no'] = $trx->meterNo;
            $data['title'] = "kct_token";
            $data['date'] = date('d-m-y h:i:s');
            return view('admin/recepit.kct-recepit', $data);
        }


    }


    /**
     * Helper method to get and validate tariff_index from Tariff model
     *
     * @param int $tariff_id The tariff ID to look up
     * @return int|null The tariff_index if found and valid, null otherwise
     * @throws \Exception if tariff_index is null or tariff not found
     */
    private function getTariffIndexWithValidation($tariff_id)
    {
        if (!$tariff_id) {
            throw new \Exception('Tariff ID is required');
        }

        $tariff = Tariff::find($tariff_id);

        if (!$tariff) {
            throw new \Exception("Tariff not found for ID: {$tariff_id}. Please contact support.");
        }

        if ($tariff->tariff_index === null || $tariff->tariff_index === '') {
            throw new \Exception("Tariff Index is not set for tariff: {$tariff->title}. Please contact admin to set the tariff index (1-99) in the tariff configuration.");
        }

        return (int) $tariff->tariff_index;
    }


}
