<?php

use App\Http\Controllers\Admin\MeterImportController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Bills\BillsController;
use App\Http\Controllers\Estate\EstateController;
use App\Http\Controllers\Feature\FeatureController;
use App\Http\Controllers\Meter\MeterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Transaction\TransactionController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// Public / Unauthenticated Routes
// ─────────────────────────────────────────────

Route::get('get-all-estate', [EstateController::class, 'get_estate']);
Route::get('get-estate-tariff', [MeterController::class, 'get_estate_tariff']);
Route::get('get-estate', [EstateController::class, 'get_estate']);

Route::post('login', [LoginController::class, 'login']);
Route::post('check-email', [RegisterController::class, 'check_user']);
Route::post('validate-email', [RegisterController::class, 'validate_email']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('reset-password', [LoginController::class, 'reset_password']);
Route::post('reset-password', [RegisterController::class, 'reset_password']);
Route::post('update-password', [RegisterController::class, 'update_password']);
Route::post('delete-user', [LoginController::class, 'delete_user']);

Route::post('validate', [MeterController::class, 'validate_meter']);
Route::post('validate', [MeterController::class, 'validate_mobile_meter']);
Route::post('validate-cable', [BillsController::class, 'validate_cable']);

Route::get('get-existing-meters', [MeterImportController::class, 'get_existing_meters']);

Route::any('e-fund', [TransactionController::class, 'enkpay_webhook']);
Route::post('enkpay_webhook', [TransactionController::class, 'enkpay_webhook']);

Route::get('support', [LoginController::class, 'support']);

// ─────────────────────────────────────────────
// POS Routes
// ─────────────────────────────────────────────

Route::any('pos/validate', [PosController::class, 'validate_meter']);
Route::any('pos/buy-token', [PosController::class, 'buy_meter_token']);
Route::any('pos/retry-meter-token', [PosController::class, 'retry_meter_token']);
Route::any('pos/eod', [PosController::class, 'get_all_transaction']);

// ─────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────

Route::group(['middleware' => ['feature_control', 'auth:api', 'acess']], function () {

    Route::post('update-password', [RegisterController::class, 'update_password']);

    Route::post('balance', [ProfileController::class, 'balance']);
    Route::get('features', [FeatureController::class, 'features']);
    Route::get('promotion', [FeatureController::class, 'promotion']);
    Route::get('getUser', [LoginController::class, 'get_user']);
    Route::get('get-account', [TransactionController::class, 'get_account_details']);
    Route::any('admin-fee-check', [TransactionController::class, 'check_admin_fee']);

    // ── Feature::MOMAS_METER ──────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::MOMAS_METER]], function () {
        Route::post('buy-meter', [MeterController::class, 'buy_meter_token']);
        Route::post('request-meter', [MeterController::class, 'request_meter']);
        Route::post('retry-meter', [TokenController::class, 'retry_generate_credit_token']);
        Route::get('vending-properties', [MeterController::class, 'vending_properties']);
    });

    // ── Feature::OTHER_METER ──────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::OTHER_METER]], function () {
        Route::post('buy-meter-others', [MeterController::class, 'pay_for_others_meter_token']);
    });

    // ── Feature::PRINT_TOKEN ──────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::PRINT_TOKEN]], function () {
        Route::post('reprint-token', [MeterController::class, 'reprint_meter_token']);
        Route::post('get-token', [MeterController::class, 'get_token']);
    });

    // ── Feature::ACCESS_TOKEN ─────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::ACCESS_TOKEN]], function () {
        Route::post('generate-token', [EstateController::class, 'estate_token']);
        Route::post('approve-token', [EstateController::class, 'approve_token']);
        Route::post('disapprove-token', [EstateController::class, 'disapprove_token']);
        Route::post('delete-token', [EstateController::class, 'delete_token']);
        Route::get('token-list', [EstateController::class, 'token_list']);
        Route::get('electricity-tokens', [TransactionController::class, 'electricityTokens']);
    });

    // ── Feature::SERVICES ────────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::SERVICES]], function () {
        Route::get('service-properties', [ServiceController::class, 'service_properties']);
        Route::get('get-artisan-by-id', [ServiceController::class, 'get_artisan_by_id']);
        Route::get('fetch-services', [ServiceController::class, 'fetch_services']);
        Route::get('get-artisans-by-service', [ServiceController::class, 'get_artisans_by_service']);
        Route::post('get-comment', [ServiceController::class, 'get_comment']);
        Route::post('save-comment', [ServiceController::class, 'save_comment']);
        Route::get('get-service', [ServiceController::class, 'get_estate']);
    });

    // ── Feature::BILL_PAYMENT ────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::BILL_PAYMENT]], function () {
        Route::post('buy-airtime', [BillsController::class, 'buy_airtime']);
        Route::get('get-data', [BillsController::class, 'get_data']);
        Route::post('buy-data', [BillsController::class, 'buy_data']);
        Route::post('buy-cable', [BillsController::class, 'buy_cable']);
        Route::get('cable-plan', [BillsController::class, 'get_cable_plan']);
    });

    // ── Feature::SUPPORT ─────────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::SUPPORT]], function () {
        Route::get('get-estate', [EstateController::class, 'get_estate']);
        Route::post('set-default', [EstateController::class, 'set_default_estate']);
    });

    Route::post('pay', [TransactionController::class, 'make_payment']);
    Route::get('get-transactions', [TransactionController::class, 'all_transactions']);
    Route::get('arrears', [TransactionController::class, 'arrears']);
    Route::post('pay_arrears', [TransactionController::class, 'pay_arrears']);
    Route::get('get-trx', [TransactionController::class, 'get_trx']);

    // ── Feature::ANALYSIS ────────────────────
    Route::group(['defaults' => ['feature' => \App\Constants\Feature::ANALYSIS]], function () {
        Route::get('get-token-purchase-by-month', [AnalyticController::class, 'getTokenPurchaseByMonth']);
        Route::get('get-analysis', [AnalyticController::class, 'getAnalyticsPage']);
        Route::get('get-trx-chart', [AnalyticController::class, 'filterTransactionChartData']);
        Route::get('get-analysis-summary', [AnalyticController::class, 'getAnalyticsSummary']);
        Route::get('get-utility-metrics', [AnalyticController::class, 'filterUtilityMetrics']);
        Route::get('get-token-report', [AnalyticController::class, 'filterAccessTokens']);
    });

});

// ─────────────────────────────────────────────
// Webhook & App Version Routes
// ─────────────────────────────────────────────

Route::get('check-app-version', [NotificationController::class, 'checkAppVersion']);
Route::post('paystack-webhook', [TransactionController::class, 'paystackWebhook'])->name('paystack.webhook');
// Route::post('test-paystack-webhook', [TransactionController::class, 'triggerPaystackWebhook']);

// ─────────────────────────────────────────────
// Log Routes
// ─────────────────────────────────────────────

Route::get('/logs', [App\Http\Controllers\LogController::class, 'getAllLogs']);
Route::get('/logs/query', [App\Http\Controllers\LogController::class, 'queryLogs']);
Route::get('/logs/stats', [App\Http\Controllers\LogController::class, 'getStats']);
Route::delete('/logs/clear', [App\Http\Controllers\LogController::class, 'clearLogs']);
Route::get('/logs/{id}', [App\Http\Controllers\LogController::class, 'getLog']);
Route::delete('/logs/{id}', [App\Http\Controllers\LogController::class, 'deleteLog']);
