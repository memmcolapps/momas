<?php

namespace App\Http\Controllers;

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
}
