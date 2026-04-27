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
use App\Models\Utitlity;
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
            $data['utl'] = Utitlity::where('estate_id', $request->id)->first() ?? null;
            $data['total_utility'] = Utitlity::where('estate_id', $request->id)->sum('amount');
            $data['utility'] = Utitlity::where('estate_id', $request->id)->get() ?? null;
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
            $data['utl'] = Utitlity::where('estate_id', Auth::user()->estate_id)->first() ?? null;
            $data['total_utility'] = Utitlity::where('estate_id', Auth::user()->estate_id)->sum('amount');


            $data['utility'] = Utitlity::where('estate_id', Auth::user()->estate_id)->get() ?? null;


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
                        Utitlity::create([
                            'title' => $utility['title'],
                            'amount' => $utility['amount'],
                            'duration' => $request->duration,
                            'estate_id' => $request->estate_id,

                        ]);
                    }
                }
            }

            $utility_amount = Utitlity::where('estate_id', $request->estate_id)->sum('amount');
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


}
