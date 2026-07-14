<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\EstateModFeature;
use App\Models\Meter;
use App\Models\ModFeature;
use App\Models\Setting;
use App\Models\Tariff;
use App\Models\User;
use App\Models\Utility;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstateController extends Controller
{
    public function estate_index(request $request)
    {
        $data['estate_list'] = Estate::latest()->paginate(20);
        $data['estate'] = Estate::count();



        return view('admin/estate/index', $data);

    }


    public function estate_new(request $request)
    {
        $data['estate_features'] = ModFeature::select([
                'title',
                'slug',
                'status'
            ])
            ->get();

        return view('admin/estate/create', $data);
    }


    public function estate_store(request $request)
    {

        if ($request->charge_fee_flat != null && $request->charge_fee_percent != null) {
            return back()->with('error', 'Enter only one charge fee');
        }


        if($request->account_number != null){

            $fl = Setting::where('id', 1)->first();
            $pksecret = $fl->paystack_secret;


            $data = [
                'business_name' => $request->title,
                'settlement_bank' => $request->settlement_bank,
                'account_number' => $request->account_number,
                'percentage_charge' => $request->percentage_charge,
                'description' => $request->description ?? '',
                'primary_contact_email' => $request->primary_contact_email,
                'primary_contact_name' => $request->primary_contact_name,
            ];

            try {
                $client = new Client();

                $response = $client->post('https://api.paystack.co/subaccount', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $pksecret,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data,
                ]);

                $body = json_decode($response->getBody(), true);
                return response()->json($body);
            } catch (\Exception $e) {

                return redirect('admin/estate')->with('error', $e->getMessage());

            }

        }




        $org = new estate();
        $org->title = $request->title;
        $org->state = $request->state;
        $org->lga = $request->lga;
        $org->city = $request->city;
        $org->ptype = $request->ptype;
        $org->paystack_subaccount = $request->paystack_subaccount;
        $org->flutterwave_subaccount = $request->flutterwave_subaccount;
        $org->account_no = $request->account_no;
        $org->charge_fee_precent = $request->charge_fee_precent;
        $org->charge_fee_flat = $request->charge_fee_flat;
        $org->bank = $request->bank;
        $org->account_name = $request->account_name;
        $org->account_no = $request->account_no;
        $org->ptype = $request->ptype;
        $org->status = 2;
        $org->admin_fee = $request->estate_admin_fee;
        $org->save();

        $estateId = $org->id;
        $features = ModFeature::all();

        foreach ($features as $feature) {
            $slug = $feature->slug;
            if ($request->has($slug)) {
                $status = $request->input($slug);
                EstateModFeature::updateOrCreate(
                    [
                        'estate_id' => $estateId,
                        'mod_feature_id' => $feature->id
                    ],
                    ['status' => $status]
                );
            }
        }




        return redirect('admin/estate')->with('message', 'Estate created successfully');
    }


    public function estate_view(request $request)
    {

        if (Auth::user()->role == 0) {
            try {
                $client = new Client();

                $response = $client->get('https://api.paystack.co/bank', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('PAYSTACK_SECRET_KEY'),
                        'Accept' => 'application/json',
                    ],
                ]);

                $banks = json_decode($response->getBody(), true);

            } catch (\Exception $e) {

                return redirect('admin/estate')->with('error', $e->getMessage());

            }


            $data['org'] = Estate::where('id', $request->id)->first();

            if (! isset($data['org'])) {
                return redirect(url('admin/estate'))->with('error', 'Estate Not Found');
            }

            $data['paystackbank'] = $banks;


            $data['tar'] = Tariff::where('estate_id', $request->id)->first();
            // $data['utl'] = Utility::where('estate_id', $request->id)->first() ?? null;
            $data['total_utility'] = Utility::where('estate_id', $request->id)->serviceCharge()->sum('amount');
            $data['utility'] = Utility::where('estate_id', $request->id)->whereNull('user_id')->get() ?? null;
            $data['service_charges'] = Utility::where('estate_id', $request->id)->serviceCharge()->get();
            $data['debt_utilities'] = Utility::where('estate_id', $request->id)->debt()->whereNull('user_id')->get();
            $data['total_meters'] = Meter::where('estate_id', $request->id)->count() ?? null;
            $data['customers'] = User::where('estate_id', $request->id)->count() ?? null;
            $data['estate_features'] = ModFeature::query()
                ->leftJoin('estate_mod_features', function ($join) use ($data) {
                    $join->on('mod_features.id', '=', 'estate_mod_features.mod_feature_id')
                        ->where('estate_mod_features.estate_id', $data['org']->id);
                })
                ->select([
                    'mod_features.title',
                    'mod_features.slug',
                    DB::raw('COALESCE(estate_mod_features.status, mod_features.status) as status'),
                ])
                ->get();


        } elseif (Auth::user()->role == 1) {


        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {


            $data['org'] = Estate::where('id', Auth::user()->estate_id)->first();
            $data['tar'] = Tariff::where('estate_id', Auth::user()->estate_id)->first();
            $data['utl'] = Utility::where('estate_id', Auth::user()->estate_id)->serviceCharge()->first() ?? null;
            $data['total_utility'] = Utility::where('estate_id', Auth::user()->estate_id)->serviceCharge()->sum('amount');

            $data['utility'] = Utility::where('estate_id', Auth::user()->estate_id)->with('user')->get() ?? null;
            $data['service_charges'] = Utility::where('estate_id', Auth::user()->estate_id)->serviceCharge()->get();
            $data['debt_utilities'] = Utility::where('estate_id', Auth::user()->estate_id)->debt()->whereNull('user_id')->get();


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }

        return view('admin/estate/view', $data);
    }


    public function estate_update(request $request)
    {

        if ($request->charge_fee_flat != null && $request->charge_fee_precent != null) {
            return back()->with('error', 'Enter only one charge fee');
        }


        Estate::where('id', $request->id)->update([
            'title' => $request->title,
            'status' => $request->status,
            'state' => $request->state,
            'city' => $request->city,
            'lga' => $request->lga,
            'ptype' => $request->ptype,
            'paystack_subaccount' => $request->paystack_subaccount,
            'flutterwave_subaccount' => $request->flutterwave_subaccount,
            'account_no' => $request->account_no,
            'bank' => $request->bank,
            'account_name' => $request->account_name,
            'charge_fee_flat' => $request->charge_fee_flat,
            'charge_fee_precent' => $request->charge_fee_precent,
            'pos_tariff_id' => $request->pos_tariff_id,
            'serial_no' => $request->serial_no,
            'admin_fee' => $request->estate_admin_fee,

        ]);
        return redirect('admin/estate')->with('message', 'Estate updated successfully');
    }


    public function estate_delete(request $request)
    {
        Estate::where('id', $request->id)->delete();
        return redirect('admin/estate')->with('message', 'Estate deleted successfully');
    }

    public function estate_update_tariff(request $request)
    {
        Tariff::where('estate_id', $request->id)->update([
            'estate_tariff_cost' => $request->amount,
            'vat' => $request->vat,
            'min_pur' => $request->min_pur,
            'max_pur' => $request->max_pur,

        ]);


        return back()->with('message', 'Tariff updated successfully');
    }

    public function estate_update_utilities(request $request)
    {



        try {

            $utilitiesData = json_decode($request->input('utilities_data'), true);
            if (is_array($utilitiesData)) {
                foreach ($utilitiesData as $utility) {
                    if (!empty($utility['title']) && !empty($utility['amount'])) {
                        $monthlyEndDate = null;
                        if (($utility['mode_of_payment'] ?? null) === 'monthly_payment' && !empty($utility['start_date']) && !empty($utility['payment_months'])) {
                            $monthlyEndDate = \Carbon\Carbon::parse($utility['start_date'])->addMonths((int) $utility['payment_months'])->toDateString();
                        }

                        Utility::create([
                            'title' => $utility['title'],
                            'amount' => $utility['amount'],
                            'duration' => $request->duration,
                            'estate_id' => $request->estate_id,
                            'type' => $utility['type'] ?? 'service_charge',
                            'start_date' => $utility['start_date'] ?? null,
                            'mode_of_payment' => $utility['mode_of_payment'] ?? null,
                            'payment_amount' => $utility['payment_amount'] ?? null,
                            'activated' => $utility['activated'] ?? false,
                            'operator_id' => auth()->id(),
                            'percent_payment' => $utility['percent_payment'] ?? null,
                            'payment_months' => $utility['payment_months'] ?? null,
                            'monthly_end_date' => $monthlyEndDate,
                        ]);
                    }
                }
            }

            $utility_amount = Utility::where('estate_id', $request->estate_id)->serviceCharge()->sum('amount');
            Estate::where('id', $request->estate_id)->update(['total_utility_amount' => $utility_amount]);

            return back()->with('message', 'Utilities Saved successfully');


        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }




    }


    public function update_duration(request $request)
    {



        Estate::where('id', $request->id)->update([
            'duration' => $request->duration,
        ]);


        return redirect()->back()->with('success', 'Duration updated successfully');


    }


    public function estate_deactivate(request $request)
    {

        Estate::where('id', $request->id)->update(['status' => 0]);

        return back()->with('message', "Estate Deactivated successfully");


    }


    public function estate_activate(request $request)
    {

        Estate::where('id', $request->id)->update(['status' => 2]);

        return back()->with('message', "Estate Activated successfully");


    }

    public function estate_feature_update(Request $request)
    {
        $estateId = $request->input('id');
        $features = ModFeature::all();

        foreach ($features as $feature) {
            $slug = $feature->slug;
            if ($request->has($slug)) {
                $status = $request->input($slug);
                EstateModFeature::updateOrCreate(
                    [
                        'estate_id' => $estateId,
                        'mod_feature_id' => $feature->id
                    ],
                    ['status' => $status]
                );
            }
        }

        return back()->with('message', 'Features updated successfully');
    }

    public function feature_update(Request $request)
    {
        $features = ModFeature::all();

        foreach ($features as $feature) {
            $slug = $feature->slug;
            if ($request->has($slug)) {
                $status = $request->input($slug);
                ModFeature::where('id', $feature->id)->update(['status' => $status]);
            }
        }

        return back()->with('message', 'Features updated successfully');
    }

    public function customer_store_utility(Request $request)
    {
        try {
            $monthlyEndDate = null;
            if ($request->mode_of_payment === 'monthly_payment' && $request->start_date && $request->payment_months) {
                $monthlyEndDate = \Carbon\Carbon::parse($request->start_date)->addMonths((int) $request->payment_months)->toDateString();
            }

            Utility::create([
                'user_id' => $request->user_id,
                'estate_id' => $request->estate_id,
                'type' => $request->input('type', 'debt'),
                'title' => $request->title,
                'amount' => $request->amount,
                'start_date' => $request->start_date,
                'mode_of_payment' => $request->mode_of_payment,
                'payment_amount' => $request->payment_amount,
                'activated' => $request->has('activated'),
                'operator_id' => auth()->id(),
                'percent_payment' => $request->percent_payment,
                'payment_months' => $request->payment_months,
                'monthly_end_date' => $monthlyEndDate,
            ]);

            return back()->with('message', 'Customer Utility Saved successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

}
