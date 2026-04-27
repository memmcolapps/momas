<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use App\Services\FlutterwavePaymentService;
use App\Services\PaystackPaymentService;
use App\Services\WalletPaymentService;
use Illuminate\Pagination\Paginator;
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
    }
}
