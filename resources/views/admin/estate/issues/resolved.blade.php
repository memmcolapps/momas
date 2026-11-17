@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Resolved Issues</h4>
                    <p class="text-muted fs-13 mb-0">Audit trail, resolution details, and historical records of resolved resident issues</p>
                </div>
                <div class="text-end mt-2 mt-sm-0 d-flex gap-2 justify-content-end">
                    <a href="/admin/logged-issues" class="btn btn-outline-primary">
                        <i data-feather="list" class="me-1" style="width: 14px; height: 14px;"></i> All Logged Issues
                    </a>
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

            <!-- KPI Summary Cards -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                    <i data-feather="check-circle" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Total Resolved</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-success me-2">{{ $stats['total_resolved'] }}</h3>
                                <span class="badge text-bg-light text-muted">Fixed & Closed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                    <i data-feather="calendar" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Resolved Today</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-primary me-2">{{ $stats['resolved_today'] }}</h3>
                                <span class="badge text-bg-primary-subtle text-primary">Today</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info-subtle rounded-circle p-2 me-2 border border-dashed border-info">
                                    <i data-feather="trending-up" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Resolved This Month</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-info me-2">{{ $stats['resolved_this_month'] }}</h3>
                                <span class="badge text-bg-info-subtle text-info">Current Month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                    <i data-feather="archive" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Closed Tickets</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-secondary me-2">{{ $stats['closed_only'] }}</h3>
                                <span class="badge text-bg-secondary-subtle text-secondary">Archived</span>
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
                            <form method="GET" action="/admin/resolved-issues" class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fs-13">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           class="form-control" placeholder="Ticket ID, Resident, Meter, Resolution notes...">
                                </div>

                                @if($isSuperAdmin && isset($estates) && count($estates) > 1)
                                    <div class="col-md-2">
                                        <label class="form-label fs-13">Estate</label>
                                        <select name="estate_id" class="form-select">
                                            <option value="">All Estates</option>
                                            @foreach($estates as $est)
                                                <option value="{{ $est->id }}" {{ request('estate_id') == $est->id ? 'selected' : '' }}>
                                                    {{ $est->estate_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

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

                                <div class="col-md-2">
                                    <label class="form-label fs-13">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="" {{ !request()->has('status') ? 'selected' : '' }}>Resolved & Closed</option>
                                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Resolved</option>
                                        <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i data-feather="filter" class="me-1"></i> Filter
                                    </button>
                                    <a href="/admin/resolved-issues" class="btn btn-outline-secondary">
                                        <i data-feather="rotate-ccw"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolved Issues Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-black mb-0">Resolved Issues History</h5>
                            <span class="badge bg-success-subtle text-success">{{ $issues->total() }} resolved record(s)</span>
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
                                        <th>Issue Title</th>
                                        <th>Resolution Notes</th>
                                        <th>Resolved By</th>
                                        <th>Date Resolved</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($issues as $issue)
                                        <tr>
                                            <td>
                                                <a href="/admin/view-issue?id={{ $issue->id }}" class="fw-bold text-primary">
                                                    {{ $issue->ticket_id }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $issue->customer_name }}</div>
                                                @if($issue->customer_phone)
                                                    <small class="text-muted d-block">{{ $issue->customer_phone }}</small>
                                                @endif
                                                @if($issue->house_no)
                                                    <small class="text-muted d-block">House: {{ $issue->house_no }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($issue->meter_no)
                                                    <span class="badge bg-light text-dark border">{{ $issue->meter_no }}</span>
                                                @else
                                                    <span class="text-muted fs-12">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-dark">{{ $issue->category }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($issue->title, 35) }}</span>
                                            </td>
                                            <td>
                                                @if($issue->resolution_notes)
                                                    <span class="text-muted fs-13" title="{{ $issue->resolution_notes }}">
                                                        {{ \Illuminate\Support\Str::limit($issue->resolution_notes, 45) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fst-italic fs-12">No notes recorded</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($issue->resolver)
                                                    <span class="badge bg-info-subtle text-info">
                                                        {{ $issue->resolver->first_name }} {{ $issue->resolver->last_name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted fs-12">System / Admin</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($issue->resolved_at)
                                                    <span class="fs-13 text-dark">{{ $issue->resolved_at->format('M d, Y H:i') }}</span>
                                                @else
                                                    <span class="fs-13 text-muted">{{ $issue->updated_at->format('M d, Y H:i') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge text-bg-{{ $issue->status_badge }}">{{ $issue->status_label }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/view-issue?id={{ $issue->id }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="View Ticket Details">
                                                    <i data-feather="eye" style="width: 14px; height: 14px;"></i> Details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-center">
                                                    <i data-feather="check-circle" class="text-muted mb-2" style="width: 48px; height: 48px; opacity: 0.5;"></i>
                                                    <h5 class="text-muted mt-2">No Resolved Issues Found</h5>
                                                    <p class="text-muted fs-13 mb-3">
                                                        @if(request()->anyFilled(['search', 'category', 'status', 'from_date', 'to_date']))
                                                            Try adjusting your search criteria or filters.
                                                        @else
                                                            There are currently no resolved or closed issues recorded.
                                                        @endif
                                                    </p>
                                                    <a href="/admin/logged-issues" class="btn btn-sm btn-primary">
                                                        <i data-feather="arrow-left" class="me-1" style="width: 14px; height: 14px;"></i> View Open Logged Issues
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($issues->hasPages())
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="text-muted fs-13">
                                        Showing {{ $issues->firstItem() }} to {{ $issues->lastItem() }} of {{ $issues->total() }} resolved issues
                                    </div>
                                    <div>
                                        {{ $issues->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
