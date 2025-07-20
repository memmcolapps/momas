<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Estate;
use App\Models\EstateService;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Service;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstateServiceController extends Controller
{
    public function index(request $request)
    {



        if(Auth::user()->role == 0){

            $data['services'] = EstateService::latest()->paginate(20);
            $data['service_count'] = EstateService::count();
            $data['estate_id'] = Auth::user()->estate_id;
            $data['prof_services'] = Service::all();
            $data['estate'] = Estate::all();





            return view('admin.estate.service-list', $data);



        } elseif(Auth::user()->role == 1){

        } elseif(Auth::user()->role == 2){

        } elseif(Auth::user()->role == 3){

            $data['services'] = EstateService::latest()->where('estate_id', Auth::user()->estate_id)->paginate(20);
            $data['service_count'] = EstateService::where('estate_id', Auth::user()->estate_id)->count();
            $data['estate_id'] = Auth::user()->estate_id;
            $data['prof_services'] = Service::all();




            return view('admin.estate.service-list', $data);

        } elseif(Auth::user()->role == 4){

        } elseif(Auth::user()->role == 5){

        } else{

        }






    }


    public function add_new_service(request $request)
    {

        $services = Service::where('id', $request->service)->first();
        $ser = new EstateService();
        $ser->professional_name = $request->professional_name;
        $ser->professional_email = $request->professional_email;
        $ser->professional_phone = $request->professional_phone;
        $ser->estate_id = $request->estate_id;
        $ser->service_id = $services->id;
        $ser->status = 2;
        $ser->service_title = $services->service_title;
        $ser->save();

        return back()->with('message', "Estate Service successfully added");


    }

    public function view_service(request $request)
    {


        $data['services'] = EstateService::where('id', $request->id)->first();
        $data['prof_services'] = Service::all();
        $data['comment'] = Comment::where('estate_service_id', $request->id)->get();



        return view('admin.estate.view-service', $data);


    }


    public function service_update(request $request)
    {


        if($request->service != null){
            $services = Service::where('id', $request->service)->first();
            EstateService::where('id', $request->id)->update([
                'professional_name' => $request->professional_name,
                'professional_phone' => $request->professional_phone,
                'professional_email' => $request->professional_email,
                'service_id' => $services->id,
                'service_title' => $services->service_title,
        ]);
        }


        EstateService::where('id', $request->id)->update([
            'professional_name' => $request->professional_name,
            'professional_phone' => $request->professional_phone,
            'professional_email' => $request->professional_email,
        ]);

        return back()->with('message', "Estate service Updated successfully");



    }


    public function delete_comment(request $request)
    {

        Comment::where('id', $request->id)->delete();
        return back()->with('message', "Comment deleted successfully");


    }


    public function delete_service(request $request)
    {
        EstateService::where('id', $request->id)->delete();
        return back()->with('message', "Estate Service deleted successfully");

    }

    public function deactivate_service(request $request)
    {
        EstateService::where('id', $request->id)->update(['status' => 0]);
        return back()->with('message', "Estate Service updated successfully");

    }

    public function activate_service(request $request)
    {
        EstateService::where('id', $request->id)->update(['status' => 2]);
        return back()->with('message', "Estate Service updated successfully");

    }


    public function estate_update_vat(request $request)
    {

        Estate::where('id', $request->estate_id)->update(['estate_vat' => $request->vat]);
        return back()->with('message', "Estate Vat updated successfully");

    }


    public function estate_update_minpur(request $request)
    {
        Estate::where('id', $request->estate_id)->update(['min_pur' => $request->min_pur,  'max_pur' => $request->max_pur, 'vend_duration' => $request->vend_duration]);
        return back()->with('message', "Estate MIN/MAX VEND updated successfully");

    }














}
