<?php

namespace App\Http\Controllers;

use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\Merchant;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\PosLog;
use App\Models\SpreadPayment;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use App\Models\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PosController extends Controller
{


    public function index(request $request)
    {

        if (Auth::user()->role == 0) {


            $data['merchants'] = Merchant::all();
            $data['total_merchants'] = Merchant::count();
            return view('admin.pos.index', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    public function new_merchant(request $request)
    {

        if (Auth::user()->role == 0) {

            return view('admin.pos.new-merchant');


        } elseif (Auth::user()->role == 1) {

        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function add_merchant(request $request)
    {

        $terminal = Merchant::where('serial_no', $request->serial_no)->first() ?? null;
        if ($terminal == null) {
            $ter = new Merchant();
            $ter->first_name = $request->first_name;
            $ter->last_name = $request->last_name;
            $ter->phone_no = $request->phone_no;
            $ter->serial_no = $request->serial_no;
            $ter->tid = $request->tid;
            $ter->state = $request->state;
            $ter->city = $request->city;
            $ter->status = 2;
            $ter->save();

            try {


                $curl = curl_init();

                $databody = array(
                    'serial_no' => $request->serial_no,
                    'tid' => $request->tid,
                );

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://enkpayapp.enkwave.com/api/register-pos',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $databody,
                ));

                $var = curl_exec($curl);
                curl_close($curl);
                $var = json_decode($var);


            } catch (\Exception $th) {
                return $th->getMessage();
            }


            return back()->with('message', 'Merchant has been created successfully');


        } else {
            return back()->with('error', 'Serial No already exist');
        }


    }

    public function delete_merchant(request $request)
    {

        Merchant::where('id', $request->id)->delete();

        return back()->with('message', 'Merchant has been deleted successfully');


    }


    public function validate_meter(request $request)
    {
        $user = User::where('meterNo', $request->meterNo)->first() ?? null;

        $meter = Meter::where('meterNo', $request->meterNo)->where('estate_id', $request->estateId)->first() ?? null;
        if ($meter == null) {
            $message = "Validation Failed, please check meter number or estate selected";
            $code = 422;
            return error($message, $code);
        }


        $get_tar = Tariff::where('user_id', $user->id)->first() ?? null;
        if ($get_tar == null) {
            $message = "Tariff not properly configured";
            $code = 422;
            return error($message, $code);
        }


        $data['customer_name'] = $user->first_name . " " . $user->last_name;
        $data['address'] = $user->address . ", " . $user->city . ", " . $user->state;
        // $data['meter_type'] = $meter_type;

        $es_id = $request->estateId ?? null;
        $duration = Estate::where('id', $es_id)->first()->duration ?? null;
        $estate_id = $es_id;
        $user_id = $user->id;

        if ($duration == null || $estate_id == null) {
            $minvend = "Not set";

        } else {

            $sp = SpreadPayment::where('user_id', $user->id)->where('estate_id', $es_id)->first()->percentage ?? null;
            if ($sp != null) {
                $percentage = $sp / 100;
                $vend = vend($duration, $estate_id, $user_id);
                $get_vend = $percentage * $vend;
                $minvend = $get_vend;

            } else {


                $get_vend = vend($duration, $estate_id, $user_id);
                if ($get_vend == null) {
                    $minvend = "Not set";
                } else {
                    $minvend = $get_vend;
                }

            }

        }

        $min_pur = Estate::where('id', $request->estateId)->first()->min_pur ?? null;
        $max_pur = Estate::where('id', $request->estateId)->first()->max_pur ?? null;
        $data['min_purchase'] = (int)$min_pur;
        $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
            ->where('user_id', $user->id)
            ->get();
        foreach ($tariffs as $tariff) {
            $tariffState = TarrifState::where('tariff_id', $tariff->id)->first();
            $tariff->amount = $tariffState ? $tariffState->amount : null;
            $tariff->vat = $tariffState ? $tariffState->vat : null;

        }


        $data['tariffs'] = $tariffs;
        $pur['min_purchase'] = (int)$min_pur;
        $pur['max_purchase'] = (int)$max_pur;
        $pur['min_vending'] = (int)$minvend;
        $data['purchase'] = $pur;

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);


    }


    public function buy_meter_token(request $request)
    {

        $utility_amount = $request->utility_amount;
        $SerialNo = $request->header('serialnumber');
        $RRN = $request->RRN;
        $STAN = $request->STAN;
        $acquiringInstitutionIdCode = $request->acquiringInstitutionIdCode;
        $authCode = $request->authCode;
        $cardCardSequenceNum = $request->cardCardSequenceNum;
        $cardExpireData = $request->cardExpireData;
        $forwardingInstCode = $request->forwardingInstCode;
        $amount = $request->institutionData['amount'];
        $tid = $request->institutionData['tid'];
        $pan = $request->pan;
        $pinBlock = $request->pinBlock;
        $receiptNumber = $request->receiptNumber;
        $respCode = $request->respCode;
        $responseMessage = $request->responseMessage;
        $posstatus = $request->status;
        $successResponse = $request->successResponse;
        $systemTraceAuditNo = $request->systemTraceAuditNo;
        $terminalId = $request->terminalId;
        $transactionDattae = $request->transactionDate;
        $transactionDateTime = $request->transactionDateTime;
        $transactionTime = $request->transactionTime;
        $transactionType = $request->transactionType;
        $cardName = $request->cardName;
        $userID = $request->UserID;
        $estate_id = $request->meter_info['estate_id'];
        $vending_amount = $request->meter_info['vending_amount'];
        $meterNo = $request->meter_info['meter_no'];
        $vend_amount_kw_per_naira = $request->meter_info['vend_amount_kw_per_naira'];
        $vat_amount = $request->meter_info['vat_amount'];
        $total_paid = $request->meter_info['total_paid_amount'];


        if ($meterNo == null) {
            return response()->json([
                'status' => false,
                'message' => 'Meter No can not be null',
            ], 422);
        }


        $estate = Estate::where('id', $estate_id)->first();
        $mer_id = Merchant::where('serial_no', $SerialNo)->first()->id ?? null;
        $tariff_id = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->tariff_id ?? null;
        $meter = Meter::where('MeterNo', $meterNo)->first() ?? null;
        $user = User::where('meterNo', $meterNo)->first() ?? null;

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Meter Not attached to user',
            ], 422);
        }



        $ck_trx = PosLog::where('rrn', $RRN)->first()->status ?? null;
        if($ck_trx == 2){
            return response()->json([
                'status' => false,
                'message' => 'Transaction already successful',
            ], 422);
        }


        $logs = new PosLog();
        $logs->rrn = $RRN;
        $logs->estate_id = $estate_id;
        $logs->cardName = $cardName;
        $logs->amount = $amount;
        $logs->STAN = $STAN;
        $logs->serialNO = $SerialNo;
        $logs->expireDate = $cardExpireData;
        $logs->responseMessage = $responseMessage;
        $logs->pan = $pan;
        $logs->responseCode = $respCode;
        $logs->terminalID = $tid;
        $logs->trx_date = $transactionDateTime;
        $logs->status = 0;
        $logs->trx_time = $transactionTime;
        $logs->save();


        if ($meter != null && $meter->NeedKCT == "on") {

            try {

                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $tariff_id, //TRARRRIF INDEX
                    'amount' => $vend_amount_kw_per_naira,
                ];
                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);

                if ($response->successful()) {
                    $gdata = $response->json();
                    $data = json_decode($gdata, true);
                    $statusm = $data['code'] ?? null;

                    if ($posstatus == "00" && $statusm == "SUCCESS") {
                        $token = $data['tokens'][0];
                        $kctdatabody = [
                            'meterType' => $meter->KRN1,
                            'tometerType' => $meter->KRN1,
                            'meterNo' => $meterNo,
                            'sgc' => (int)$meter->OldSGC,
                            'tosgc' => (int)$meter->NewSGC,
                            'ti' => $tariff_id,
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

                                $cdt = new CreditToken();
                                $cdt->user_id = 1;
                                $cdt->trx_id = $RRN;
                                $cdt->meterNo = $meterNo;
                                $cdt->amount = $total_paid ?? 0;
                                $cdt->vat = $vat_amount ?? 0;
                                $cdt->estate_name = Estate::where('id', $estate_id)->first()->title ?? "NAME";
                                $cdt->estate_id = $estate_id;
                                $cdt->tariff_id = $tariff_id;
                                $cdt->vatAmount = $vat_amount;
                                $cdt->costOfUnit = $vending_amount;
                                $cdt->tariffPerKWatt = $vend_amount_kw_per_naira;
                                $cdt->save();

                                $met = new MeterToken();
                                $met->user_id = $user->id;
                                $met->trx_id = $RRN;
                                $met->meterNo = $meterNo;
                                $met->token = $token;
                                $met->amount = $total_paid ?? 0;
                                $met->unit = $vend_amount_kw_per_naira;
                                $met->kct_tokens = $kct_data['tokens'][0] . "," . $kct_data['tokens'][1];
                                $met->vat = $vat_amount;
                                $met->estate_id = $estate_id;
                                $met->status = 2;
                                $met->save();


                                $trx = new Transaction();
                                $trx->trx_id = $RRN;
                                $trx->user_id = $user->id;
                                $trx->service = "METER PURCHASE POS";
                                $trx->service_type = "meter";
                                $trx->unit_amount = $vending_amount;
                                $trx->tariff_id = $tariff_id;
                                $trx->save();


                                $data2['full_name'] = $user->first_name . " " . $user->last_name;
                                $data2['address'] = $user->address . "," . $user->city . "," . $user->state;
                                $data2['service'] = "MOMAS METER";
                                $data2['trx_id'] = $trx;
                                $data2['token'] = $token;
                                $data2['amount'] = $total_paid;
                                $data2['vending_amount'] = $vending_amount;
                                $data2['vend_amount_kw_per_naira'] = $vend_amount_kw_per_naira;
                                $data2['kct_token1'] = $kct_data['tokens'][0];
                                $data2['kct_token2'] = $kct_data['tokens'][1];
                                $data2['vat_amount'] = $vat_amount;


                                PosLog::where('rrn', $RRN)->first()->update([
                                    'token' => $token,
                                    'kct_token' => $kct_data['tokens'][0] . " " . $kct_data['tokens'][1],
                                    'vending_amount' => $vending_amount,
                                    'vat_amount' => $vat_amount,
                                    'vend_amount_kw_per_naira' => $vend_amount_kw_per_naira,
                                    'meter_no' => $meterNo,
                                    'address' => $data['address'],
                                    'name' => $data['full_name'],
                                    'merchant_id' => $mer_id,


                                    'status' => 2,
                                ]);


                                return response()->json([
                                    'newTransaction' => [
                                        'success' => true,
                                        'transaction' => $logs,
                                    ],
                                    'message' => "Transaction successfully",
                                    'meter' => $data2 ?? null
                                ], 200);


                            }
                        } else {

                            $logs = new PosLog();
                            $logs->rrn = $RRN;
                            $logs->estate_id = $estate_id;
                            $logs->cardName = $cardName;
                            $logs->amount = $amount;
                            $logs->STAN = $STAN;
                            $logs->serialNO = $SerialNo;
                            $logs->expireDate = $cardExpireData;
                            $logs->responseMessage = "Money Funded but meter not funded";
                            $logs->pan = $pan;
                            $logs->responseCode = "03";
                            $logs->terminalID = $tid;
                            $logs->trx_date = $transactionDateTime;
                            $logs->status = 3;
                            $logs->trx_time = $transactionTime;
                            $logs->meterNo = $meterNo;
                            $logs->vend_amount_kw_per_naira = $vend_amount_kw_per_naira;
                            $logs->vat_amount = $vat_amount;
                            $logs->save();


                            return response()->json([

                                'status' => false,
                                'message' => "Vending server not connected, Retry again on transaction history",
                            ], 422);
                        }

                    }


                } else {

                    return response()->json([
                        'status' => false,
                        'message' => "Meter vending failed, Retry again using your wallet"
                    ], 422);

                }


            } catch (Exception $e) {
            Logger::error('PosController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);

            }

        }


        if ($meter != null && $meter->NeedKCT == null) {

            try {

                $databody = [
                    'meterType' => $meter->KRN1,
                    'meterNo' => $meterNo,
                    'sgc' => (int)$meter->OldSGC,
                    'ti' => $tariff_id,
                    'amount' => $vend_amount_kw_per_naira,
                ];
                $no_kct_response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 10,
                ])->post('http://169.239.189.91:19071/tokenGen', $databody);


                if ($no_kct_response->successful()) {
                    $no_kct = $no_kct_response->json();
                    $no_kct_data = json_decode($no_kct, true);
                    $statusm = $no_kct_data['code'] ?? null;

                    if ($posstatus == "00" && $statusm == "SUCCESS") {

                        $no_kct_token = $no_kct_data['tokens'][0];
                        $vat = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first()->amount ?? 0;
                        $met = new MeterToken ();
                        $met->user_id = $user->id;
                        $met->trx_id = $RRN;
                        $met->meterNo = $meterNo;
                        $met->token = $no_kct_token;
                        $met->amount = $vending_amount;
                        $met->unit = $vend_amount_kw_per_naira;
                        $met->vat = $vat;
                        $met->estate_id = $estate_id;
                        $met->status = 2;
                        $met->save();

                        $trx = new Transaction();
                        $trx->user_id = $user->id;
                        $trx->trx_id = $RRN;
                        $trx->service = "METER PURCHASE POS";
                        $trx->service_type = "meter";
                        $trx->unit_amount = $vending_amount;
                        $trx->tariff_id = $tariff_id;
                        $trx->save();


                        $data['full_name'] = $user->first_name . " " . $user->last_name;
                        $data['address'] = $user->address . "," . $user->city . "," . $user->state;
                        $data['service'] = "MOMAS METER";
                        $data['trx_id'] = $RRN;
                        $data['token'] = $no_kct_data['tokens'][0];
                        $data['amount'] = $total_paid;
                        $data['vending_amount'] = $vending_amount;
                        $data['vat_amount'] = $vat_amount;
                        $data['vend_amount_kw_per_naira'] = $vend_amount_kw_per_naira;
//                        $email = $user->email;
//                        $token = $no_kct_data['tokens'][0];
//                        send_email_token($email, $token, $amount);


                        PosLog::where('rrn', $RRN)->first()->update([
                            'token' => $no_kct_data['tokens'][0],
                            'vending_amount' => $vending_amount,
                            'vat_amount' => $vat_amount,
                            'vend_amount_kw_per_naira' => $vend_amount_kw_per_naira,
                            'meter_no' => $meterNo,
                            'address' => $data['address'],
                            'name' => $data['full_name'],
                            'merchant_id' => $mer_id,
                            'status' => 2,
                        ]);


                        return response()->json([
                            'newTransaction' => [
                                'success' => true,
                                'transaction' => $logs,
                            ],
                            'message' => "Transaction successfully",
                            'meter' => $data ?? null
                        ], 200);


                    } else {


                        $logs = new PosLog();
                        $logs->rrn = $RRN;
                        $logs->estate_id = $estate_id;
                        $logs->cardName = $cardName;
                        $logs->amount = $amount;
                        $logs->STAN = $STAN;
                        $logs->serialNO = $SerialNo;
                        $logs->expireDate = $cardExpireData;
                        $logs->responseMessage = "Money Funded but meter not funded";
                        $logs->pan = $pan;
                        $logs->responseCode = "03";
                        $logs->terminalID = $tid;
                        $logs->trx_date = $transactionDateTime;
                        $logs->status = 3;
                        $logs->trx_time = $transactionTime;
                        $logs->meterNo = $meterNo;
                        $logs->vend_amount_kw_per_naira = $vend_amount_kw_per_naira;
                        $logs->vat_amount = $vat_amount;
                        $logs->save();


                        return response()->json([
                            'status' => false,
                            'message' => "Vending server not connected, Retry again on transaction history",
                        ], 422);

                    }


                }

            } catch (Exception $e) {
            Logger::error('PosController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);

            }


        }


        return response()->json([

            'status' => false,
            'message' => "Something went wrong, Contact our support",
        ], 422);


    }


    public function retry_meter_token(request $request)
    {

        $amount = $request->amount;
        $meterNo = $request->meterNo;
        $trx_id = $request->trxref;


        $trx = Transaction::where('trx_id', $trx_id)->first() ?? null;
        if ($trx == null) {
            return response()->json([
                'status' => false,
                'message' => "Transaction not found, contact our support for more support",
            ], 422);
        }

        if ($trx->status == 2) {
            return response()->json([
                'status' => false,
                'message' => "Transaction already successful, contact our support for more support",
            ], 422);

        }

        $tariff_index = Tariff::where('id', $trx->tariff_id)->first()->tariff_index ?? null;
        $user = User::where('id', $trx->user_id)->first();
        $meter = Meter::where('user_id', $trx->user_id)->first();

        User::where('id', $user->id)->decrement('main_wallet', $trx->unit_amount);


        if ($meter != null && $meter->NeedKCT == "on") {
            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->OldSGC,
                'ti' => $tariff_index, //TRARRRIF INDEX
                'amount' => $trx->unit_amount,
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
                        'meterNo' => $meter->meterNo,
                        'sgc' => (int)$meter->OldSGC,
                        'tosgc' => (int)$meter->NewSGC,
                        'ti' => $tariff_index,
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

                            $met = new MeterToken ();
                            $met->user_id = $trx->user_id;
                            $met->trx_id = $trx->trx_id;
                            $met->meterNo = $meter->meterNo;
                            $met->token = $token;
                            $met->amount = $trx->amount;
                            $met->unit = $trx->unit_amount;
                            $met->kct_tokens = $kct_data['tokens'][0] . "," . $kct_data['tokens'][1];
                            $met->vat = $trx->vat;
                            $met->estate_id = $user->estate_id;
                            $met->status = 2;
                            $met->save();

                            Transaction::where('trx_id', $trx_id)->update(['status' => 2, 'note' => "Tokens generated successfully"]);


                            $data2['full_name'] = $user->first_name . " " . $user->last_name;
                            $data2['address'] = $user->address . "," . $user->city . "," . $user->state;
                            $data2['service'] = "MOMAS METER";
                            $data2['trx_id'] = $trx_id;
                            $data2['token'] = $token;
                            $data2['amount'] = "$trx->amount";
                            $data2['vending_amount'] = "$trx->vending_amount";
                            $data2['vend_amount_kw_per_naira'] = "$trx->unit_amount";
                            $data2['kct_token1'] = $kct_data['tokens'][0];
                            $data2['kct_token2'] = $kct_data['tokens'][1];
                            $data2['vat_amount'] = "$trx->vat";


                            $email = $user->email;
                            $kct_token = $kct_data['tokens'];
                            $kct_token1 = $kct_token[0];
                            $kct_token2 = $kct_token[1];

                            send_kct_email_token($email, $token, $amount, $kct_token1, $kct_token2);

                            return response()->json([
                                'status' => true,
                                'data' => $data2
                            ], 200);


                        }
                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service_type' => "token Purchase",
                            'service' => "Meter",
                            'status' => 3,
                            'note' => json_encode($kct_response)


                        ]);

                        User::where('id', Auth::id())->increment('main_wallet', $trx->unit_amount);

                        return response()->json([
                            'status' => false,
                            'message' => "Vending server not connected, Retry again later using your wallet",
                        ], 422);
                    }

                }


            } else {

                return response()->json([
                    'status' => false,
                    'message' => "Meter vending failed, Retry again on transaction history"
                ], 422);

            }

        }


        if ($meter != null && $meter->NeedKCT == null) {

            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->OldSGC,
                'ti' => 1,
                'amount' => $trx->unit_amount,
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
                    $met = new MeterToken ();
                    $met->user_id = $trx->user_id;
                    $met->trx_id = $trx->trx_id;
                    $met->meterNo = $meter->meterNo;
                    $met->token = $no_kct_token;
                    $met->amount = $trx->amount;
                    $met->unit = $trx->unit_amount;
                    $met->vat = $trx->vat;
                    $met->estate_id = $user->estate_id;
                    $met->status = 2;
                    $met->save();

                    Transaction::where('trx_id', $trx_id)->update(['status' => 2, 'note' => "Tokens generated successfully"]);


                    $data['full_name'] = $user->first_name . " " . $user->last_name;
                    $data['address'] = $user->address . "," . $user->city . "," . $user->state;
                    $data['service'] = "MOMAS METER";
                    $data['trx_id'] = $trx_id;
                    $data['token'] = $no_kct_token;
                    $data['amount'] = "$trx->amount";
                    $data['vending_amount'] = "$trx->vending_amount";
                    $data['vend_amount_kw_per_naira'] = "$trx->unit_amount";
                    $data['vat_amount'] = "$trx->vat";

                    $email = $user->email;

                    send_email_token($email, $no_kct_token, $amount);

                    return response()->json([
                        'status' => true,
                        'data' => $data
                    ], 200);


                } else {


                    Transaction::where('trx_id', $trx_id)->update([
                        'service_type' => "token Purchase",
                        'service' => "Meter",
                        'status' => 3,
                        'note' => json_encode($no_kct_response)
                    ]);

                    User::where('id', Auth::id())->increment('main_wallet', $trx->amount);
                    return response()->json([
                        'status' => false,
                        'message' => "Meter vending failed, Retry again on transaction history"
                    ], 422);


                }


            }


        }

        return response()->json([

            'status' => false,
            'message' => "Something went wrong, Contact our support",
        ], 422);


    }


    public function get_all_transaction(request $request)
    {

        date_default_timezone_set('UTC');

        if ($request->rrn != null) {

            $SerialNo = $request->header('serialnumber');
            $data = PosLog::where('RRN', $request->rrn)->get()->makeHidden(['created_at', 'updated_at']) ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'allTransaction' => $data,
                ], 200);
            }


        }

        if ($request->startofday == null && $request->endofday == null) {

            $SerialNo = $request->header('serialnumber');
            $data = PosLog::where('SerialNo', $SerialNo)->get()->makeHidden(['created_at', 'updated_at']) ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'allTransaction' => $data,
                ], 200);
            }


        }


        if ($request->startofday != null && $request->endofday == null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::latest()->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->startofday)->get()->makeHidden(['created_at', 'updated_at']) ?? null;

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'allTransaction' => $data,
                ], 200);
            }

        }


        if ($request->startofday == null && $request->endofday != null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::latest()->where('SerialNo', $SerialNo)->whereDate('createdAt', $request->endofday)->get()->makeHidden(['created_at', 'updated_at']) ?? null;

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'allTransaction' => $data,
                ], 200);
            }

        }


        if ($request->startofday != null && $request->endofday != null) {
            $SerialNo = $request->header('serialnumber');
            $data = PosLog::where('SerialNo', $SerialNo)->whereBetween('createdAt', [$request->startofday . ' 00:00:00', $request->endofday . ' 23:59:59'])->get()->makeHidden(['created_at', 'updated_at']) ?? null;
            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'transaction' => [],
                    'allTransaction' => null,
                    'totalSuccessAmount' => null,
                    'totalFailedAmount' => null,
                    'totalTransactionAmount' => null,
                    'message' => "No Record Found",
                    'error' => null,
                ], 200);

            } else {

                return response()->json([
                    'success' => true,
                    'allTransaction' => $data,
                ], 200);
            }


        }

        return response()->json([
            'success' => false,
            'transaction' => [],
            'allTransaction' => null,
            'totalSuccessAmount' => null,
            'totalFailedAmount' => null,
            'totalTransactionAmount' => null,
            'message' => "Something Went Wrong",
            'mid' => null,
            'merchantDetails' => [
                'merchantName' => null,
                'serialnumber' => null,
                'mid' => null,
                'tid' => null,
                'merchantaddress' => null
            ],
            'meter' => [],
            'error' => true,
        ], 200);


    }

}
