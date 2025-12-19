<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditlog;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TariffController extends Controller
{


    public function tariff_list(request $request)
    {


        if(Auth::user()->role == 0){

            $data['tariff_list'] = Tariff::latest()->paginate(20);
            $data['tariffis'] = Tariff::all();
            $data['tarifftariffis'] = Tariff::count();
            $data['estate'] = Estate::all();



            return view('admin/tariff/index', $data);



        } elseif(Auth::user()->role == 1){

        } elseif(Auth::user()->role == 2){

        } elseif(Auth::user()->role == 3){

            $data['tariff_list'] = Tariff::latest()->where('estate_id', Auth::user()->estate_id)->paginate(20);
            $data['tariffis'] = Tariff::latest()->latest()->where('estate_id', Auth::user()->estate_id)->get();
            $data['tarifftariffis'] = Tariff::where('estate_id', Auth::user()->estate_id)->count();
            // $data['estate']  = Estate::all();
             $data['estate'] = Estate::where('id', Auth::user()->estate_id)->get(); 

            return view('admin/tariff/index', $data);


        } elseif(Auth::user()->role == 4){

        } elseif(Auth::user()->role == 5){

        } else{

        }




    }


    public function new_tariff(request $request)
    {


        // $data['estate']  = Estate::all();
        // return view('admin.tariff.new-tariff', $data);

    if(Auth::user()->role == 0) {
        $data['estate'] = Estate::all();
    } elseif(Auth::user()->role == 3) {
        // Estate admin can only create tariffs for their estate
        $data['estate'] = Estate::where('id', Auth::user()->estate_id)->get();

    } else {
        return redirect('admin/admin-dashboard')->with('error', 'Unauthorized access');
    }
    
    // Get all used indices per estate - pass to view
    $data['used_indices_by_estate'] = Tariff::select('estate_id', 'tariff_index')
                                           ->get()
                                           ->groupBy('estate_id')
                                           ->map(function($tariffs) {
                                               return $tariffs->pluck('tariff_index')->toArray();
                                           });

    return view('admin.tariff.new-tariff', $data);

    }



    public function add_new_Tariff(request $request)
    {

        // AUTHORIZATION CHECK - Only for estate admins (role 3)
        if(Auth::user()->role == 3) {
            if($request->estate_id != Auth::user()->estate_id) {
                return back()->with('error', 'You can only create tariffs for your estate');
            }
        }

        $ck = Tariff::where('title', $request->title)->first() ?? null;
        // DUPLICATE CHECK - Prevent duplicate tariffs for same estate
        // $ck = Tariff::where('title', $request->title)
        //         ->where('estate_id', $request->estate_id)
        //         ->first() ?? null;

        if($ck != null){
            return back()->with('error', 'Tariff already exist for this exist');
        }

        $tr = new Tariff();
        $tr->title = $request->title;
        $tr->estate_id = $request->estate_id;
        $tr->status = 2;
        $tr->tariff_index = $request->tariff_index;
        $tr->type = $request->tariff_source;
        $tr->save();


        $tr = Tariff::where('id', $tr->id)->first();

        return redirect("/admin/view-tariff?id=$tr->id")->with('message', "Tariff created successfully");


    }








    public function delete_tariff(request $request)
    {
        $tariff = Tariff::where('id', $request->id)->first();

        if (!$tariff) {
            return back()->with('error', 'Tariff not found');
        }
    
        // Estate admins can only delete tariffs from their estate
        if(Auth::user()->role == 3) {
            if($tariff->estate_id != Auth::user()->estate_id) {
                return back()->with('error', 'You can only delete tariffs from your estate');
            }
        }

        Tariff::where('id', $request->id)->delete();
        return back()->with('messsage', 'Tariff deleted successfully');

    }


    public function view_tariff(request $request)
    {

        $tr = Tariff::where('id', $request->id)->first();

        // Estate admins can only view tariffs from their estate
        if(Auth::user()->role == 3) {
            if($tr->estate_id != Auth::user()->estate_id) {
                return redirect('admin/tariff-list')->with('error', 'Unauthorized access');
            }
        }

        $chk_active = TarrifState::where('tariff_id', $request->id)->where('status', 2)->count();
        if($chk_active > 1){
            TarrifState::where('tariff_id',$request->id)->update(['status' => 0]);
            TarrifState::where('tariff_id',$request->id)->first()->update(['status' => 2]);

        }

        $tstate = TarrifState::where('tariff_id', $request->id)->get();
        $effictive_date = TarrifState::where('tariff_id', $request->id)->where('status', 2)->first()->effective_from ?? null;

        if(Auth::user()->role == 0) {
            $estate = Estate::all();
        } else {
            $estate = Estate::where('id', Auth::user()->estate_id)->get();
        }

        // Get used tariff indices for this estate (excluding current tariff)
        $usedIndices = Tariff::where('estate_id', $tr->estate_id)
            ->where('id', '!=', $tr->id)
            ->whereNotNull('tariff_index')
            ->pluck('tariff_index')
            ->toArray();

        return view('admin.tariff.view', compact('tr', 'tstate','estate', 'effictive_date', 'usedIndices'));

    }

    // public function add_state_tariff(request $request)
    // {

    //     $id = $request->id;


    //     if($request->date_to == null && $request->never_expire == "yes"){


    //         $tr = new TarrifState();
    //         $tr->amount = $request->amount;
    //         $tr->effective_from = $request->date_from;
    //         $tr->effective_to = null;
    //         $tr->t_index = $request->t_index;
    //         $tr->estate_id = $request->estate_id;
    //         $tr->vat = $request->vat;
    //         $tr->tariff_id = $request->tariff_id;
    //         $tr->save();



    //         $ck_count = TarrifState::where('tariff_id', $request->tariff_id)->count();
    //         if($ck_count == 1){
    //             TarrifState::latest()->where('status', 0)->where('tariff_id', $request->tariff_id)->update(['status' => 2]);
    //         }

    //         return back()->with('message', 'Tariff Added Successfully');

    //     }


    //     if($request->date_to == null && $request->never_expire == "no"){
    //         return redirect("admin/view-tariff?id=$id")->with('error', 'Please choose effective date to');
    //     }

    //     if($request->date_to != null && $request->never_expire == "no"){
    //         return redirect("admin/view-tariff?id=$id")->with('error', 'Effective date has been set please choose NO on never expire option');
    //     }

    //     $ddfrom = new DateTime($request->date_from);
    //     $ddto = new DateTime($request->date_to);

    //     if( $ddfrom >= $ddto  ){
    //         return redirect("admin/view-tariff?id=$id")->with('error', 'End date can not be greater than start date');
    //     }

    //     $newdate = TarrifState::latest()->where('status', 2)->where('tariff_id', $request->id)->first()->effective_to ?? null;
    //     $nd = new DateTime($newdate) ?? null;

    //     if($ddto <= $nd ){
    //         return redirect("admin/view-tariff?id=$id")->with('error', 'you have a running tariff');
    //     }

    //     $tr = new TarrifState();
    //     $tr->amount = $request->amount;
    //     $tr->effective_from = $request->date_from;
    //     $tr->effective_to = $request->date_to;
    //     $tr->tariff_id = $request->id;
    //     $tr->estate_id = $request->estate_id;
    //     $tr->t_index = $request->t_index;
    //     $tr->vat = $request->vat;
    //     $tr->save();

    //     $ck_count = TarrifState::where('tariff_id', $request->id)->count();

    //     if($ck_count == 1){
    //         TarrifState::latest()->where('status', 0)->where('tariff_id', $request->id)->update(['status' => 2]);
    //     }


    //     return back()->with('message', 'Tariff Added Successfully');


    // }


    public function add_state_tariff(request $request) {
        // Same as $tariff_id below it, but it is to handle redirection (feel free to remove it and replace with tariff_id)
        $id = $request->tariff_id ?? $request->id;
        // Handle both super admin and estate admin forms
        $tariff_id = $request->tariff_id ?? $request->id;

        // Case 1: Never expire = YES
        if($request->never_expire == "yes") {
            // Should not have an end date
            if($request->date_to != null) {
                return redirect("admin/view-tariff?id=$id")->with('error', 'Cannot set end date when never expire is enabled');
            }

            $tr = new TarrifState();
            $tr->amount = $request->amount;
            $tr->effective_from = $request->date_from;
            $tr->effective_to = null; // No end date
            $tr->t_index = $request->t_index;
            $tr->estate_id = $request->estate_id;
            // Handle VAT based on user role
            if(Auth::user()->role == 0) {
                // Super Admin - use direct VAT input
                $tr->vat = $request->vat;
                $tr->fixed_charge = 0; // Super Admin doesn't set fixed charge
            } else {
                // Estate Admin - use checkbox logic
                if($request->has('apply_vat') && $request->apply_vat == '1') {
                    $tr->vat = $request->estate_vat;
                } else {
                    $tr->vat = 0;
                }
                $tr->fixed_charge = $request->fixed_charge ?? 0;
            }
            $tr->tariff_id = $tariff_id;
            $tr->save();

            // Set as active if first tariff state
            $ck_count = TarrifState::where('tariff_id', $tariff_id)->count();
            if($ck_count == 1){
                TarrifState::latest()->where('status', 0)->where('tariff_id', $tariff_id)->update(['status' => 2]);
            }

            return back()->with('message', 'Tariff Added Successfully');
        }

        // Case 2: Never expire = NO
        if($request->never_expire == "no") {
            // Must have an end date
            if($request->date_to == null) {
                return redirect("admin/view-tariff?id=$id")->with('error', 'Please choose effective end date');
            }

            // Validate date range
            $ddfrom = new DateTime($request->date_from);
            $ddto = new DateTime($request->date_to);

            if($ddfrom >= $ddto) {
                return redirect("admin/view-tariff?id=$id")->with('error', 'End date must be after start date');
            }

            // Check for overlapping tariffs
            $newdate = TarrifState::latest()->where('status', 2)->where('tariff_id', $request->id)->first()->effective_to ?? null;
            if($newdate) {
                $nd = new DateTime($newdate);
                if($ddto <= $nd) {
                    return redirect("admin/view-tariff?id=$id")->with('error', 'You have an overlapping tariff period');
                }
            }

            $tr = new TarrifState();
            $tr->amount = $request->amount;
            $tr->effective_from = $request->date_from;
            $tr->effective_to = $request->date_to;
            $tr->tariff_id = $tariff_id;
            $tr->estate_id = $request->estate_id;
            $tr->t_index = $request->t_index;
            // Handle VAT based on user role
            if(Auth::user()->role == 0) {
                // Super Admin - use direct VAT input
                $tr->vat = $request->vat;
                $tr->fixed_charge = 0; // Super Admin doesn't set fixed charge
            } else {
                // Estate Admin - use checkbox logic
                if($request->has('apply_vat') && $request->apply_vat == '1') {
                    $tr->vat = $request->estate_vat;
                } else {
                    $tr->vat = 0;
                }
                $tr->fixed_charge = $request->fixed_charge ?? 0;
            }
            $tr->save();

            $ck_count = TarrifState::where('tariff_id', $tariff_id)->count();
            if($ck_count == 1){
                TarrifState::latest()->where('status', 0)->where('tariff_id', $tariff_id)->update(['status' => 2]);
            }

            return back()->with('message', 'Tariff Added Successfully');
        }

        return redirect("admin/view-tariff?id=$id")->with('error', 'Please select never expire option');
    }




    public function update_the_tariff(request $request)
    {

        if($request->isDualTariff ==  "on"){

            Tariff::where('id', $request->id)->update([
                'title' => $request->title,
            ]);


        }else{

            Tariff::where('id', $request->id)->update([
                'title' => $request->title,
            ]);

        }

        return redirect('admin/tariff-list')->with('message', 'Tariff updated successfully');

    }


    public function update_nepa(request $request)
    {
        $ck = Tariff::where('user_id', $request->user_id)->where('type', 'nepa')->first() ?? null;


        if($ck != null){
            Tariff::where('id', $request->id)->where('user_id', $request->user_id)->update([
                'user_id' => null,
                 'type' => 'nepa'
            ]);
        }


        Tariff::where('id', $request->id)->update([
            'user_id' => $request->user_id, 'type' => "nepa"
        ]);



        return back()->with('message', "User Tariff has been updated");

    }


    public function update_gen(request $request)
    {

        Tariff::where('id', $request->tariff)->update(['user_id' => $request->user_id, 'type' => "gen"]);
        return back()->with('message', "User Tariff has been updated");

    }


    public function detach_gen_tariff(request $request)
    {
        Tariff::where('id', $request->id)->update(['user_id' => null, 'type' => "gen"]);
        return back()->with('message', "Detach Tariff successful");

    }


    public function detach_nepa_tariff(request $request)
    {
        Tariff::where('id', $request->id)->update(['user_id' => null, 'type' => "nepa"]);
        return back()->with('message', "Detach Tariff successful");

    }



    public function update_tariffstate(request $request)
    {
        // Debug: Log all request data
        \Log::info('Update tariff state request data:', $request->all());

        $ttf = TarrifState::find($request->id);
        if ($ttf) {
            // If setting this tariff state to active (status = 2)
            if($request->status == 2) {
                // First, set ALL other tariff states for this tariff to inactive
                TarrifState::where('tariff_id', $ttf->tariff_id)
                          ->where('id', '!=', $ttf->id)
                          ->update(['status' => 0]);
            }

            $ttf->status = $request->status;
            $ttf->amount = $request->amount;
            $ttf->estate_id = $ttf->estate_id;

            // Handle VAT based on user role
            if(Auth::user()->role == 0) {
                // Super Admin - use direct VAT input
                $ttf->vat = $request->vat;
                // Super Admin doesn't update fixed charge (keep existing value)
            } else {
                // Estate Admin - use checkbox logic
                if($request->has('apply_vat') && $request->apply_vat == '1') {
                    $ttf->vat = $request->estate_vat;

                } else {
                    $ttf->vat = 0;

                }
                $ttf->fixed_charge = $request->fixed_charge ?? 0;
            }

            $ttf->save();

        }

        return back()->with('message', "Tariff state updated successful");
    }


    public function update_tariff_index(Request $request)
    {
        // Validate the request
        $request->validate([
            'tariff_id' => 'required|exists:tariffs,id',
            'tariff_index' => 'required|integer|min:1|max:99'
        ]);

        $tariff = Tariff::find($request->tariff_id);

        if (!$tariff) {
            return back()->with('error', 'Tariff not found');
        }

        // Authorization check - Estate admins can only update tariffs from their estate
        if(Auth::user()->role == 3) {
            if($tariff->estate_id != Auth::user()->estate_id) {
                return back()->with('error', 'You can only update tariffs from your estate');
            }
        }

        // Check if tariff_index already exists for another tariff in the same estate
        $existingTariff = Tariff::where('estate_id', $tariff->estate_id)
            ->where('tariff_index', $request->tariff_index)
            ->where('id', '!=', $tariff->id)
            ->first();

        if ($existingTariff) {
            return back()->with('error', "Tariff index {$request->tariff_index} is already used by tariff: {$existingTariff->title}");
        }

        // Update the tariff index
        $tariff->tariff_index = $request->tariff_index;
        $tariff->save();

        return back()->with('message', "Tariff index updated successfully to {$request->tariff_index}");
    }

}
