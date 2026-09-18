@extends('layouts.main')

@section('content')
<div class="content">
    <div class="container-fluid">

        <!-- Page Header Title -->
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column justify-content-between">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Logged Issues & Resident Complaints</h4>
                <p class="text-muted fs-13 mb-0">Manage, track, and resolve maintenance and vending issues logged across your estate.</p>
            </div>
            <div class="mt-3 mt-sm-0">
                <button type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#newIssueModal">
                    <i data-feather="plus-circle" class="me-1"></i> Log New Issue
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Widget Metrics Summary Row -->
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                    <i data-feather="alert-circle" class="text-primary"></i>
                                </div>
                                <p class="mb-0 text-dark fs-15">Total Logged</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">128</h3>
                                <span class="badge bg-primary-subtle text-primary">All Time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning-subtle rounded-circle p-2 me-2 border border-dashed border-warning">
                                    <i data-feather="clock" class="text-warning"></i>
                                </div>
                                <p class="mb-0 text-dark fs-15">Open / Pending</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">14</h3>
                                <span class="badge bg-warning-subtle text-warning">Requires Attention</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info-subtle rounded-circle p-2 me-2 border border-dashed border-info">
                                    <i data-feather="loader" class="text-info"></i>
                                </div>
                                <p class="mb-0 text-dark fs-15">In Progress</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">8</h3>
                                <span class="badge bg-info-subtle text-info">Technician Assigned</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-first">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                    <i data-feather="check-circle" class="text-success"></i>
                                </div>
                                <p class="mb-0 text-dark fs-15">Resolved</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">106</h3>
                                <span class="badge bg-success-subtle text-success">98.2% Resolution</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="row">
            <div class="col-md-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3 text-dark fw-semibold"><i data-feather="filter" class="icon-sm me-1"></i> Filter Logged Issues</h6>
                        <form action="" method="GET">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="search" placeholder="Search by Ticket ID, Resident, Meter, or Address..."
                                        class="form-control" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        <option value="meter_fault" {{ request('category') == 'meter_fault' ? 'selected' : '' }}>Meter Fault / Error</option>
                                        <option value="vending" {{ request('category') == 'vending' ? 'selected' : '' }}>Vending & Token Failure</option>
                                        <option value="power" {{ request('category') == 'power' ? 'selected' : '' }}>Power & Substation Outage</option>
                                        <option value="utility" {{ request('category') == 'utility' ? 'selected' : '' }}>Utility & Service Charge</option>
                                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>General Estate Complaint</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Open / Pending</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>In Progress</option>
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Resolved</option>
                                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Escalated</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logged Issues Data Table Card -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card overflow-hidden">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title text-black mb-0">Estate Support Ticket Queue</h5>
                        <span class="text-muted fs-13">Showing real-time issue tickets</span>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Resident Name</th>
                                        <th>Address & Meter</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Logged Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample Row 1 -->
                                    <tr>
                                        <td class="fw-bold"><a href="{{ url('admin/logged-issues/detail?id=101') }}" class="text-primary">#TKN-89102</a></td>
                                        <td>
                                            <div class="fw-semibold">Adejimi Tolulope</div>
                                            <small class="text-muted">+234 803 123 4567</small>
                                        </td>
                                        <td>
                                            <div>Flat 4B, Block 12, EVE Estate</div>
                                            <small class="text-muted">Meter: 04218849201</small>
                                        </td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">STS Vending Error</span></td>
                                        <td><span class="badge text-bg-danger">High</span></td>
                                        <td><span class="badge text-bg-warning">Open / Pending</span></td>
                                        <td>Nov 04, 2025 14:10</td>
                                        <td class="text-center">
                                            <a href="{{ url('admin/logged-issues/detail?id=101') }}" class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="icon-xs me-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Sample Row 2 -->
                                    <tr>
                                        <td class="fw-bold"><a href="{{ url('admin/logged-issues/detail?id=102') }}" class="text-primary">#TKN-89098</a></td>
                                        <td>
                                            <div class="fw-semibold">Babatunde Emmanuel</div>
                                            <small class="text-muted">+234 802 987 6543</small>
                                        </td>
                                        <td>
                                            <div>House 15, Palm Avenue, EVE Estate</div>
                                            <small class="text-muted">Meter: 04218849312</small>
                                        </td>
                                        <td><span class="badge bg-danger-subtle text-danger">Tamper Code Needed</span></td>
                                        <td><span class="badge text-bg-warning">Medium</span></td>
                                        <td><span class="badge text-bg-info">In Progress</span></td>
                                        <td>Nov 04, 2025 11:45</td>
                                        <td class="text-center">
                                            <a href="{{ url('admin/logged-issues/detail?id=102') }}" class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="icon-xs me-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Sample Row 3 -->
                                    <tr>
                                        <td class="fw-bold"><a href="{{ url('admin/logged-issues/detail?id=103') }}" class="text-primary">#TKN-89085</a></td>
                                        <td>
                                            <div class="fw-semibold">Grace Okafor</div>
                                            <small class="text-muted">+234 814 555 0192</small>
                                        </td>
                                        <td>
                                            <div>Flat 2A, Block 8, EVE Estate</div>
                                            <small class="text-muted">Meter: 04218849105</small>
                                        </td>
                                        <td><span class="badge bg-primary-subtle text-primary">Service Charge Enquiry</span></td>
                                        <td><span class="badge text-bg-secondary">Low</span></td>
                                        <td><span class="badge text-bg-success">Resolved</span></td>
                                        <td>Nov 03, 2025 16:20</td>
                                        <td class="text-center">
                                            <a href="{{ url('admin/logged-issues/detail?id=103') }}" class="btn btn-sm btn-outline-secondary">
                                                <i data-feather="eye" class="icon-xs me-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Sample Row 4 -->
                                    <tr>
                                        <td class="fw-bold"><a href="{{ url('admin/logged-issues/detail?id=104') }}" class="text-primary">#TKN-89071</a></td>
                                        <td>
                                            <div class="fw-semibold">Chidi Nnamdi</div>
                                            <small class="text-muted">+234 809 333 4411</small>
                                        </td>
                                        <td>
                                            <div>Plot 7, Feeder Road, EVE Estate</div>
                                            <small class="text-muted">Meter: 04218849900</small>
                                        </td>
                                        <td><span class="badge bg-warning-subtle text-warning">Transformer Outage</span></td>
                                        <td><span class="badge text-bg-danger">High</span></td>
                                        <td><span class="badge text-bg-danger">Escalated</span></td>
                                        <td>Nov 02, 2025 09:15</td>
                                        <td class="text-center">
                                            <a href="{{ url('admin/logged-issues/detail?id=104') }}" class="btn btn-sm btn-primary">
                                                <i data-feather="eye" class="icon-xs me-1"></i> View Details
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Log New Resident Issue -->
<div class="modal fade" id="newIssueModal" tabindex="-1" aria-labelledby="newIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-16 fw-semibold" id="newIssueModalLabel">Log New Support Issue / Resident Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Resident / Customer Name</label>
                            <input type="text" class="form-control" name="resident_name" placeholder="e.g. Adejimi Tolulope" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" placeholder="e.g. +234 803 123 4567" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meter Number</label>
                            <input type="text" class="form-control" name="meter_number" placeholder="e.g. 04218849201" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Flat / Property Address</label>
                            <input type="text" class="form-control" name="address" placeholder="e.g. Flat 4B, Block 12" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Issue Category</label>
                            <select class="form-select" name="category" required>
                                <option value="">Select Category...</option>
                                <option value="vending">STS Vending & Token Failure</option>
                                <option value="meter_fault">Meter Fault / Error Code</option>
                                <option value="power">Transformer / Power Outage</option>
                                <option value="utility">Utility & Service Charge Payment</option>
                                <option value="other">General Complaint</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority Level</label>
                            <select class="form-select" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High / Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Detailed Description of Problem</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Describe the issue reported by the resident in detail..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" onclick="alert('New issue logged successfully!');">Save & Log Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
