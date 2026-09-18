<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Beneficiary;
use App\Models\Estate;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function beneficiary_index(request $request)
    {
        $query = Beneficiary::with(['estate', 'bank'])->latest();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('beneficiary_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('beneficiary_account', 'like', '%'.$searchTerm.'%')
                    ->orWhere('bank_code', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('estate', fn($q) => $q->where('title', 'like', '%'.$searchTerm.'%'));
            });
        }

        $data['beneficiary_list'] = $query->paginate(20)->withQueryString();
        $data['beneficiary_count'] = Beneficiary::where('status', 2)->count();

        return view('admin/beneficiary/index', $data);
    }

    public function beneficiary_new(request $request)
    {
        $data['estate'] = Estate::where('status', 2)->get();
        $data['bank'] = Bank::whereNotNull('remita_code')->orderBy('name')->get();

        return view('admin/beneficiary/create', $data);
    }

    public function beneficiary_store(Request $request)
    {
        $request->validate([
            'estate_id' => 'required|integer|exists:estates,id',
            'beneficiary_account' => 'required|string',
            'bank_code' => 'required|string|exists:banks,remita_code',
            'beneficiary_name' => 'nullable|string',
            'deduct_fee_from' => 'nullable',
            'status' => 'nullable|in:0,2',
        ]);

        $lineItemsId = $request->line_items_id;
        if ($lineItemsId == null || $lineItemsId == '') {
            $lineItemsId = (string) (Beneficiary::where('estate_id', $request->estate_id)->count() + 1);
        }

        $beneficiary = new Beneficiary();
        $beneficiary->estate_id = $request->estate_id;
        $beneficiary->line_items_id = $lineItemsId;
        $beneficiary->beneficiary_name = $request->beneficiary_name;
        $beneficiary->beneficiary_account = $request->beneficiary_account;
        $beneficiary->bank_code = $request->bank_code;
        $beneficiary->deduct_fee_from = $request->boolean('deduct_fee_from') ? 1 : 0;
        $beneficiary->status = $request->status ?? 2;
        $beneficiary->save();

        return redirect('admin/beneficiary')->with('message', 'Beneficiary created successfully');
    }

    public function beneficiary_view(request $request)
    {
        $data['beneficiary'] = Beneficiary::with(['estate', 'bank'])
            ->where('id', $request->id)
            ->first();

        if (! $data['beneficiary']) {
            return redirect(url('admin/beneficiary'))->with('error', 'Beneficiary Not Found');
        }

        $data['estate'] = Estate::where('status', 2)->get();
        $data['bank'] = Bank::whereNotNull('remita_code')->orderBy('name')->get();

        return view('admin/beneficiary/view', $data);
    }

    public function beneficiary_update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:beneficiaries,id',
            'estate_id' => 'required|integer|exists:estates,id',
            'beneficiary_account' => 'required|string',
            'bank_code' => 'required|string|exists:banks,remita_code',
            'beneficiary_name' => 'nullable|string',
            'deduct_fee_from' => 'nullable',
            'status' => 'nullable|in:0,2',
        ]);

        $beneficiary = Beneficiary::where('id', $request->id)->first();

        if (! $beneficiary) {
            return redirect('admin/beneficiary')->with('error', 'Beneficiary Not Found');
        }

        $lineItemsId = $request->line_items_id;
        if ($lineItemsId == null || $lineItemsId == '') {
            $lineItemsId = (string) (Beneficiary::where('estate_id', $request->estate_id)->count() + 1);
        }

        $beneficiary->estate_id = $request->estate_id;
        $beneficiary->line_items_id = $lineItemsId;
        $beneficiary->beneficiary_name = $request->beneficiary_name;
        $beneficiary->beneficiary_account = $request->beneficiary_account;
        $beneficiary->bank_code = $request->bank_code;
        $beneficiary->deduct_fee_from = $request->boolean('deduct_fee_from') ? 1 : 0;
        $beneficiary->status = $request->status ?? 2;
        $beneficiary->save();

        return redirect('admin/beneficiary')->with('message', 'Beneficiary updated successfully');
    }

    public function beneficiary_delete(request $request)
    {
        Beneficiary::where('id', $request->id)->delete();

        return redirect('admin/beneficiary')->with('message', 'Beneficiary deleted successfully');
    }

    public function beneficiary_activate(request $request)
    {
        Beneficiary::where('id', $request->id)->update(['status' => 2]);

        return redirect('admin/beneficiary')->with('message', 'Beneficiary activated successfully');
    }

    public function beneficiary_deactivate(request $request)
    {
        Beneficiary::where('id', $request->id)->update(['status' => 0]);

        return redirect('admin/beneficiary')->with('message', 'Beneficiary deactivated successfully');
    }
}