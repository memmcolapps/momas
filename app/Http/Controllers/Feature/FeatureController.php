<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Models\EstateModFeature;
use App\Models\Feature;
use App\Models\ModFeature;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeatureController extends Controller
{


    public function features(){

        $auth_user = Auth::user();
        $meter = meter();
        $features =  EstateModFeature::byUser($auth_user)
            ->join('mod_features', 'mod_features.id', 'estate_mod_features.mod_feature_id')
            ->select([
                    'estate_mod_features.status',
                    'estate_mod_features.estate_id',
                    'mod_features.title',
                    'mod_features.slug'
                ])
            ->get();


        $mod_features = [];

        // dd($features->toArray(), [\App\Constants\Feature::MOMAS_METER, \App\Constants\Feature::OTHER_METER]);

        foreach ($features as $feature) {
            $mod_features[$feature->slug] = $feature->status;

            if (
                in_array($feature->slug, [\App\Constants\Feature::MOMAS_METER, \App\Constants\Feature::OTHER_METER])
                && $feature->status = ModFeature::AVAILABLE_STATUS
                && ! $meter->isActive()
            ) {
                $mod_features[$feature->slug] = 2;
            }
        }


        return response()->json([

            'status' => true,
            'feature' => $mod_features,

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
