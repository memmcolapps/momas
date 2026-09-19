<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use App\Models\EstateModFeature;
use App\Models\ModFeature;
use App\Services\FlutterwavePaymentService;
use App\Services\PaystackPaymentService;
use App\Services\WalletPaymentService;
use App\Support\RequestContext;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\SqlServerConnector;

class AppServiceProvider extends ServiceProvider
{





    /**
     * Register any application services.
     */
    public function register(): void
    {
        Sanctum::ignoreMigrations();
        $this->app->singleton(RequestContext::class);
        $this->app->bind(PaymentServiceInterface::class, function ($app, $params) {

            $provider = $params['provider'] ?? config('payments.default');

            switch ($provider) {
                case 'paystack':
                    return new PaystackPaymentService();
                case 'flutterwave':
                    return new FlutterwavePaymentService();
                case 'wallet':
                    return new WalletPaymentService();
                default:
                    dd($provider);
            }
        });

        Connection::resolverFor('sqlsrv', function ($connection, $database, $prefix, $config) {
            $connector = new class extends SqlServerConnector {
                protected function getDsn(array $config): string
                {
                    $dsn = parent::getDsn($config);
                    if (!empty($config['TrustServerCertificate'])) {
                        $dsn .= ';TrustServerCertificate=1';
                    }
                    return $dsn;
                }
            };

            $pdo = $connector->connect($config);

            return new \Illuminate\Database\SqlServerConnection($pdo, $database, $prefix, $config);
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

            $mod_features = [];
            try {
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
                            }

                            $mod_features[$feature->slug] = (int) $final_status;
                        }

                        return $mod_features;
                    }
                );
            } catch (\Throwable $e) {
                $mod_features = [];
            }
            $view->with('mod_features', $mod_features);
        });
    }
}
