<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\LoggedIssue;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoggedIssueController extends Controller
{
    /**
     * Display a listing of logged issues for the Estate Admin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        $estateId = $user->estate_id;

        $query = LoggedIssue::query()->with(['estate', 'user', 'resolver']);

        if (!$isSuperAdmin) {
            $query->where('estate_id', $estateId);
        } elseif ($request->filled('estate_id')) {
            $query->where('estate_id', $request->estate_id);
        }

        // Apply Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('meter_no', 'like', "%{$search}%")
                    ->orWhere('house_no', 'like', "%{$search}%");
            });
        }

        // Apply Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', (int) $request->status);
        }

        // Apply Priority Filter
        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Apply Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Apply Date Range Filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Statistics
        $baseStatsQuery = LoggedIssue::query();
        if (!$isSuperAdmin) {
            $baseStatsQuery->where('estate_id', $estateId);
        } elseif ($request->filled('estate_id')) {
            $baseStatsQuery->where('estate_id', $request->estate_id);
        }

        $stats = [
            'total'       => (clone $baseStatsQuery)->count(),
            'open'        => (clone $baseStatsQuery)->where('status', LoggedIssue::STATUS_OPEN)->count(),
            'in_progress' => (clone $baseStatsQuery)->where('status', LoggedIssue::STATUS_IN_PROGRESS)->count(),
            'resolved'    => (clone $baseStatsQuery)->where('status', LoggedIssue::STATUS_RESOLVED)->count(),
            'closed'      => (clone $baseStatsQuery)->where('status', LoggedIssue::STATUS_CLOSED)->count(),
        ];

        $issues = $query->latest()->paginate(20)->withQueryString();

        // Customer and meter lists for modal
        $customers = $isSuperAdmin
            ? User::where('role', 2)->latest()->take(100)->get()
            : User::where('estate_id', $estateId)->where('role', 2)->latest()->get();

        $meters = $isSuperAdmin
            ? Meter::latest()->take(100)->get()
            : Meter::where('estate_id', $estateId)->get();

        $estates = $isSuperAdmin ? Estate::all() : collect([$user->estate]);

        return view('admin.estate.issues.index', compact(
            'issues',
            'stats',
            'customers',
            'meters',
            'estates',
            'estateId',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created logged issue.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'priority'       => 'required|in:low,medium,high,urgent',
            'description'    => 'required|string',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'meter_no'       => 'nullable|string|max:50',
            'house_no'       => 'nullable|string|max:100',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
        ]);

        $estateId = Auth::user()->isSuperAdmin() && $request->filled('estate_id')
            ? $request->estate_id
            : Auth::user()->estate_id;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $destination = public_path('uploads/issues');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $fileName);
            $attachmentPath = 'uploads/issues/' . $fileName;
        }

        $ticketId = LoggedIssue::generateTicketId();

        $issue = LoggedIssue::create([
            'ticket_id'      => $ticketId,
            'estate_id'      => $estateId,
            'user_id'        => $request->user_id ?? null,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'meter_no'       => $request->meter_no,
            'house_no'       => $request->house_no,
            'category'       => $request->category,
            'priority'       => $request->priority,
            'title'          => $request->title,
            'description'    => $request->description,
            'attachment'     => $attachmentPath,
            'status'         => LoggedIssue::STATUS_OPEN,
        ]);

        return redirect()->back()->with('message', "Issue logged successfully! Ticket ID: {$issue->ticket_id}");
    }

    /**
     * Display the specified logged issue details.
     */
    public function show(Request $request)
    {
        $id = $request->id;
        $issue = LoggedIssue::with(['estate', 'user', 'resolver'])->findOrFail($id);

        // Security check for estate admin
        if (!Auth::user()->isSuperAdmin() && $issue->estate_id != Auth::user()->estate_id) {
            return redirect('admin/logged-issues')->with('error', 'Unauthorized access to this issue.');
        }

        return view('admin.estate.issues.view', compact('issue'));
    }

    /**
     * Update the status and resolution notes of the issue.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'               => 'required|exists:logged_issues,id',
            'status'           => 'required|in:0,1,2,3',
            'resolution_notes' => 'nullable|string',
        ]);

        $issue = LoggedIssue::findOrFail($request->id);

        if (!Auth::user()->isSuperAdmin() && $issue->estate_id != Auth::user()->estate_id) {
            return redirect('admin/logged-issues')->with('error', 'Unauthorized access.');
        }

        $newStatus = (int) $request->status;
        $issue->status = $newStatus;

        if ($request->filled('resolution_notes')) {
            $issue->resolution_notes = $request->resolution_notes;
        }

        if ($newStatus === LoggedIssue::STATUS_RESOLVED) {
            $issue->resolved_by = Auth::id();
            $issue->resolved_at = now();
        } elseif ($newStatus === LoggedIssue::STATUS_OPEN) {
            $issue->resolved_by = null;
            $issue->resolved_at = null;
        }

        $issue->save();

        return redirect()->back()->with('message', "Issue [{$issue->ticket_id}] updated successfully.");
    }

    /**
     * Remove the specified issue.
     */
    public function destroy(Request $request)
    {
        $issue = LoggedIssue::findOrFail($request->id);

        if (!Auth::user()->isSuperAdmin() && $issue->estate_id != Auth::user()->estate_id) {
            return redirect('admin/logged-issues')->with('error', 'Unauthorized access.');
        }

        $ticketId = $issue->ticket_id;
        $issue->delete();

        return redirect('admin/logged-issues')->with('message', "Issue [{$ticketId}] has been deleted.");
    }
}
