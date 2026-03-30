<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{


    public function features(){

        $feature =  Feature::where('id', 1)->first()->makeHidden(['created_at', 'updated_at']);
        $feature->bill_payment = 1;

        $meter = meter();
        if ($meter->status !== 2) {
            $feature->momas_meter = 2;
            $feature->other_meter = 2;
        }


        return response()->json([

            'status' => true,
            'feature' => $feature

        ]);

    }

    // public function updateFeaturesStatus(Request $request) {
    //     $validator = Validator::make($request->all(), []);

    //     if ($validator->fails()) {

    //     }
    // }


    public function promotion(){

        $slider = Slider::all();

        foreach($slider as $data){
            $slide = [];
            $slide['url'] = url('')."/public/asset/img/".$data->image.".png";
            $slide['link'] = $data->link;
            $slides[] = $slide;
        }

        return response()->json([
            'status' => true,
            'promo' => $slides
        ]);



    }
}
