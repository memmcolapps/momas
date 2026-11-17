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

        $audit_logs = Auditlog::with('user')->latest()->paginate(100);
        $total = Auditlog::count();

        return view('admin.audit.generalaudit', compact('audit_logs', 'total'));
    }
}
