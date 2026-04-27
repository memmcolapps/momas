<?php

namespace Database\Seeders;

use App\Models\ModFeature;
use Illuminate\Database\Seeder;

class ModFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'title' => 'Buy Token',
                'slug' => 'momas_meter',
                'description' => 'Access to momas meter services',
            ],
            [
                'title' => 'Buy Token(Others)',
                'slug' => 'other_meter',
                'description' => 'Access to other meter providers',
            ],
            [
                'title' => 'Print Token',
                'slug' => 'print_token',
                'description' => 'Ability to print tokens',
            ],
            [
                'title' => 'Access Token',
                'slug' => 'access_token',
                'description' => 'Ability to access tokens',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'description' => 'General service access',
            ],
            [
                'title' => 'Bill Payment',
                'slug' => 'bill_payment',
                'description' => 'Pay bills',
            ],
            [
                'title' => 'Support',
                'slug' => 'support',
                'description' => 'Customer support access',
            ],
            [
                'title' => 'Top Up',
                'slug' => 'top_up',
                'description' => 'Recharge/top-up services',
            ],
            [
                'title' => 'Analysis',
                'slug' => 'analysis',
                'description' => 'Analytics and reporting',
            ],
        ];

        foreach ($features as $feature) {
            ModFeature::updateOrCreate(
                ['slug' => $feature['slug']], // 🔥 stable identifier
                [
                    'title' => $feature['title'],
                    'description' => $feature['description'],
                    'status' => ModFeature::AVAILABLE_STATUS,
                ]
            );
        }
    }
}
