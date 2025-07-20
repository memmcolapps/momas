<?php


use App\Models\Meter;
use App\Models\OauthAccessToken;
use App\Models\Token;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Models\Utitlity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;


if (!function_exists('token')) {

    function token()
    {

        $email = env('ENKPAYEMAIL');
        $passsword = env('ENKPAYPASSWORD');


        $databody = array(
            "email" => $email,
            "password" => $passsword,

        );

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.sprintpay.online/api/auth',
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
                'Content-Type: application/json'
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;


        if ($status == true) {
            return $var->Authorization;
        } else {

            $message = $var;
            send_notification($message);
            return false;
        }
    }


}


if (!function_exists('get_balance')) {

    function get_balance()
    {

        $email = env('ENKPAYEMAIL');
        $passsword = env('ENKPAYPASSWORD');


        $databody = array(
            "email" => $email,
            "password" => $passsword,

        );

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://enkpayapp.enkwave.com/api/email-login',
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
                'Content-Type: application/json'
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;


        if ($status == false) {
            return $var->data->main_wallet;
        } else {

            $message = $var;
            send_notification($message);
            return false;
        }
    }


}


if (!function_exists('error')) {

    function error($message, $code)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }
}


if (!function_exists('success')) {

    function success($message)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], 200);
    }
}


if (!function_exists('user')) {

    function user()
    {
        $user = User::where('id', Auth::id())->first()->makeHidden(['created_at', 'updated_at']);
        return $user;
    }
}


if (!function_exists('validate_code')) {

    function validate_code($code, $email)
    {
        $get_code = User::where('email', $email)->first()->code;
        if ($get_code == $code) {
            $vv = User::where('email', $email)->where('code', $code)->update(['status' => 1]);
            return 0;
        } else {
            return 3;
        }
    }
}


if (!function_exists('meter')) {

    function meter()
    {

        $ck_meter = Meter::where('user_id', Auth::id())->first() ?? null;

        if ($ck_meter == null) {
            return [];
        }


        $meter = Meter::where('user_id', Auth::id())->first()->makeHidden(['created_at', 'updated_at']) ?? [];
        return $meter;
    }
}


if (!function_exists('flush_token')) {

    function flush_token()
    {
        $get_token = OauthAccessToken::where('user_id', Auth::id())->first()->user_id ?? null;
        if ($get_token != null) {
            OauthAccessToken::where('user_id', Auth::id())->delete();
        }

        return 0;

    }
}


if (!function_exists('total_utility')) {

    function total_utility($estate_id)
    {

        $total_utility = Utitlity::where('estate_id', $estate_id)->sum('amount');
        return $total_utility;

    }
}


if (!function_exists('vend')) {

    function vend($duration, int $estate_id, int $user_id)
    {


        $totalDue = total_utility($estate_id);
        $paymentsQuery = UtilitiesPayment::where([
            'user_id'   => $user_id,
            'estate_id' => $estate_id,
            'type'      => 'utilities',
            'status'    => 0,
        ]);




        switch ($duration) {
            case 'daily':
                $paymentsQuery->whereDate('created_at', Carbon::today());
                break;

            case 'weekly':
                $paymentsQuery->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;

            case 'monthly':
                $paymentsQuery->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ]);
                break;

            case 'yearly':
                $paymentsQuery->whereBetween('created_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear(),
                ]);
                break;

            case 'per_transaction':
                break;

            default:
                return $totalDue;
        }

        $paidSoFar = $paymentsQuery->sum('amount');

        if ($duration === 'per_transaction') {
            return $totalDue;
        }




        return $paidSoFar;


    }
}


if (!function_exists('send_login_code')) {

    function send_login_code($email, $code)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "One Time Password",
            'toreceiver' => $email,
            'code' => $code,
            'user' => $first_name,
        );

        Mail::send('emails.loginotp', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


function payment_email($email, $type, $amount, $duration)
{
    $first_name = User::where('email', $email)->first()->first_name;
    $data = array(
        'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
        'subject' => "Payment Notification",
        'toreceiver' => $email,
        'type' => $type,
        'amount' => $amount,
        'duration' => $duration,
        'user' => $first_name,
    );


    Mail::send('emails.payment', ["data1" => $data], function ($message) use ($data) {
        $message->from($data['fromsender']);
        $message->to($data['toreceiver']);
        $message->subject($data['subject']);
    });


    return 0;


}


if (!function_exists('send_reset_email_notification')) {

    function send_reset_email_notification($email)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "Password Reset Notification",
            'toreceiver' => $email,
            'user' => $first_name,
        );

        Mail::send('emails.reset-notification', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


if (!function_exists('send_email')) {

    function send_email($email, $sms_code)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "One Time Password",
            'toreceiver' => $email,
            'sms_code' => $sms_code,
            'user' => $first_name ?? "USER",
        );

        Mail::send('emails.email', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}

if (!function_exists('send_email_reset')) {

    function send_email_reset($email, $sms_code)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "One Time Password",
            'toreceiver' => $email,
            'sms_code' => $sms_code,
            'user' => $first_name,
        );

        Mail::send('emails.reset', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


if (!function_exists('send_email_token')) {

    function send_email_token($email, $token, $amount)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "token Purchase",
            'toreceiver' => $email,
            'token' => $token,
            'user' => $first_name,
            'amount' => $amount
        );

        Mail::send('emails.vendtoken', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


if (!function_exists('send_email_token_others')) {

    function send_email_token_others($email, $token, $meterNo, $title)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "token Purchase",
            'toreceiver' => $email,
            'token' => $token,
            'user' => $first_name,
            'meterNo' => $meterNo,
            'title' => $title
        );

        Mail::send('emails.vendtokenothers', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}
if (!function_exists('send_email_kct_token')) {

    function send_email_kct_token($email, $token, $meterNo)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "token Purchase",
            'toreceiver' => $email,
            'token' => $token,
            'user' => $first_name,
            'meterNo' => $meterNo
        );

        Mail::send('emails.vendkcttokenothers', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


if (!function_exists('send_kct_email_token')) {

    function send_kct_email_token($email, $token, $amount, $kct_token1, $kct_token2)
    {
        $first_name = User::where('email', $email)->first()->first_name;
        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "token Purchase",
            'toreceiver' => $email,
            'token' => $token,
            'kct_token1' => $kct_token1,
            'kct_token2' => $kct_token2,
            'user' => $first_name,
            'amount' => $amount
        );

        Mail::send('emails.vendkcttoken', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });


        return 0;

    }
}


if (!function_exists('send_token_email')) {

    function send_token_email($email, $token_code, $estate, $valid_date)
    {


        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "Pass token",
            'toreceiver' => $email,
            'token_code' => $token_code,
            'estate' => $estate,
            'valid_date' => $valid_date,


        );

        Mail::send('emails.token', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });

        return $token_code;

    }
}


if (!function_exists('generate_token')) {

    function generate_token($user_id, $visitor, $email, $valid_date, $estate_id)
    {

        $usr = User::where('id', Auth::id())->first();
        $get_token = random_int(000000, 999999);
        $tok = new Token();
        $tok->user_id = $user_id;
        $tok->token = $get_token;
        $tok->visitor = $visitor;
        $tok->estate_id = $estate_id;
        $tok->email = $email ?? Auth::user()->email;
        $tok->address = $usr->address . " " . $usr->city . " " . $usr->state;
        $tok->valid_date = $valid_date;

        $tok->save();

        return $get_token;

    }
}


if (!function_exists('send_notification')) {

    function send_notification($message)
    {

        $response = Http::post('https://api.telegram.org/bot8199899919:AAECnCVRt27WEqryCWGck8pP7GPxWa_zJ_8/sendMessage?chat_id=1316552414', [
            'chat_id' => "1316552414",
            'text' => $message,

        ]);
        $responseData = $response->json();


    }
}
