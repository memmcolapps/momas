<?php

namespace App\Http\Controllers\Estate;

use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\Logger;
use App\Models\LoggedIssue;
use App\Models\Meter;
use App\Services\StandardResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IssueApiController extends Controller
{
    /**
     * Get available issue categories, priorities, and default resident info.
     * Useful for populating dropdowns and pre-filling the mobile log issue form.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        try {
            $user = Auth::user();

            $estateName = null;
            if ($user && $user->estate_id) {
                $estate = Estate::find($user->estate_id);
                $estateName = $estate?->title ?? $user->estate_name;
            }

            $customerName = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));

            $data = [
                'categories' => LoggedIssue::CATEGORIES,
                'priorities' => [
                    ['value' => 'low', 'label' => 'Low'],
                    ['value' => 'medium', 'label' => 'Medium'],
                    ['value' => 'high', 'label' => 'High'],
                    ['value' => 'urgent', 'label' => 'Urgent'],
                ],
                'default_resident_info' => [
                    'user_id'        => $user?->id,
                    'customer_name'  => $customerName ?: null,
                    'customer_email' => $user?->email,
                    'customer_phone' => $user?->phone,
                    'meter_no'       => $user?->meterNo,
                    'house_no'       => $user?->address,
                    'estate_id'      => $user?->estate_id,
                    'estate_name'    => $estateName,
                ],
            ];

            return response()->json([
                'status'  => true,
                'message' => 'Issue categories and form options retrieved successfully',
                'data'    => $data,
            ], 200);
        } catch (Exception $e) {
            Logger::error('IssueApiController categories error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Unable to retrieve issue options', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log a new issue from mobile application.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'title'          => 'required|string|max:255',
                'category'       => 'required|string|max:100',
                'description'    => 'required|string',
                'priority'       => 'nullable|string|in:low,medium,high,urgent',
                'customer_name'  => 'nullable|string|max:255',
                'customer_email' => 'nullable|email|max:255',
                'customer_phone' => 'nullable|string|max:50',
                'meter_no'       => 'nullable|string|max:50',
                'house_no'       => 'nullable|string|max:100',
                'estate_id'      => 'nullable|integer|exists:estates,id',
                'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:10240',
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(422, 'Validation Error', [
                    'validation_error' => $validator->errors(),
                ]);
            }

            // Determine Estate ID
            $estateId = $request->estate_id ?? $user?->estate_id;
            if (!$estateId) {
                return StandardResponse::error(422, 'Estate is required. Please ensure user is attached to an estate.');
            }

            // Fallback user details
            $fullName = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));
            $customerName  = $request->customer_name ?: ($fullName ?: 'Resident');
            $customerEmail = $request->customer_email ?: $user?->email;
            $customerPhone = $request->customer_phone ?: $user?->phone;
            $meterNo       = $request->meter_no ?: $user?->meterNo;
            $houseNo       = $request->house_no ?: $user?->address;
            $priority      = strtolower($request->priority ?: 'medium');

            // Handle File Attachment Upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $destination = public_path('uploads/issues');
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                $file->move($destination, $fileName);
                $attachmentPath = 'uploads/issues/' . $fileName;
            } elseif ($request->filled('attachment_base64')) {
                // Support base64 image strings from mobile
                $attachmentPath = $this->saveBase64Attachment($request->attachment_base64);
            }

            $ticketId = LoggedIssue::generateTicketId();

            $issue = LoggedIssue::create([
                'ticket_id'      => $ticketId,
                'estate_id'      => $estateId,
                'user_id'        => $user?->id,
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'meter_no'       => $meterNo,
                'house_no'       => $houseNo,
                'category'       => $request->category,
                'priority'       => $priority,
                'title'          => $request->title,
                'description'    => $request->description,
                'attachment'     => $attachmentPath,
                'status'         => LoggedIssue::STATUS_OPEN,
            ]);

            Logger::info("New mobile issue logged: {$ticketId} by User ID: " . ($user?->id ?? 'guest'));

            return response()->json([
                'status'  => true,
                'message' => 'Issue logged successfully. Your ticket ID is ' . $ticketId,
                'data'    => $issue->fresh(['estate:id,title']),
            ], 201);
        } catch (Exception $e) {
            Logger::error('IssueApiController store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Failed to log issue. Please try again.', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retrieve all issues logged by the authenticated mobile user.
     * Supports filtering by status, category, priority, and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myIssues(Request $request)
    {
        try {
            $user = Auth::user();

            $query = LoggedIssue::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    if ($user->phone) {
                        $q->orWhere('customer_phone', $user->phone);
                    }
                    if ($user->meterNo) {
                        $q->orWhere('meter_no', $user->meterNo);
                    }
                })
                ->with(['estate:id,title', 'resolver:id,first_name,last_name']);

            // Filter by Status (0: Open, 1: In Progress, 2: Resolved, 3: Closed)
            if ($request->filled('status') && $request->status !== 'all') {
                $statusMap = [
                    'open'        => LoggedIssue::STATUS_OPEN,
                    'in_progress' => LoggedIssue::STATUS_IN_PROGRESS,
                    'resolved'    => LoggedIssue::STATUS_RESOLVED,
                    'closed'      => LoggedIssue::STATUS_CLOSED,
                ];
                $val = is_numeric($request->status) ? (int) $request->status : ($statusMap[strtolower($request->status)] ?? null);
                if ($val !== null) {
                    $query->where('status', $val);
                }
            }

            // Filter by Category
            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            // Filter by Priority
            if ($request->filled('priority') && $request->priority !== 'all') {
                $query->where('priority', strtolower($request->priority));
            }

            // Search by Ticket ID or title
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_id', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('meter_no', 'like', "%{$search}%");
                });
            }

            // User Summary Counts
            $baseUserQuery = LoggedIssue::query()->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->phone) {
                    $q->orWhere('customer_phone', $user->phone);
                }
                if ($user->meterNo) {
                    $q->orWhere('meter_no', $user->meterNo);
                }
            });

            $summary = [
                'total'       => (clone $baseUserQuery)->count(),
                'open'        => (clone $baseUserQuery)->where('status', LoggedIssue::STATUS_OPEN)->count(),
                'in_progress' => (clone $baseUserQuery)->where('status', LoggedIssue::STATUS_IN_PROGRESS)->count(),
                'resolved'    => (clone $baseUserQuery)->where('status', LoggedIssue::STATUS_RESOLVED)->count(),
                'closed'      => (clone $baseUserQuery)->where('status', LoggedIssue::STATUS_CLOSED)->count(),
            ];

            $perPage = (int) ($request->per_page ?: 15);
            $issues = $query->latest('id')->paginate($perPage);

            return response()->json([
                'status'  => true,
                'message' => 'Issues retrieved successfully',
                'data'    => [
                    'summary'    => $summary,
                    'issues'     => $issues->items(),
                    'pagination' => [
                        'current_page' => $issues->currentPage(),
                        'last_page'    => $issues->lastPage(),
                        'per_page'     => $issues->perPage(),
                        'total'        => $issues->total(),
                    ],
                ],
            ], 200);
        } catch (Exception $e) {
            Logger::error('IssueApiController myIssues error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Unable to retrieve issues', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get details of a specific issue.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'id'        => 'nullable|integer',
                'ticket_id' => 'nullable|string',
            ]);

            if ($validator->fails() || (!$request->filled('id') && !$request->filled('ticket_id'))) {
                return StandardResponse::error(422, 'Please provide issue id or ticket_id');
            }

            $query = LoggedIssue::with([
                'estate:id,title,address',
                'resolver:id,first_name,last_name,email',
            ]);

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            } else {
                $query->where('ticket_id', trim($request->ticket_id));
            }

            $issue = $query->first();

            if (!$issue) {
                return StandardResponse::error(404, 'Issue ticket not found');
            }

            // Security authorization: Ensure user owns the ticket or belongs to the same estate/admin
            $isOwner = ($issue->user_id && $issue->user_id == $user->id)
                || ($user->phone && $issue->customer_phone == $user->phone)
                || ($user->meterNo && $issue->meter_no == $user->meterNo);

            $isAdmin = in_array((int) $user->role, [0, 3, 4]);

            if (!$isOwner && !$isAdmin) {
                return StandardResponse::error(403, 'Unauthorized access to this issue ticket');
            }

            return response()->json([
                'status'  => true,
                'message' => 'Issue details retrieved successfully',
                'data'    => $issue,
            ], 200);
        } catch (Exception $e) {
            Logger::error('IssueApiController show error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Unable to retrieve issue details', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Close/cancel an issue by the resident.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function close(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'id'        => 'nullable|integer',
                'ticket_id' => 'nullable|string',
                'reason'    => 'nullable|string|max:500',
            ]);

            if ($validator->fails() || (!$request->filled('id') && !$request->filled('ticket_id'))) {
                return StandardResponse::error(422, 'Please provide issue id or ticket_id');
            }

            $query = LoggedIssue::query();
            if ($request->filled('id')) {
                $query->where('id', $request->id);
            } else {
                $query->where('ticket_id', trim($request->ticket_id));
            }

            $issue = $query->first();
            if (!$issue) {
                return StandardResponse::error(404, 'Issue ticket not found');
            }

            // Authorization
            $isOwner = ($issue->user_id && $issue->user_id == $user->id)
                || ($user->phone && $issue->customer_phone == $user->phone);

            if (!$isOwner && !in_array((int) $user->role, [0, 3, 4])) {
                return StandardResponse::error(403, 'Unauthorized to close this issue');
            }

            if ($issue->status === LoggedIssue::STATUS_CLOSED) {
                return StandardResponse::error(400, 'Issue is already closed');
            }

            $reason = $request->reason ? 'Resident closed: ' . $request->reason : 'Closed by resident via mobile';
            $issue->status = LoggedIssue::STATUS_CLOSED;
            $issue->resolution_notes = $issue->resolution_notes ? $issue->resolution_notes . " | " . $reason : $reason;
            $issue->save();

            return response()->json([
                'status'  => true,
                'message' => 'Issue ticket ' . $issue->ticket_id . ' has been closed',
                'data'    => $issue->fresh(),
            ], 200);
        } catch (Exception $e) {
            Logger::error('IssueApiController close error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Unable to close issue', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Public or authenticated ticket status tracker by Ticket ID.
     * Allows mobile users to quickly track any ticket using ticket_id.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(422, 'ticket_id is required');
            }

            $issue = LoggedIssue::where('ticket_id', trim($request->ticket_id))
                ->with(['estate:id,title'])
                ->first();

            if (!$issue) {
                return StandardResponse::error(404, 'No issue found with ticket ID: ' . $request->ticket_id);
            }

            $trackingData = [
                'ticket_id'        => $issue->ticket_id,
                'title'            => $issue->title,
                'category'         => $issue->category,
                'priority'         => $issue->priority,
                'priority_badge'   => $issue->priority_badge,
                'status'           => $issue->status,
                'status_label'     => $issue->status_label,
                'status_badge'     => $issue->status_badge,
                'is_resolved'      => in_array($issue->status, [LoggedIssue::STATUS_RESOLVED, LoggedIssue::STATUS_CLOSED]),
                'date_logged'      => $issue->created_at?->format('Y-m-d H:i:s'),
                'date_resolved'    => $issue->resolved_at?->format('Y-m-d H:i:s'),
                'resolution_notes' => $issue->resolution_notes,
                'estate_name'      => $issue->estate?->title,
            ];

            return response()->json([
                'status'  => true,
                'message' => 'Ticket tracking information retrieved',
                'data'    => $trackingData,
            ], 200);
        } catch (Exception $e) {
            Logger::error('IssueApiController track error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(500, 'Unable to track ticket', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Helper to decode and store base64 attachment image.
     */
    private function saveBase64Attachment(string $base64String): ?string
    {
        try {
            $destination = public_path('uploads/issues');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $extension = 'jpg';
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
                $extension = strtolower($type[1]);
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }
            }

            $decoded = base64_decode($base64String);
            if ($decoded === false) {
                return null;
            }

            $fileName = time() . '_' . Str::random(8) . '.' . $extension;
            file_put_contents($destination . '/' . $fileName, $decoded);

            return 'uploads/issues/' . $fileName;
        } catch (Exception $e) {
            Logger::error('Failed to save base64 issue attachment: ' . $e->getMessage());
            return null;
        }
    }
}
