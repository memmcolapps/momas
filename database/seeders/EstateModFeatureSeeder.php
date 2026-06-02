<?php

namespace Database\Seeders;

use App\Constants\Feature;
use App\Models\Estate;
use App\Models\ModFeature;
use App\Models\EstateModFeature;
use Illuminate\Database\Seeder;

class EstateModFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $estates = Estate::all(['id']);
        $features = ModFeature::all(['id']);

        foreach ($estates as $estate) {
            foreach ($features as $feature) {

                $default_status = EstateModFeature::AVAILABLE_STATUS;

                if ($feature->slug == Feature::MOMAS_METER) {
                    $default_status = EstateModFeature::AVAILABLE_STATUS;
                }

                EstateModFeature::updateOrCreate(
                    [
                        'estate_id' => $estate->id,
                        'mod_feature_id' => $feature->id,
                    ],
                    [
                        'status' => $default_status,
                    ]
                );

            }
        }
    }
}
