@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Logged Issues</h4>
                    <p class="text-muted fs-13 mb-0">Manage resident complaints, meter issues, and service tickets</p>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logIssueModal">
                        <i data-feather="plus" class="me-1"></i> Log New Issue
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session()->get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Summary KPI Widgets -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                    <i data-feather="file-text" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Total Issues</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">{{ $stats['total'] }}</h3>
                                <span class="badge text-bg-light text-muted">All Tickets</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning-subtle rounded-circle p-2 me-2 border border-dashed border-warning">
                                    <i data-feather="clock" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Open / Pending</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-warning me-2">{{ $stats['open'] }}</h3>
                                <span class="badge text-bg-warning-subtle text-warning">Awaiting Action</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info-subtle rounded-circle p-2 me-2 border border-dashed border-info">
                                    <i data-feather="refresh-cw" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">In Progress</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-info me-2">{{ $stats['in_progress'] }}</h3>
                                <span class="badge text-bg-info-subtle text-info">Being Handled</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                    <i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Resolved</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-success me-2">{{ $stats['resolved'] }}</h3>
                                <span class="badge text-bg-success-subtle text-success">Closed: {{ $stats['closed'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="/admin/logged-issues" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fs-13">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           class="form-control" placeholder="Ticket ID, Resident, Meter, Title...">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fs-13">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="all" {{ request('status') === 'all' || !request()->has('status') ? 'selected' : '' }}>All Statuses</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Open / Pending</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>In Progress</option>
                                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Resolved</option>
                                        <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fs-13">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="all" {{ request('priority') === 'all' || !request()->has('priority') ? 'selected' : '' }}>All Priorities</option>
                                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fs-13">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="all" {{ request('category') === 'all' || !request()->has('category') ? 'selected' : '' }}>All Categories</option>
                                        <option value="Power / Meter" {{ request('category') === 'Power / Meter' ? 'selected' : '' }}>Power / Meter</option>
                                        <option value="Token Generation" {{ request('category') === 'Token Generation' ? 'selected' : '' }}>Token Generation</option>
                                        <option value="Billing / Tariff" {{ request('category') === 'Billing / Tariff' ? 'selected' : '' }}>Billing / Tariff</option>
                                        <option value="Water / Utility" {{ request('category') === 'Water / Utility' ? 'selected' : '' }}>Water / Utility</option>
                                        <option value="Security / Gate" {{ request('category') === 'Security / Gate' ? 'selected' : '' }}>Security / Gate</option>
                                        <option value="Facility Maintenance" {{ request('category') === 'Facility Maintenance' ? 'selected' : '' }}>Facility Maintenance</option>
                                        <option value="General" {{ request('category') === 'General' ? 'selected' : '' }}>General</option>
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i data-feather="filter" class="me-1"></i> Filter
                                    </button>
                                    <a href="/admin/logged-issues" class="btn btn-outline-secondary">
                                        <i data-feather="rotate-ccw"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issues Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-black mb-0">Issues List</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $issues->total() }} record(s)</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Resident / Reporter</th>
                                        <th>Meter No</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date Logged</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($issues as $issue)
                                        <tr>
                                            <td>
                                                <a href="/admin/view-issue?id={{ $issue->id }}" class="fw-semibold text-primary">
                                                    {{ $issue->ticket_id }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $issue->customer_name }}</div>
                                                <div class="fs-12 text-muted">
                                                    @if($issue->customer_phone)
                                                        <i data-feather="phone" style="width: 12px; height: 12px;"></i> {{ $issue->customer_phone }}
                                                    @endif
                                                    @if($issue->house_no)
                                                        &bull; {{ $issue->house_no }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($issue->meter_no)
                                                    <span class="badge text-bg-light border text-dark">{{ $issue->meter_no }}</span>
                                                @else
                                                    <span class="text-muted fs-12">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-dark">{{ $issue->category }}</span>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $issue->title }}">
                                                    {{ $issue->title }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-{{ $issue->priority_badge }} text-uppercase fs-11">
                                                    {{ $issue->priority }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-{{ $issue->status_badge }} fs-12">
                                                    {{ $issue->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fs-12 text-muted">{{ $issue->created_at->format('M d, Y H:i') }}</span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="/admin/view-issue?id={{ $issue->id }}" class="btn btn-sm btn-outline-primary" title="View & Manage">
                                                        <i data-feather="eye" style="width: 14px; height: 14px;"></i> View
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickStatusModal{{ $issue->id }}" title="Update Status">
                                                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </div>

                                                <!-- Quick Status Modal -->
                                                <div class="modal fade text-start" id="quickStatusModal{{ $issue->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Status: {{ $issue->ticket_id }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="/admin/update-issue-status" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $issue->id }}">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Status</label>
                                                                        <select name="status" class="form-select" required>
                                                                            <option value="0" {{ $issue->status === 0 ? 'selected' : '' }}>Open / Pending</option>
                                                                            <option value="1" {{ $issue->status === 1 ? 'selected' : '' }}>In Progress</option>
                                                                            <option value="2" {{ $issue->status === 2 ? 'selected' : '' }}>Resolved</option>
                                                                            <option value="3" {{ $issue->status === 3 ? 'selected' : '' }}>Closed</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Resolution Notes / Comments</label>
                                                                        <textarea name="resolution_notes" rows="3" class="form-control" placeholder="Enter notes or updates regarding this issue...">{{ $issue->resolution_notes }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Quick Status Modal -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i data-feather="inbox" style="width: 40px; height: 40px;" class="mb-2"></i>
                                                    <p class="mb-0">No logged issues found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-end mt-3">
                                {{ $issues->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal: Log New Issue -->
    <div class="modal fade" id="logIssueModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="logIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-5" id="logIssueModalLabel">
                        <i data-feather="plus-circle" class="me-1"></i> Log New Issue
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="/admin/store-issue" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <!-- SuperAdmin Estate selector if applicable -->
                        @if($isSuperAdmin)
                            <div class="mb-3">
                                <label class="form-label fw-medium">Select Estate <span class="text-danger">*</span></label>
                                <select name="estate_id" class="form-select" required>
                                    @foreach($estates as $estate)
                                        <option value="{{ $estate->id }}">{{ $estate->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Customer auto-fill selector -->
                        <div class="mb-3">
                            <label class="form-label fw-medium">Select Existing Resident / Customer (Optional)</label>
                            <select id="resident_select" class="form-select">
                                <option value="" selected>-- Type or Select Resident to Auto-Fill --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}"
                                            data-name="{{ $c->first_name }} {{ $c->last_name }}"
                                            data-email="{{ $c->email }}"
                                            data-phone="{{ $c->phone }}"
                                            data-meter="{{ $c->meter_number ?? '' }}"
                                            data-address="{{ $c->address ?? '' }}">
                                        {{ $c->first_name }} {{ $c->last_name }} ({{ $c->phone }} | {{ $c->email }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="user_id" id="issue_user_id">
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Resident Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="issue_customer_name" class="form-control" placeholder="e.g. John Doe" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="customer_phone" id="issue_customer_phone" class="form-control" placeholder="e.g. 08012345678">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="customer_email" id="issue_customer_email" class="form-control" placeholder="e.g. john@example.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Meter Number</label>
                                <input type="text" name="meter_no" id="issue_meter_no" class="form-control" placeholder="e.g. 01423456789">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">House / Unit No</label>
                                <input type="text" name="house_no" id="issue_house_no" class="form-control" placeholder="e.g. Block A, Flat 3">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Power / Meter">Power / Meter</option>
                                    <option value="Token Generation">Token Generation</option>
                                    <option value="Billing / Tariff">Billing / Tariff</option>
                                    <option value="Water / Utility">Water / Utility</option>
                                    <option value="Security / Gate">Security / Gate</option>
                                    <option value="Facility Maintenance">Facility Maintenance</option>
                                    <option value="General" selected>General</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-medium">Issue Subject / Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Brief summary of the issue" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                            <textarea name="description" rows="4" class="form-control" placeholder="Provide full details of the issue..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Attachment (Screenshot, Photo or Document - Max 10MB)</label>
                            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="check" class="me-1"></i> Submit Issue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to autofill resident info when selected -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const residentSelect = document.getElementById('resident_select');
            if (residentSelect) {
                residentSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    if (this.value) {
                        document.getElementById('issue_user_id').value = this.value;
                        document.getElementById('issue_customer_name').value = selected.getAttribute('data-name') || '';
                        document.getElementById('issue_customer_email').value = selected.getAttribute('data-email') || '';
                        document.getElementById('issue_customer_phone').value = selected.getAttribute('data-phone') || '';
                        document.getElementById('issue_meter_no').value = selected.getAttribute('data-meter') || '';
                        document.getElementById('issue_house_no').value = selected.getAttribute('data-address') || '';
                    } else {
                        document.getElementById('issue_user_id').value = '';
                    }
                });
            }
        });
    </script>

@endsection
