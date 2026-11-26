<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Estate;
use App\Models\EstateService;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\Token;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstateServiceController extends Controller
{
    public function index(request $request)
    {



        if(Auth::user()->role == 0){

            $data['services'] = EstateService::latest()->paginate(20);
            $data['service_count'] = EstateService::count();
            $data['estate_id'] = Auth::user()->estate_id;
            $data['prof_services'] = Service::all();
            $data['estate'] = Estate::all();





            return view('admin.estate.service-list', $data);



        } elseif(Auth::user()->role == 1){

        } elseif(Auth::user()->role == 2){

        } elseif(Auth::user()->role == 3){

            $data['services'] = EstateService::latest()->where('estate_id', Auth::user()->estate_id)->paginate(20);
            $data['service_count'] = EstateService::where('estate_id', Auth::user()->estate_id)->count();
            $data['estate_id'] = Auth::user()->estate_id;
            $data['prof_services'] = Service::all();




            return view('admin.estate.service-list', $data);

        } elseif(Auth::user()->role == 4){

        } elseif(Auth::user()->role == 5){

        } else{

        }






    }


    public function add_new_service(request $request)
    {

        $services = Service::where('id', $request->service)->first();
        $ser = new EstateService();
        $ser->professional_name = $request->professional_name;
        $ser->professional_email = $request->professional_email;
        $ser->professional_phone = $request->professional_phone;
        $ser->estate_id = $request->estate_id;
        $ser->service_id = $services->id;
        $ser->status = 2;
        $ser->service_title = $services->service_title;
        $ser->save();

        Audit::action('service.created', $ser);

        return back()->with('message', "Estate Service successfully added");


    }

    public function view_service(request $request)
    {


        $data['services'] = EstateService::where('id', $request->id)->first();
        $data['prof_services'] = Service::all();
        $data['comment'] = Comment::where('estate_service_id', $request->id)->get();

        if ($data['services']) {
            Audit::action('service.viewed', $data['services']);
        }

        return view('admin.estate.view-service', $data);


    }


    public function service_update(request $request)
    {

        $service = EstateService::where('id', $request->id)->first();

        if ($service) {
            $update = [
                'professional_name' => $request->professional_name,
                'professional_phone' => $request->professional_phone,
                'professional_email' => $request->professional_email,
            ];

            if($request->service != null){
                $services = Service::where('id', $request->service)->first();
                if ($services) {
                    $update['service_id'] = $services->id;
                    $update['service_title'] = $services->service_title;
                }
            }

            $service->update($update);

            Audit::action('service.updated', $service);
        }

        return back()->with('message', "Estate service Updated successfully");



    }


    public function delete_comment(request $request)
    {

        $comment = Comment::where('id', $request->id)->first();

        if ($comment) {
            $comment->delete();
            Audit::action('service.comment_deleted', $comment);
        }

        return back()->with('message', "Comment deleted successfully");


    }


    public function delete_service(request $request)
    {
        $service = EstateService::where('id', $request->id)->first();

        if ($service) {
            $service->delete();
            Audit::action('service.deleted', $service);
        }

        return back()->with('message', "Estate Service deleted successfully");

    }

    public function deactivate_service(request $request)
    {
        $service = EstateService::where('id', $request->id)->first();

        if ($service) {
            $service->update(['status' => 0]);
            Audit::action('service.deactivated', $service);
        }

        return back()->with('message', "Estate Service updated successfully");

    }

    public function activate_service(request $request)
    {
        $service = EstateService::where('id', $request->id)->first();

        if ($service) {
            $service->update(['status' => 2]);
            Audit::action('service.activated', $service);
        }

        return back()->with('message', "Estate Service updated successfully");

    }


    public function estate_update_vat(request $request)
    {
        $estate = Estate::find($request->estate_id);

        // Update the estate VAT
        if ($estate) {
            $estate->update(['estate_vat' => $request->vat]);
        }

        // Update all non-zero tariff rates for this estate with the new VAT percentage
        // First get all tariff IDs for this estate
        $tariffIds = Tariff::where('estate_id', $request->estate_id)->pluck('id');

        // Then update TarrifState records for those tariffs that have non-zero VAT
        TarrifState::whereIn('tariff_id', $tariffIds)
        ->where('vat', '!=', 0)  // Only update tariff rates that have non-zero VAT
        ->update(['vat' => $request->vat]);

        if ($estate) {
            Audit::action('estate.vat_updated', $estate, ['vat' => $request->vat]);
        }

        return back()->with('message', "Estate VAT updated successfully and applied to all tariff rates");
    }


    public function estate_update_minpur(request $request)
    {
        $request->validate([
            'emergency_token_max' => 'required|integer|min:1',
        ]);

        // Estate::where('id', $request->estate_id)->update(['min_pur' => $request->min_pur,  'max_pur' => $request->max_pur, 'vend_duration' => $request->vend_duration]);
        // Note: vend_duration commented out - column doesn't exist in DB and no form input for it
        $update = [
            'min_pur' => $request->min_pur,
            'max_pur' => $request->max_pur,
            'emergency_token_max' => $request->emergency_token_max,
        ];

        if ($request->has('minimum_vend_per_transaction')) {
            $update['minimum_vend_per_transaction'] = (bool)$request->minimum_vend_per_transaction;
        }

        $estate = Estate::find($request->estate_id);

        if ($estate) {
            $estate->update($update);

            Audit::action('estate.min_purchase_updated', $estate, ['min_purchase' => $request->min_pur]);
        }

        return back()->with('message', "Estate MIN/MAX VEND updated successfully");

    }

    public function estate_update_fee_accumulation(request $request)
    {
        $estate = Estate::find($request->estate_id);

        if ($estate) {
            $estate->update([
                'fee_accumulation_period' => $request->fee_accumulation_period
            ]);

            Audit::action('estate.fee_accumulation_updated', $estate, ['fee_accumulation_period' => $request->fee_accumulation_period]);
        }

        return back()->with('message', "Estate Fee Accumulation Period updated successfully");
    }

    public function estate_update_transaction_fee(request $request)
    {
        $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'transaction_fee_type' => 'required|in:' . implode(',', [
                \App\Constants\TransactionFeeType::FLAT,
                \App\Constants\TransactionFeeType::PERCENTAGE,
            ]),
            'transaction_fee' => 'required|numeric|min:0',
        ]);

        $estate = Estate::find($request->estate_id);

        if ($estate) {
            $estate->update([
                'transaction_fee_type' => $request->transaction_fee_type,
                'transaction_fee' => $request->transaction_fee,
            ]);

            Audit::action('estate.transaction_fee_updated', $estate, ['transaction_fee' => $request->transaction_fee]);
        }

        return back()->with('message', "Estate Transaction Fee updated successfully");
    }

    public function estate_update_payment_gateways(Request $request)
    {
        $request->validate([
            'estate_id' => 'required|exists:estates,id',
            'payment_gateways' => 'nullable|array',
            'payment_gateways.*' => 'string|in:paystack,flutterwave,remita,enkpay',
        ]);

        $estate_id = $request->estate_id;
        $new_gateways = array_values(array_unique(array_map('strtolower', $request->input('payment_gateways', []))));

        $current = app(\App\Services\ConfigManagementService::class)->getConfig('payment_gateways', $estate_id) ?? [];
        $current = array_map('strtolower', (array) $current);
        sort($current);
        sort($new_gateways);

        if ($current === $new_gateways) {
            return back()->with('message', 'No changes to payment gateways.');
        }

        app(\App\Services\ConfigManagementService::class)->put(
            'payment_gateways',
            $new_gateways,
            false,
            $estate_id,
            'Payment Gateways',
            null,
            'system'
        );

        $estate = Estate::find($estate_id);

        if ($estate) {
            Audit::action('estate.payment_gateways_updated', $estate, ['gateways' => $new_gateways]);
        }

        return back()->with('message', 'Estate payment gateways updated successfully');
    }

    public function create_service(request $request) {
        $estates = Estate::get(['title', 'id']);
        $services = Service::get(['service_title', 'id']);

        return view('admin.estate.create-service', [
            'estates' => $estates,
            'all_services' => $services,
        ]);
    }
}
