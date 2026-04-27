<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\SecretToken;
use App\Models\User;
use App\Services\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{


    public
    function reset_password(request $request)
    {

        $reset_token = $request->reset_token;

        $token = SecretToken::where('token_hash', hash('sha256', $reset_token))->first();

        $token_is_valid = $token && Carbon::now()->lt($token?->expires_at);

        if (! $token_is_valid) {
            return StandardResponse::error(404, 'Invalid Token', []);
        }


        User::where('id', $token->user_id)->update([
            'password' => bcrypt($request->password)
        ]);

        $token->delete();


        $email = User::where('id', $token->user_id)->first()?->email;
        send_reset_email_notification($email);

        $message = "Password Reset Successfully";
        return success($message);


    }


    public function check_user(request $request)
    {

        $validator = Validator::make($request->all(), [
            'action' => ['required', 'string'],
            'email' => ['required_without:meterNo', 'email'],
            'meterNo' => ['required_without:email', 'string'],
        ]);

        if ($validator->fails()) {

            return StandardResponse::error(422, 'Validation Error', [
                'validator_errors' => $validator->errors(),
            ]);
        }

        if ($request->action == "reset") {

            $usr = get_user($request->email, $request->meterNo);

            if ($usr) {

                $otp = random_int(0000, 9999);
                $email = $usr->email;
                $otp_hash = hash('sha256', $otp);

                SecretToken::create([
                    'user_id' => $usr->id,
                    'token_hash' => $otp_hash,
                    'expires_at' => Carbon::now()->addHours(2),
                ]);

                $user = send_email_reset($email, $otp);
            }

            if ($user == 0) {
                $message = "OTP Code has been sent successfully to $email";
                return success($message);
            }


        }


        // if ($request->action == "register") {

        //     $usr = User::where('email', $request->email)->first() ?? null;
        //     $status = User::where('email', $request->email)->first()->status ?? null;


        //     if ($usr == null) {
        //         $sms_code = random_int(0000, 9999);
        //         $email = $request->email;

        //         $usrr = new User();
        //         $usrr->email = $email;
        //         $usrr->save();

        //         User::where('email', $request->email)->update(['code' => $sms_code]);
        //         $user = send_email($email, $sms_code);

        //         if ($user == 0) {
        //             $message = "OTP Code has been sent successfully to $email";
        //             return success($message);
        //         }

        //     }


        //     if ($status == 0) {
        //         $sms_code = random_int(0000, 9999);
        //         $email = $request->email;
        //         User::where('email', $request->email)->update(['code' => $sms_code]);
        //         $user = send_email($email, $sms_code);

        //         if ($user == 0) {
        //             $message = "OTP Code has been sent successfully to $email";
        //             return success($message);
        //         }


        //     }

        //     if ($status == 2) {

        //         $code = 422;
        //         $message = "User Already exist with email, Please login";
        //         return error($code, $message);

        //     }

        // }


        if ($request->action == "forget") {

            $usr = User::where('email', $request->email)->first() ?? null;

            if ($usr != null) {

                $sms_code = random_int(0000, 9999);
                $email = $request->email;

                User::where('email', $request->email)->update(['code' => $sms_code]);
                $user = send_email_reset($email, $sms_code);

                if ($user == 0) {
                    $message = "OTP Code has been sent successfully to $email";
                    return success($message);
                }

            } else {

                return response()->json([
                    'status' => false,
                    'message' => "Email does not exist",
                ], 422);
            }


        }


        $usr = User::where('email', $request->email)->first() ?? null;
        $status = User::where('email', $request->email)->first()->status ?? null;

        if($status != 2){
            User::where('email', $request->email)->delete();
        }

        if ($usr == null && $status != 2) {
            $sms_code = random_int(0000, 9999);
            $email = $request->email;


            $usrr = new User();
            $usrr->email = $email;
            $usrr->save();

            User::where('email', $request->email)->update(['code' => $sms_code]);
            $user = send_email($email, $sms_code);

            if ($user == 0) {
                $message = "OTP Code has been sent successfully to $email";
                return success($message);
            }
        } elseif ($status == 2) {
            $code = 422;
            $message = "User already exist, login your account";
            return error($message, $code);
        }


    }

    public function validate_email(request $request)
    {

        $code = $request->code;
        $email = $request->email;

        $user = get_user($request->email, $request->meterNo);

        // $validate = validate_code($code, $email);
       $token = SecretToken::where('otp_hash', hash('sha256', $code))
            ->where('user_id', $user?->user_id)
            ->first();

        $token_is_valid = $token && Carbon::now()->lt($token->expires_at);

        if (! $token_is_valid || ! $user) {
            return error("Invalid Code", 422);
        }

        // invalidate OTP
        $token->delete();

        $reset_token = Str::random(64);

        SecretToken::create([
            'token_hash' => hash('sha256', $reset_token),
            'user_id' => $user->user_id,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        return StandardResponse::success(200, 'Verified OTP Successfully', [
            'reset_token' => $reset_token,
        ]);

    }


    // public function register(request $request)
    // {


    //     $usr = User::where('email', $request->email)->first() ?? null;
    //     if ($usr == null) {

    //         $code = 422;
    //         $message = "Please Verify your email";
    //         return error($message, $code);
    //     }


    //     $gm = Meter::where('meterNo', $request->meterNo)->first() ?? null;
    //     $muser_id = Meter::where('meterNo', $request->meterNo)->first()->user_id ?? null;
    //     if ($gm == null) {
    //         $code = 422;
    //         $message = "Meter has not been profiled";
    //         return error($message, $code);

    //     }

    //     if($muser_id != null){
    //         $code = 422;
    //         $message = "Meter is already attached to a customer";
    //         return error($message, $code);
    //     }


    //     $ptype = Meter::where('meterNo', $request->meterNo)->first()->ptype ?? null;
    //     if($ptype == 2){
    //         $code = 422;
    //         $message = "You can not register at the moment. Kindly visit your estate manager for further instruction";
    //         return error($message, $code);
    //     }



    //     $estate_name = Estate::where('id', $gm->estate_id)->first()->first()->title ?? null;

    //     if ($usr->status == 1) {

    //         User::where('email', $request->email)->update([

    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'address' => $request->address,
    //             'city' => $request->city,
    //             'state' => $request->state,
    //             'phone' => $request->phone,
    //             'meterNo' => $request->meterNo,
    //             'estate_id' => $gm->estate_id ?? null,
    //             'estate_name' => $gm->estate_name ?? null,
    //             'status' => 2,
    //             'password' => bcrypt($request->password),

    //         ]);

    //         $user_id = User::where('email', $request->email)->first()->id;
    //         Meter::where('meterNo', $request->meterNo)->update(['user_id' => $user_id]);


    //         $message = "Account Registered Successfully";
    //         return success($message);

    //     }

    //     if ($usr->status == 0) {
    //         $code = 422;
    //         $message = "Please Verify your email";
    //         return error($message, $code);

    //     }

    //     if ($usr->status == 2) {
    //         $code = 422;
    //         $message = "User Already Exist";
    //         return error($message, $code);

    //     }


    //     $code = 422;
    //     $message = "We can not register this moment!!";
    //     return error($code, $message);


    // }
}
