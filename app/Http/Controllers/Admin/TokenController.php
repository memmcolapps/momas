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



    //Validate
    public function validate_compensation_meter(request $request)
    {


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
            $data['tarrif_index'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->t_index;




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
            $data['estate_name'] = $request->estate_id;
            $data['credit_tokens'] = CreditToken::latest()->where('estate_id', Auth::user()->estate_id)->paginate('50');
            $data['estate_id'] = Auth::user()->estate_id;
            $data['title'] = Estate::where('id', Auth::user()->estate_id)->first()->title;
            $data['tarrif_index'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->t_index;
            $data['preview'] = "on";


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
            $data['user'] = $user;
            $data['meter'] = $meter;
            $data['estate'] = Estate::where('id', $estate_id)->first();
            $data['amount'] = $request->amount;
            $data['vat'] = $vat;
            $data['estate_name'] = $request->estate_id;
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
            $data['tarrif_index'] = $tariffState->t_index ?? null;
            $data['credit_tokens'] = CreditToken::latest()->paginate('50');
            $data['estateFee'] = $estateFee;
            $data['fixedCharge'] = $fixedCharge;
            $data['serviceFee'] = $percn;

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


            $estate_id = Estate::where('id', $request->estate_id)->first()->id;
            $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
            $user = User::where('meterNo', $request->meterNo)->first() ?? null;

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
            $data['credit_tokens'] = CreditToken::latest()->paginate('50');
            $data['tarrif_index'] = $tariffState->t_index ?? null;
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
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
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
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
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
            $data['preview'] = "on";
            $data['amount'] = $request->amount + $fee;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
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
            $data['preview'] = "on";
            $data['amount'] = $request->amount + $fee;
            $data['vat'] = $vat;
            $data['estate_id'] = $estate_id;
            $data['estate_name'] = $request->estate_id;
            $data['tarrif_amount'] = TarrifState::where('tariff_id', $request->tariff_id)->first()->amount;
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

            // 3. Prepare token generation API payload using UNITS (not costOfUnit)
            $unitsKwh = $request->unit ?? 0;

            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo'   => $meter->meterNo,
                'sgc'       => (int)$meter->OldSGC,
                'ti'        => $request->t_index,
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

//            try {

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

            $databody = array(
                "amount" => $request->amount * 100,
                "email" => $customer_email,
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

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status;


            if ($status == true) {
                $trx = new Transaction();
                $trx->user_id = $request->user_id;
                $trx->pay_type = "paystack";
                $trx->amount = $request->amount;
                $trx->fee = $fee;
                $trx->trx_id = $trx_id;
                $trx->utility_id = $get_utility_id ?? null;
                $trx->payment_ref = $var->data->access_code ?? null;
                $trx->service_type = "credit_token";
                $trx->save();

                return redirect()->away($var->data->authorization_url);

            }

            $code = 422;
            $message = "Payment not available at the moment, Kindly select other payment option";
            return error($message, $code);

//            } catch (Exception $e) {
//                return back()->with('error', $e);
//            }

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
        $cdt->tariff_id = TarrifState::where('amount', $request->tariff_amount)->first()->tariff_id;
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

                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-check-web-tamper",
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
                    $trx->user_id = $request->user_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "credit_token";
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


        if ($request->pay_type == 'vend') {


            try {


                $meterNo = $request->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = TamperToken::where('trx_id', $trx_id)->first();
                $traff_id = TamperToken::where('trx_id', $trx_id)->first();


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $trx->tariff_id,
                    'sbc' => 5,
                    'amount' => 10, // Amount not needed for tamper tokens
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
                        TamperToken::where('trx_id', $trx_id)->update([
                            'token' => $no_kct_token,
                            'status' => 2
                        ]);

                        $trx_id = $trx_id;
                        $user = User::where('id', $trx->user_id)->first();
                        $email = $user->email;
                        $token = $no_kct_token;

                        send_email_kct_token($email, $token, $meterNo);


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "TAMPER TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
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
                return back()->with('error', $e);
            }
        }
    }

    public function generate_kctclear_token(request $request)
    {


        $est = Estate::where('id', $request->estate_name)->first();

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
        $cdt->tariff_id = TarrifState::where('amount', $request->tariff_amount)->first()->tariff_id;
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
            
                Log::info("Old : {$request->tariff_amount}" );
                Log::info("Meter SGC Data Types", [
    'OldSGC_Value' => $meter->OldSGC,
    'OldSGC_Type'  => gettype((int)$meter->OldSGC),
    'NewSGC_Value' => $meter->NewSGC,
    'NewSGC_Type'  => gettype((int)$meter->NewSGC),
]);


            // 3. Prepare KCT token generation payload
            $kctdatabody = [
                'meterType' => $meter->KRN1,
                'tometerType' => $meter->KRN2,
                'meterNo' => $request->meterNo,
                'sgc' => (int)$meter->OldSGC,
                'tosgc' => (int)$meter->NewSGC,
                // 'ti' => $request->tariff_amount ?? 0,
                'ti' => 7,
                'toti' => 1,
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
                    'status' => 3,
                ]);

                KctToken::where('trx_id', $trx_id)->update([
                    'status' => 3
                ]);

                return back()->with('error', 'Failed to connect to token generation service');
            }
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

                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-check-kct",
                    'split_code' => $est->paystack_subaccount,
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
                    $trx->user_id = $request->user_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "credit_token";
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
                                'status' => 3,
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


                    $databody = [
                        'meterType' => $meter->KRN2,
                        'meterNo' => $meter->meterNo,
                        'sgc' => (int)$meter->NewSGC,
                        'ti' => $trx->tariff_id,
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

                            Transaction::where('trx_id', $trx)->update([
                                'service' => "CLEAR CREDIT TOKEN PURCHASE",
                                'service_type' => "meter",
                                'status' => 3,
                                'tariff_id' => $request->tariff_id,
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

                $trx_id = "TRX" . random_int(0000000, 9999999);
                $email = Auth::user()->email;


                $databody = array(
                    "amount" => $request->amount * 100,
                    "email" => $email,
                    "ref" => $trx_id,
                    'callback_url' => url('') . "/admin/paystack-clear-credit",
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
                    $trx->user_id = $request->user_id;
                    $trx->pay_type = "paystack";
                    $trx->amount = $request->amount;
                    $trx->fee = $fee;
                    $trx->trx_id = $trx_id;
                    $trx->payment_ref = $var->data->access_code ?? null;
                    $trx->service_type = "credit_token";
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
        $cdt->save();


        $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
        if ($meter == null) {
            return back()->with('error', "meter not found");
        }

        $tariff_id = TarrifState::where('estate_id', $estate_id)->first()->tariff_id ?? null;
        if ($tariff_id == null) {
            return back()->with('error', "Tariff ID not set");
        }


        $databody = [
            'meterType' => $meter->KRN1 ?? "STS6",
            'meterNo' => $meter->meterNo,
            'sgc' => (int)$meter->NewSGC ?? 901102,
            'ti' => $request->t_index,
            'amount' => (int)$request->amount,
        ];


        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/tokenGen', $databody);
        $error = $response->json() ?? null;


        if ($response->successful()) {

            $get_token = $response->json();
            $token_data = json_decode($get_token, true);
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
                $trx->save();

                Transaction::where('trx_id', $trx)->update([
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

    public function paystack_verify_web(request $request)
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


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2, 'payment_ref' => $ref]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();


                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->NewSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => (float)$trx->unitkwh,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx)->update([
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

        if ($ck_transaction == 0) {

            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = CreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = CreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $user = User::where('meterNo', $meterNo)->first();


                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => (float)$trx->unitkwh,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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

    public function retry_generate_credit_token(request $request)
    {

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
                $status = $var->status ?? null;

                $ref =  $var->data->reference ?? null;
                $ref2 = $request->trx_id;


                $ck_transaction = Transaction::where('trx_id', $ref)->first()->status ?? null;



                if ($ck_transaction == null || $ck_transaction == 0) {


                    if ($status == 'success') {


                        Transaction::where('trx_id', $ref2)->update(['status' => 2, 'payment_ref' => $ref]);
                        $meterNo = CreditToken::where('trx_id', $ref2)->first()->meterNo;
                        $meter = Meter::where('meterNo', $meterNo)->first();
                        $trx = CreditToken::where('trx_id', $ref2)->first();
                        $traff_id = CreditToken::where('trx_id', $ref2)->first();


                        $databody = [
                            'meterType' => $meter->KRN2,
                            'meterNo' => $meter->meterNo,
                            'sgc' => (int)$meter->NewSGC,
                            'ti' => $trx->tariff_id,
                            'amount' => (float)$trx->unitkwh,
                        ];


                        $url = "http://169.239.189.91:19071/tokenGen";

                        $no_kct_response = Http::withOptions([
                            'verify' => false,
                            'timeout' => 10,
                        ])->post($url, $databody);
                        $error = $no_kct_response->json() ?? null;


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


                                Transaction::where('trx_id', $trx)->update([
                                    'status' => 2,
                                ]);

                                $type = "credit_token";
                                return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                            } else {

                                Transaction::where('trx_id', $trx)->update([
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
                        return back()->with('error', "Payment not found or failed on Paystack, Please try again");
                    }

                }

                if ($ck_transaction == 0) {

                    if ($status == 'success') {


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


                                Transaction::where('trx_id', $trx)->update([
                                    'status' => 2,
                                ]);

                                return redirect("admin/recepit?trx_id=$trx_id");

                            } else {

                                Transaction::where('trx_id', $trx)->update([
                                    'service' => "METER PURCHASE",
                                    'service_type' => "meter",
                                    'status' => 3,
                                    'tariff_id' => $request->tariff_id,
                                    'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                                ]);

                                User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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


        $ck_transaction = Transaction::where('trx_id', $ref)->first()->status ?? null;


        if ($ck_transaction == null) {

            if ($status == 'success') {

                $meterNo = KctToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = KctToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = KctToken::where('trx_id', $var->data->metadata->ref)->first();
                $ck_transaction = Transaction::where('trx_id', $var->data->metadata->ref)->first()->status ?? null;


                if ($ck_transaction == null || $ck_transaction == 0) {

                    if ($status == 'success') {
                        $meterNo = KctToken::where('trx_id', $ref)->first()->meterNo;
                        $meter = Meter::where('meterNo', $meterNo)->first();
                        $trx = KctToken::where('trx_id', $ref)->first();
                        $traff_id = KctToken::where('trx_id', $ref)->first();

                        if ($meter != null && $meter->NeedKCT == "on") {
                            $databody = [
                                'meterType' => $meter->KRN2,
                                'meterNo' => $meterNo,
                                'sgc' => (int)$meter->OldSGC,
                                'ti' => $trx->tariff_id, //TRARRRIF INDEX
                                'amount' => (float)$trx->costOfUnit,
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

                                    Transaction::where('trx_id', $trx)->update([
                                        'service' => "METER PURCHASE",
                                        'service_type' => "meter",
                                        'status' => 3,
                                        'tariff_id' => $request->tariff_id,
                                        'note' => json_encode($kct_data) . "|" . json_encode($databody)


                                    ]);

                                    User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                                    return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $kct_response->json() . " | " . json_encode($databody));

                                }


                            }

                        }
                    }
                }


                return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $kct_response->json() . " | " . json_encode($databody));


            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'sbc' => 5,
                    'amount' => (int)$trx->costOfUnit,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "TAMPER TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)
                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


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


                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'sbc' => 5,
                    'amount' => (int)$trx->costOfUnit,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "CLEAR TAMPER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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


        $ck_transaction = Transaction::where('trx_id', $var->data->reference)->first()->status ?? null;
        if ($ck_transaction == null || $ck_transaction == 0) {
            if ($status == 'success') {


                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $meterNo = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first()->meterNo;
                $meter = Meter::where('meterNo', $meterNo)->first();
                $trx = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $traff_id = ClearcreditToken::where('trx_id', $var->data->metadata->ref)->first();
                $amount = (float)number_format((float)$trx->tariffPerKWatt, 2, '.', '');


                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'sbc' => 1,
                    'amount' => $amount,
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

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "CLEAR CREDIT TOKEN PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        $type = "credit_token";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => (int)$trx->costOfUnit,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        $type = "tamper";
                        return redirect("admin/recepit?trx_id=$trx_id&type=$type");


                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


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


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'sbc' => 5,
                    'amount' => $trx->costOfUnit,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");

                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
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


                $databody = [
                    'meterType' => $meter->KRN2,
                    'meterNo' => $meter->meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $trx->tariff_id,
                    'amount' => (float)number_format((float)$trx->costOfUnit, 2, '.', ''),
                    "sbc" => 1,
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


                        Transaction::where('trx_id', $trx)->update([
                            'status' => 2,
                        ]);

                        return redirect("admin/recepit?trx_id=$trx_id");


                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'note' => json_encode($no_kct_data) . "|" . json_encode($databody)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


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
                    $databody = [
                        'meterType' => $meter->KRN2,
                        'meterNo' => Auth::user()->meterNo,
                        'sgc' => (int)$meter->OldSGC,
                        'ti' => $trx->tariff_id, //TRARRRIF INDEX
                        'amount' => (int)$trx->costOfUnit,
                    ];
                    $response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->post('http://169.239.189.91:19071/tokenGen', $databody);

                    if ($response->successful()) {
                        $gdata = $response->json();
                        $data = json_decode($gdata, true);
                        $status = $data['code'] ?? null;

                        if ($status == "SUCCESS") {
                            $token = $data['tokens'][0];

                            $kctdatabody = [
                                'meterType' => $meter->KRN1,
                                'tometerType' => $meter->KRN1,
                                'meterNo' => Auth::user()->meterNo,
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

                                    Transaction::where('trx_id', $trx)->update([
                                        'service' => "METER PURCHASE",
                                        'service_type' => "meter",
                                        'status' => 3,
                                        'tariff_id' => $request->tariff_id,
                                        'note' => json_encode($kct_data) . "|" . json_encode($databody)


                                    ]);

                                    User::where('id', Auth::id())->increment('main_wallet', $trx->amount);


                                    return redirect('admin/credit-token')->with('error', $error['errors'][0]['title'] ?? $response->json() . " | " . json_encode($databody));

                                }


                            }


                        } else {

                            return response()->json([
                                'status' => false,
                                'message' => "Meter vending failed, Retry again using your wallet"
                            ], 422);

                        }

                    }

                }
            }
        }
    }


    public
    function recepit(request $request)
    {


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
                $data['amount'] = $trx_comp->amount;
                $data['vat_amount'] = $trx_comp->vatAmount;
                $data['tariff_amount'] = $trx_comp->tariff_amount;
                $data['vend_amount_kw_per_naira'] = $trx_comp->costOfUnit;
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


}
