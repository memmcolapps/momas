<?php

use App\Exports\CustomerExport;
use App\Exports\MeterExport;
use App\Exports\MeterTransactionExport;
use App\Http\Controllers\AccessToken\AccessTokenConroller;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CustomerImportController;
use App\Http\Controllers\Admin\DashboardContoller;
use App\Http\Controllers\Admin\EstateController;
use App\Http\Controllers\Admin\ExportControler;
use App\Http\Controllers\Admin\MeterImportController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\AuditlogController;
use App\Http\Controllers\Estate\EstateServiceController;
use App\Http\Controllers\Meter\MeterController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transformer\TransformerController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UtilitiesPaymentsExport;;
use Illuminate\Support\Facades\Response;




Route::get('/export-utilities', function (Illuminate\Http\Request $request) {
    $userId = $request->input('user_id');
    $estateId = $request->input('estate_id');

    return Excel::download(new UtilitiesPaymentsExport($userId, $estateId), 'utilities.xlsx');
});

Route::get('/export-customers', function (Illuminate\Http\Request $request) {
    $userId = $request->input('user_id');
    $estateId = $request->input('estate_id');

    return Excel::download(new CustomerExport($estateId), 'Customers.xlsx');
});


Route::get('/export-meters', function () {
    $userId = request()->get('user_id');
    $estateId = request()->get('estate_id');

    return Excel::download(new MeterExport($userId, $estateId), 'meters.xlsx');
});

Route::get('/fetch-tariff', [MeterController::class, 'fetchTariff']);
Route::get('export-metertransactions', [ExportControler::class, 'exportmetertransactions']);


Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


Route::get('/get-tariffs/{estate_id}', function ($estate_id) {
    $tariffs = App\Models\TarrifState::where('estate_id', $estate_id)->get(['amount', 'tariff_id']);
    return response()->json($tariffs);
});

Route::get('enkpay-payment', [TransactionController::class, 'enkpay_payment_verify']);


Route::any('verify2fa', [AuthController::class, 'verify2fa']);
Route::any('verify2fa-code', [AuthController::class, 'verify2fa_view']);
Route::any('resolve-account', [DashboardContoller::class, 'resolve_account']);



Route::get('onboarding', [DashboardContoller::class, 'onboarding_estate']);
Route::post('estate-onboarding', [DashboardContoller::class, 'register_now']);
Route::get('onboarding-pending', [DashboardContoller::class, 'pending_onboarding']);



Route::get('/', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return app(AuthController::class)->admin_login(request());
});


Route::post('login-now', [AuthController::class, 'login_now']);
Route::any('logout', [AuthController::class, 'log_out']);
Route::post('verify-code', [AuthController::class, 'verify_code']);
Route::get('resend_email_code', [AuthController::class, 'resend_email_code']);


Route::get('/search-meters', [MeterController::class, 'searchMeters']);
Route::get('/search-meter', [MeterController::class, 'searchMeter']);



Route::any('pay-flutter', [TransactionController::class, 'flutter_payment']);
Route::any('payment-check', [TransactionController::class, 'flutter_verify']);
Route::any('paystack-check', [TransactionController::class, 'paystack_verify']);
Route::any('fund_wallet', [TransactionController::class, 'fund_wallet']);


Route::any('set-2fa', [AuthController::class, 'set_2fa']);
Route::any('code', [AuthController::class, 'code']);
Route::any('auth_login', [AuthController::class, 'login.blade.php']);
Route::any('resend_code', [AuthController::class, 'resend_code']);
Route::any('verify_code', [AuthController::class, 'verify_code']);
Route::get('code', [AuthController::class, 'code']);
Route::get('auth-code', [AuthController::class, 'auth_code']);
Route::get('forget-password', [AuthController::class, 'forgot_password']);
Route::post('email-forget-password', [AuthController::class, 'email_forgot_password']);
Route::get('resetpassword', [AuthController::class, 'reset_password']);
Route::post('resetpasswordnow', [AuthController::class, 'reset_password_now']);
Route::get('verifyemail', [AuthController::class, 'verifyemail']);
Route::get('auth-code', [AuthController::class, 'auth_code']);




Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'blockaccess']], function () {

    Route::get('onboarding-email', [DashboardContoller::class, 'onboarding_email']);
    Route::post('setup_paystack', [DashboardContoller::class, 'setup_paystack']);

    Route::get('pay-utility', [DashboardContoller::class, 'pay_utility']);
    Route::get('unpay-utility', [DashboardContoller::class, 'unpay_utility']);




    Route::post('import-users', [CustomerImportController::class, 'import'])->name('customers.import');
    Route::post('import-meters', [MeterImportController::class, 'import'])->name('meters.import');


    Route::get('admin-dashboard', [DashboardContoller::class, 'index']);
    Route::get('users-list', [DashboardContoller::class, 'list_users']);
    Route::get('customers', [DashboardContoller::class, 'list_customers']);
    Route::get('filter-customer', [DashboardContoller::class, 'filter_customers']);




    Route::get('user-activate', [DashboardContoller::class, 'user_activate']);
    Route::get('user-deactivate', [DashboardContoller::class, 'user_deactivate']);


    Route::get('new-user', [DashboardContoller::class, 'new_user']);
    Route::get('new-customer', [DashboardContoller::class, 'new_customer']);
    Route::post('add-new-user', [DashboardContoller::class, 'add_new_user']);
    Route::post('add-new-customer', [DashboardContoller::class, 'add_new_customer']);

    Route::get('user-delete', [DashboardContoller::class, 'delete_user']);
    Route::get('view-user', [DashboardContoller::class, 'view_user']);
    Route::post('update-user', [DashboardContoller::class, 'update_user']);
    Route::post('update_user_email', [DashboardContoller::class, 'update_user_email']);
    Route::get('send-token-email', [DashboardContoller::class, 'send_token_email']);
    Route::post('update-passsword-now', [DashboardContoller::class, 'update_password_now']);
    Route::get('update_passsword_view', [DashboardContoller::class, 'update_password_view']);




    //Estates
    Route::get('estate', [EstateController::class, 'estate_index']);
    Route::get('new-estate', [EstateController::class, 'estate_new']);
    Route::post('estate-store', [EstateController::class, 'estate_store']);
    Route::post('estate-update-tariff', [EstateController::class, 'estate_update_tariff']);
    Route::get('view-estate', [EstateController::class, 'estate_view']);
    Route::post('estate-update-info', [EstateController::class, 'estate_update']);
    Route::post('estate-update-utilities', [EstateController::class, 'estate_update_utilities']);
    Route::post('update-duration', [EstateController::class, 'update_duration']);
    Route::get('estate-service', [EstateServiceController::class, 'index']);
    Route::get('view-service', [EstateServiceController::class, 'view_service']);
    Route::post('add-new-service-list', [EstateServiceController::class, 'add_new_service']);
    Route::post('service-update', [EstateServiceController::class, 'service_update']);
    Route::get('delete-comment', [EstateServiceController::class, 'delete_comment']);
    Route::get('service-delete', [EstateServiceController::class, 'delete_service']);
    Route::get('service-deactivate', [EstateServiceController::class, 'deactivate_service']);
    Route::get('service-activate', [EstateServiceController::class, 'activate_service']);
    Route::post('estate-update-vat', [EstateServiceController::class, 'estate_update_vat']);
    Route::post('estate-update-minpur', [EstateServiceController::class, 'estate_update_minpur']);








    //Access TOken
    Route::get('access-token', [AccessTokenConroller::class, 'index']);
    Route::get('token-delete', [AccessTokenConroller::class, 'delete_token']);
    Route::get('token-activate', [AccessTokenConroller::class, 'activate_token']);
    Route::get('token-deactivate', [AccessTokenConroller::class, 'deactivate_token']);


    Route::get('estate-delete', [EstateController::class, 'estate_delete']);


    Route::get('estate-activate', [EstateController::class, 'estate_activate']);
    Route::get('estate-deactivate', [EstateController::class, 'estate_deactivate']);


    //Organization
    Route::get('organization', [DashboardContoller::class, 'organization_index']);
    Route::get('new-organization', [DashboardContoller::class, 'organization_new']);
    Route::post('organization-store', [DashboardContoller::class, 'organization_store']);
    Route::get('view-organization', [DashboardContoller::class, 'organization_view']);
    Route::post('organization-update', [DashboardContoller::class, 'organization_update']);
    Route::get('organization-delete', [DashboardContoller::class, 'organization_delete']);
    Route::post('set-percentage', [DashboardContoller::class, 'set_percentage']);


    //Assets
    Route::get('asset', [AssetController::class, 'asset_index']);
    Route::get('new-asset', [AssetController::class, 'asset_new']);
    Route::post('asset-store', [AssetController::class, 'asset_store']);
    Route::get('view-asset', [AssetController::class, 'asset_view']);
    Route::post('asset-update', [AssetController::class, 'asset_update']);
    Route::get('asset-delete', [AssetController::class, 'asset_delete']);


    //utilities
    Route::get('utility', [DashboardContoller::class, 'utility_index']);
    Route::get('utility-store', [DashboardContoller::class, 'utility_store']);
    Route::get('utility-update', [DashboardContoller::class, 'utility_update']);
    Route::get('utility-delete', [DashboardContoller::class, 'utility_delete']);


    Route::get('meter-list', [MeterController::class, 'list_meter']);
    Route::post('filter-meter', [MeterController::class, 'filter_by_estate']);
    Route::post('update-meter-info', [MeterController::class, 'update_meter_info']);
    Route::post('update-meter', [MeterController::class, 'update_meter']);
    Route::get('new-meter', [MeterController::class, 'new_meter']);
    Route::post('add-new-meter', [MeterController::class, 'add_new_meter']);
    Route::get('meter-delete', [MeterController::class, 'delete_meter']);
    Route::get('edit-delete', [MeterController::class, 'delete_meter']);
    Route::get('view-meter', [MeterController::class, 'view_meter']);




    Route::get('meter-activate', [MeterController::class, 'meter_activate']);
    Route::get('meter-deactivate', [MeterController::class, 'meter_deactivate']);


    Route::get('transformer-list', [TransformerController::class, 'list_transformer']);
    Route::get('new-transformer', [TransformerController::class, 'new_transformer']);
    Route::get('view-transformer', [TransformerController::class, 'view_transformer']);
    Route::post('add-new-transformer', [TransformerController::class, 'add_new_transformer']);
    Route::post('update-transformer', [TransformerController::class, 'update_transformer']);
    Route::get('transformer-delete', [TransformerController::class, 'delete_transformer']);


    Route::get('settings', [DashboardContoller::class, 'settings']);
    Route::post('features', [DashboardContoller::class, 'update_feat']);
    Route::post('payment-keys', [DashboardContoller::class, 'update_pay']);
    Route::post('admin-fee-update', [DashboardContoller::class, 'admin_fee_update']);
    Route::post('support-set', [DashboardContoller::class, 'support_set']);
    Route::get('update-utility', [DashboardContoller::class, 'update_utility']);
    Route::get('delete-utility', [DashboardContoller::class, 'delete_utility']);


    Route::get('tariff-list', [TariffController::class, 'tariff_list']);
    Route::get('new-tariff', [TariffController::class, 'new_tariff']);
    Route::any('add-new-Tariff', [TariffController::class, 'add_new_Tariff']);

    Route::post('add_new_state_Tariff', [TariffController::class, 'add_new_state_Tariff']);


    Route::get('delete-tariff', [TariffController::class, 'delete_tariff']);
    Route::get('tariff-delete', [TariffController::class, 'delete_tariff']);
    Route::get('view-tariff', [TariffController::class, 'view_tariff']);
    Route::post('update-the-tariff', [TariffController::class, 'update_the_tariff']);


    Route::post('update-nepa', [TariffController::class, 'update_nepa']);
    Route::post('update-gen', [TariffController::class, 'update_gen']);

    Route::get('detach-gen-tariff', [TariffController::class, 'detach_gen_tariff']);
    Route::get('detach-nepa-tariff', [TariffController::class, 'detach_nepa_tariff']);





    //Meter token
    Route::post('generate-kct-token', [MeterController::class, 'kct_token']);
    Route::post('generate-token', [MeterController::class, 'generate_meter_token']);
    Route::get('detach-meter', [MeterController::class, 'detach_meter']);






    //REPORT
    Route::get('report-transaction', [TransactionController::class, 'transaction_reports']);
    Route::get('report-meters', [MeterController::class, 'meter_report']);
    Route::post('search-trx', [TransactionController::class, 'search_trx']);
    Route::post('search-utility-trx', [TransactionController::class, 'search_utility_trx']);
    Route::get('utility-payment', [TransactionController::class, 'utility_payment']);
    Route::get('uncomplete-payment', [TransactionController::class, 'uncomplete_payment']);
    Route::get('complete-payment', [TransactionController::class, 'complete_payment']);



    //token
    Route::get('credit-token', [TokenController::class, 'credit_token_index']);
    Route::get('compensation-token', [TokenController::class, 'compensation_index']);
    Route::get('tamper-token', [TokenController::class, 'tamper_index']);
    Route::get('kct-token', [TokenController::class, 'kct_token_index']);
    Route::get('clear-credit-token', [TokenController::class, 'clear_credit_token_index']);

    Route::post('validate-meter', [TokenController::class, 'validate_meter']);
    Route::post('validate-kct-meter', [TokenController::class, 'validate_kct_meter']);
    Route::post('validate-tamper-meter', [TokenController::class, 'validate_tamper_meter']);
    Route::post('validate-compensation-meter', [TokenController::class, 'validate_compensation_meter']);
    Route::post('validate-clear-credit-meter', [TokenController::class, 'validate_clear_credit_meter']);




    Route::post('generate-credit-meter-token', [TokenController::class, 'generate_credit_meter_token']);
    Route::post('generate-compensation-meter-token', [TokenController::class, 'generate_compensation_meter_token']);
    Route::post('generate-tamper-meter-token', [TokenController::class, 'generate_tamper_meter_token']);
    Route::post('generate-kctclear-token', [TokenController::class, 'generate_kctclear_token']);
    Route::post('generate-clear-credit-meter-token', [TokenController::class, 'generate_clear_credit_meter_token']);


    Route::any('paystack-check-web', [TokenController::class, 'paystack_verify_web']);
    Route::any('paystack-check-kct', [TokenController::class, 'paystack_verify_kct']);
    Route::any('paystack-check-web-tamper', [TokenController::class, 'paystack_verify_web_tamper']);
    Route::any('flutter-verify-tamper', [TokenController::class, 'flutter_verify_web_tamper']);
    Route::any('flutter-verify-kct', [TokenController::class, 'flutter_verify_kct']);
    Route::any('pay-flutter-web', [TokenController::class, 'flutter_verify_web']);
    Route::any('paystack-clear-credit', [TokenController::class, 'paystack_clear_credit']);
    Route::any('flutter-verify-clear-credit', [TokenController::class, 'flutter_verify_clear_credit']);
    Route::any('enkpay-payment', [TransactionController::class, 'enkpay_payment_verify']);





    Route::any('recepit', [TokenController::class, 'recepit']);
    Route::any('retry-generate-tamper-token', [TokenController::class, 'retry_generate_tamper-token']);
    Route::any('retry-generate-credit-token', [TokenController::class, 'retry_generate_credit_token']);




    Route::post('add-new-tariffstate', [TariffController::class, 'add_state_tariff']);
    Route::post('update-tariffstate', [TariffController::class, 'update_tariffstate']);




    //POS

    Route::get('pos-index', [PosController::class, 'index']);
    Route::get('new-merchant', [PosController::class, 'new_merchant']);
    Route::post('add-merchant', [PosController::class, 'add_merchant']);
    Route::get('delete-merchant', [PosController::class, 'delete_merchant']);



    //Audit
    Route::get('tariff_audit', [AuditlogController::class, 'tariff_audit']);
    Route::get('utility_pay_audit', [AuditlogController::class, 'utility_payment_audit']);


















});


// test middleware
Route::get('/test_middleware', function () {
    return "2FA middleware work!";
})->middleware(['auth', '2fa']);




