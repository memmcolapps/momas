<?php

namespace App\Http\Controllers\Transformer;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\Tariff;
use App\Models\Transformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransformerController extends Controller
{
    public function list_transformer()
    {


        if(Auth::user()->role == 0){

            $data['transformer_list'] = Transformer::latest()->paginate(20);
            $data['transformer'] = Transformer::latest()->count();
            return view('admin/transformer/transformer-list', $data);

        } elseif(Auth::user()->role == 1){

        } elseif(Auth::user()->role == 2){

        } elseif(Auth::user()->role == 3){

            $data['transformer_list'] = Transformer::latest()->where('Estate_id', Auth::user()->estate_id)->paginate(20);
            $data['transformer'] = Transformer::latest()->where('Estate_id', Auth::user()->estate_id)->count();
            return view('admin/transformer/transformer-list', $data);


        } elseif(Auth::user()->role == 4){

        } elseif(Auth::user()->role == 5){

        } else{

        }



    }


    public function new_transformer()
    {

        if(Auth::user()->role == 0){

            $data['estate'] = Estate::all();


        } elseif(Auth::user()->role == 1){

        } elseif(Auth::user()->role == 2){

        } elseif(Auth::user()->role == 3){

            $data['estate'] = Estate::where('status', 2)->where('id', Auth::user()->estate_id)->first();

        } elseif(Auth::user()->role == 4){

        } elseif(Auth::user()->role == 5){

        } else{

        }


        return view('admin/transformer/new-transformer', $data);
    }


    public function add_new_transformer(request $request)
    {

        $trs = Transformer::create($request->all());
        $est = Estate::where('id', $request->Estate_id)->first()->title;
        Transformer::where('id', $trs->id)->update(['estate'=>$est]);
        return redirect('admin/transformer-list')->with('message', "Transformer added successfully");

    }

    public function update_transformer(request $request)
    {

        $trs = Transformer::find($request->id);
        $est = Estate::where('id', $request->Estate_id)->first()->title;
        Transformer::where('id', $trs->id)->update(['estate'=>$est]);
        if($request->Multiplier != "on"){
            Transformer::where('id', $trs->id)->update(['Multiplier'=>"off"]);
        }
        $trs->update($request->all());



        return redirect('admin/transformer-list')->with('message', "Transformer updated successfully");

    }

    public function delete_transformer(request $request)
    {


        Transformer::where('id', $request->id)->delete();

        return redirect('admin/transformer-list')->with('message', "Transformer deleted successfully");

    }







     public function view_transformer(request $request)
     {
         $data['trans'] = Transformer::where('id', $request->id)->first();
         $data['estate'] = Estate::where('status', 2)->get();

         return view('admin/transformer/transformer-view', $data);
     }

     public function transformer_deactivate(request $request)
     {
         Transformer::where('id', $request->id)->update(['status' => 1]); // Setting status to 1 for deactivated
         return redirect('admin/transformer-list')->with('message', "Transformer deactivated successfully");
     }

     public function transformer_activate(request $request)
     {
         Transformer::where('id', $request->id)->update(['status' => 2]); // Setting status to 2 for active
         return redirect('admin/transformer-list')->with('message', "Transformer activated successfully");
     }
}
