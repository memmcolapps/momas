<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use App\Models\EstateModFeature;
use App\Models\ModFeature;
use App\Services\FlutterwavePaymentService;
use App\Services\PaystackPaymentService;
use App\Services\WalletPaymentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{





    /**
     * Register any application services.
     */
    public function register(): void
    {
        Sanctum::ignoreMigrations();
        $this->app->bind(PaymentServiceInterface::class, function ($app, $params) {

            $provider = $params['provider'] ?? config('payments.default');

            return match ($provider) {
                'paystack' => new PaystackPaymentService(),
                'flutterwave' => new FlutterwavePaymentService(),
                'wallet' => new WalletPaymentService(),
                default => dd($provider) //throw new \Exception('Unsupported payment provider'),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Paginator::useBootstrap();
        // AppServiceProvider::boot()
        View::composer('layouts.main', function ($view) {

            if (! Auth::check()) {
                return;
            }
            $user = Auth::user();

            $features =  EstateModFeature::byUser($user)
                ->join('mod_features', 'mod_features.id', 'estate_mod_features.mod_feature_id')
                ->select([
                        'estate_mod_features.status as estate_status',
                        'estate_mod_features.estate_id',
                        'mod_features.title',
                        'mod_features.slug',
                        'mod_features.status as mod_status'
                    ])
                ->get();

            $mod_features = cache()->remember(
                "mod_features_{$user->estate_id}",
                now()->addMinutes(5),
                function () use ($user, $features) {
                    $mod_features = [];

                    foreach ($features as $feature) {
                        $final_status = $feature->mod_status;

                        if ($feature->mod_status == ModFeature::AVAILABLE_STATUS) {
                            $final_status = $feature->estate_status;

                            // if (
                            //     in_array($feature->slug, [\App\Constants\Feature::MOMAS_METER, \App\Constants\Feature::OTHER_METER])
                            //     && $feature->estate_status == ModFeature::AVAILABLE_STATUS
                            //     && ! $meter->isActive()
                            // ) {
                            //     $final_status = ModFeature::TEMPORARY_DOWNTIME_STATUS;
                            // }
                        }

                        $mod_features[$feature->slug] = (int) $final_status;
                    }

                    return $mod_features;
                }
            );
            $view->with('mod_features', $mod_features);
        });
    }
}
