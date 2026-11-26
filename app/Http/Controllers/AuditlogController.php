<?php

namespace App\Http\Controllers;

use App\Models\Auditlog;
use App\Models\Estate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

class AuditlogController extends Controller
{

    public function tariff_audit(Request $request)
    {
        $tariff_logs = Audit::latest()->where('auditable_type', 'App\Models\TarrifState')->paginate(100);
        $total = Audit::latest()->where('auditable_type', 'App\Models\TarrifState')->count();
        $transactions = Transaction::latest()->take(100)->paginate(20);
        $estate = Estate::all();

        return view('admin.audit.tariffaudit', compact('tariff_logs', 'total', 'transactions', 'estate'));
    }

    public function utility_payment_audit(Request $request)
    {
        $tariff_logs = Audit::latest()->where('auditable_type', 'App\Models\UtilitiesPayment')->paginate(100);
        $total = Audit::latest()->where('auditable_type', 'App\Models\UtilitiesPayment')->count();
        $transactions = Transaction::latest()->take(100)->paginate(20);
        $estate = Estate::all();

        return view('admin.audit.utilitypayment', compact('tariff_logs', 'total', 'transactions', 'estate'));
    }

    public function general_audit(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only a superadmin can view general audit logs.');
        }

        $perPageOptions = [25, 50, 100, 200];
        $perPage = (int) $request->input('per_page', 50);

        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 50;
        }

        $query = Auditlog::query();

        $query->when($request->filled('q'), function ($query) use ($request) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($query) use ($search) {
                $query->where('user_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('action_identifier', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('entity_type', 'like', "%{$search}%")
                    ->orWhere('entity_id', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        });

        $query->when($request->filled('action_identifier'), function ($query) use ($request) {
            $query->where('action_identifier', $request->input('action_identifier'));
        });

        $query->when($request->filled('entity_type'), function ($query) use ($request) {
            $query->where('entity_type', $request->input('entity_type'));
        });

        $query->when($request->filled('estate_id'), function ($query) use ($request) {
            $query->where('estate_id', $request->input('estate_id'));
        });

        $query->when($request->filled('date_from'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        });

        $query->when($request->filled('date_to'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        });

        $audit_logs = $query->with(['user', 'estate'])->latest()->paginate($perPage)->withQueryString();

        $total = Auditlog::count();
        $todayTotal = Auditlog::whereDate('created_at', today())->count();
        $uniqueActors = Auditlog::whereNotNull('user_id')->distinct()->count('user_id');
        $uniqueActions = Auditlog::whereNotNull('action_identifier')->distinct()->count('action_identifier');

        $actionOptions = Auditlog::whereNotNull('action_identifier')
            ->distinct()
            ->orderBy('action_identifier')
            ->pluck('action_identifier');

        $entityOptions = Auditlog::whereNotNull('entity_type')
            ->distinct()
            ->orderBy('entity_type')
            ->pluck('entity_type')
            ->mapWithKeys(fn ($type) => [$type => class_basename($type)]);

        $estates = Estate::orderBy('title')->pluck('title', 'id');

        return view('admin.audit.generalaudit', compact(
            'audit_logs',
            'total',
            'todayTotal',
            'uniqueActors',
            'uniqueActions',
            'actionOptions',
            'entityOptions',
            'estates',
            'perPage',
            'perPageOptions'
        ));
    }
}
