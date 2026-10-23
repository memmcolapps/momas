<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstateController extends Controller
{

    public function get_estate(){

        $data = Estate::where('status', 2)->get()->makeHidden(['created_at','updated_at']);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);


    }


    public function estate_token(request $request){



        $user_id = Auth::id();
        $visitor = $request->qty;
        $email = $request->email;
        $can_send = $request->can_send_mail;
        $estate_id = Auth::user()->estate_id;
        $currentTime = Carbon::now();
        $valid_date = $currentTime->addHours(24);

        $tok = generate_token($user_id, $visitor, $email, $valid_date, $estate_id);
        if($can_send == 1){

            $request->validate([
                'email' => 'required|email'
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Enter a valid email address.',
            ]);


            $usr = User::where('id', Auth::id())->first();
            $token_code = $tok;
            $estate = Estate::where('id', $estate_id)->first()->title ?? null;
            $send = send_token_email($email, $token_code, $estate, $valid_date);
            $data['token'] = $send;
            $data['name'] = $usr->first_name." ".$usr->last_name;
            $data['address'] = $usr->address." ".$usr->city." ".$usr->state;
            $data['service'] = "Access token";
            if($send == 0 ){
               return response()->json([
                   'status' => true,
                   'data' => $data,
                   'message' => "token Created Successfully, token sent to visitor"
               ]);
           }
        }

        $usr = User::where('id', Auth::id())->first();
        $data['token'] = $tok;
        $data['name'] = $usr->first_name." ".$usr->last_name;
        $data['address'] = $usr->address." ".$usr->city." ".$usr->state;
        $data['service'] = "Access token";


        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => "token Created Successfully"
        ]);


    }



    public function approve_token(request $request){

        $ck = Token::where('id', $request->token_id)->first()->status ?? null;
        if($ck == "2"){
            return response()->json([
                'status' => false,
                'message' => "token has already been validated"
            ]);
        }else{

            Token::where('id', $request->token_id)->update(['status' => "2"]);
            return response()->json([
                'status' => true,
                'message' => "token Successfully Validated"
            ]);

        }

    }


    public function token_list(request $request){

        $list = Token::latest()->where('estate_id', Auth::user()->estate_id)->where('status', 0)->take('50')->get() ?? null;
        return response()->json([
            'status' => true,
            'data' =>$list
        ]);


    }


    public function disapprove_token(request $request){

        Token::where('id', $request->token_id)->update(['status' => 3]);
        return response()->json([
            'status' => true,
            'message' => "token Successfully Disapproved"
        ]);


    }


    public function delete_token(request $request){

        Token::where('id', $request->token_id)->delete();
        return response()->json([
            'status' => true,
            'message' => "token Successfully Deleted"
        ]);


    }


    public function set_default_estate(request $request){


        $name = Estate::where('id', $request->estate_id)->first()->title;
        User::where('id', Auth::id())->update([
            'estate_id' => $request->estate_id,
            'address' => $request->address,
            'hno' => $request->hno,
            'estate_name' => $name,


        ]);

        return response()->json([
            'status' => true,
            'message' => "Default Address Set successfully"
        ]);


    }


    public function estate_deactivate(request $request)
    {

        Estate::where('id', $request->id)->update(['status' => 0]);

        return back()->with('message', "Estate Deactivated successfully");


    }


    public function estate_activate(request $request)
    {

        Estate::where('id', $request->id)->update(['status' => 2]);

        return back()->with('message', "Estate Activated successfully");


    }





}
