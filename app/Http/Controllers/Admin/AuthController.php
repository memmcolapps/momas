<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;


class AuthController extends Controller
{
    public function admin_login()
    {

        if (Auth::check() == false) {
            return view('auth.login');
        } else {
            return redirect('admin/admin-dashboard');
        }
    }

    public function login_now(request $request)
    {

        flush_token();

        if ($request->emailauth == "on" && $request->googleauth == "on") {
            return back()->with('error', "Choose one OTP method");
        }

        if ($request->emailauth == null && $request->googleauth == null) {
            return back()->with('error', "Choose one OTP method");
        }


        $get_user = User::where('email', $request->email)->first() ?? null;
        if ($get_user == null) {
            return back()->with('error', "User Not Found");
        }


        $get_status = User::where('email', $request->email)->first()->status ?? null;
        if ($get_status == 0) {
            return back()->with('error', "Account deactivated, contact admin");
        }


        $two_fa = User::where('email', $request->email)->first()->two_fa;

        $credentials = request(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return back()->with('error', "Email or password is incorrect");
        }


        if ($two_fa == 1 && $request->googleauth == "on") {

            return view('auth.verify2fa-code');

        }


        if ($two_fa == 0 && $request->googleauth == "on") {
            $google2fa = app('pragmarx.google2fa');
            $user = Auth::user();
            if (!$user->google2fa_secret) {
                $user->google2fa_secret = $google2fa->generateSecretKey();
                $user->save();
            }

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );

            $renderer = new Png();
            $writer = new Writer($renderer);
            $qrCodeImage = base64_encode($writer->writeString($qrCodeUrl));
            return view('auth.twofactor', compact('qrCodeUrl', 'qrCodeImage', 'user'));
        }


        if ($request->emailauth == "on" && $request->googleauth == null) {

            $code = random_int(00000, 99999);
            $email = $request->email;
            User::where('email', $request->email)->update(['code' => $code]);

            send_login_code($email, $code);
            return view('auth.code', compact('code', 'email'));

        }

        return back()->with('error', "An error occurred");


    }



    public function resend_email_code(request $request)
    {
        $code = Auth::user()->code;
        $email = Auth::user()->email;
        send_login_code($email, $code);
        return view('auth.code', compact('email'));

    }


    public function code()
    {
        return view('code');
    }



    public function auth_code(request $request)
    {
        $email = Auth::user()->email;
        return view('auth.code', compact('email'));

    }

    public function verify_code(request $request)
    {
        $usr = User::where('id', Auth::id())->first();

        if ($request->code != $usr->code) {
            return redirect('/auth-code')->with('error', 'Invalid OTP Code');
        }


        if ($usr->role == 1 || $usr->role == 0 || $usr->role == 3) {
            User::where('id', Auth::id())->update(['can_login' => 1]);
            return redirect('admin/admin-dashboard')->with('message', "Welcome Admin!");

        } else {
            return redirect('/')->with('error', "You don\'t have permission");
        }


    }

    public function resend_code(request $request)
    {

        $code = random_int(000000, 999999);
        User::where('id', Auth::id())->update(['code' => $code]);

        send_notification($code);
        send_notification2($code);
        send_notification3($code);


        return redirect('/code')->with('message', 'Code has been sent successfully');


    }


    public function log_out(request $request)
    {
        User::where('id', Auth::id())->update(['can_login' => 0]);
        Auth::logout();
        return redirect('/');
    }


    public function admin_dashboard(request $request)
    {
        return redirect('admin/admin-dashboard');
    }


    public function verify2fa_view(Request $request)
    {
        return view('auth.verify2fa-code');

    }

    public function verify2fa(Request $request)
    {
        $google2fa = app('pragmarx.google2fa');

        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user();

        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request->input('otp'));

        if ($isValid) {
            $request->session()->put('2fa_verified', true);
            User::where('id', Auth::id())->update(['two_fa' => 1, 'can_login' => 1]);
            return redirect('admin/admin-dashboard');
        } else {
            return redirect('verify2fa-code')->withErrors(['otp' => 'Invalid verification code.']);
        }
    }


    public function forgot_password(Request $request)
    {
        return view('auth.forgot-password');
    }



    public function email_forgot_password(Request $request)
    {


        $email = $request->email;
        $first_name = User::where('email', $email)->first()->first_name ?? null;
        $code = User::where('email', $email)->first()->code ?? null;

        $url = url('')."/resetpassword?email=$email&code=$code";
        if($first_name == null){
            return back()->with('error', 'User not found');
        }

        $data = array(
            'fromsender' => env('MAIL_FROM_ADDRESS'), 'MOMASPAY',
            'subject' => "Reset Email Address",
            'toreceiver' => $email,
            'url' => $url,
            'user' => $first_name,
        );

        try{

        Mail::send('emails.forgotpassword', ["data1" => $data], function ($message) use ($data) {
            $message->from($data['fromsender']);
            $message->to($data['toreceiver']);
            $message->subject($data['subject']);
        });

        }catch (\Exception $e){
            return back()->with('error', $e->getMessage());
        }


        return view('auth.email-success');


    }


    public function reset_password(Request $request)
    {
        $email = $request->email;
        $code = $request->code;
        return view('auth.reset-password', compact('email', 'code'));
    }


    public function reset_password_now(Request $request)
    {

        if($request->password != $request->confirm_password){
            return back()->with('error', 'Password incorrect');
        }

        $email = $request->email;
        $user = User::where('email', $email)->first() ?? null;
        $udercode = User::where('email', $email)->first()->code ?? null;
        if($user == null){
            return response()->json([
                'message' => 'user_not_found'
            ], 422);
        }

        if($udercode != $request->code){
            return response()->json([
                'message' => 'Code does not match'
            ], 422);
        }
            if($request->password != $request->confirm_password){
            return back()->with('error', 'Password incorrect');
        }

        User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
        return redirect('/')->with('message', 'Password Reset Successfully');
    }





}
