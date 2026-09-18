@extends('layouts.main')

@section('content')
<div class="content">
    <div class="container-fluid">

        <!-- Top Header & Back Navigation -->
        <div class="py-3 d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('admin/logged-issues') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i data-feather="arrow-left" class="icon-xs me-1"></i> Back to Logged Issues
                </a>
                <h4 class="fs-18 fw-semibold m-0">Issue Ticket Details: #TKN-89102</h4>
            </div>
            <div>
                <span class="badge text-bg-warning fs-13 px-3 py-2">Status: Open / Pending</span>
                <span class="badge text-bg-danger fs-13 px-3 py-2 me-2">Priority: High</span>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Ticket Info & Activity Timeline -->
            <div class="col-lg-8">
                
                <!-- Resident & Meter Info Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light-subtle d-flex justify-content-between align-items-center">
                        <h5 class="card-title fs-15 m-0 text-dark"><i data-feather="user" class="icon-sm me-1 text-primary"></i> Resident Information</h5>
                        <small class="text-muted">Logged: Nov 04, 2025 14:10</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block fs-13">Resident Full Name</span>
                                <strong class="fs-15 text-dark">Adejimi Tolulope Adewale</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block fs-13">Phone Number</span>
                                <strong class="fs-15 text-dark">+234 803 123 4567</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block fs-13">Meter Serial Number</span>
                                <span class="badge bg-primary-subtle text-primary fs-13 font-monospace">04218849201</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block fs-13">Estate Property Address</span>
                                <strong class="fs-14 text-dark">Flat 4B, Block 12, EVE Estate, Ikeja</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issue Description Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light-subtle">
                        <h5 class="card-title fs-15 m-0 text-dark"><i data-feather="file-text" class="icon-sm me-1 text-warning"></i> Issue Summary & Reported Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-secondary-subtle text-secondary me-2">Category: STS Vending Error</span>
                            <span class="badge bg-info-subtle text-info">Channel: Mobile App Vending</span>
                        </div>
                        <h6 class="fw-bold text-dark">Problem Reported by Resident:</h6>
                        <p class="text-muted fs-14 bg-light p-3 rounded">
                            "I purchased electricity units worth ₦5,000 via the mobile app today at 14:05. The payment was debited successfully from my bank account, but the 20-digit STS token code failed to generate and displayed a gateway timeout error. Kindly generate my token code or credit my meter."
                        </p>

                        <div class="mt-3">
                            <h6 class="fw-semibold fs-13 text-dark">Attached Transaction Receipt / Photo:</h6>
                            <div class="p-2 border rounded d-inline-block bg-white">
                                <i data-feather="image" class="text-primary me-1"></i>
                                <span class="fs-13 text-dark">payment_receipt_24063372JM.png</span>
                                <a href="#" class="btn btn-sm btn-link text-primary ms-2" onclick="alert('Viewing attachment receipt...');">View File</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Action Log & Timeline -->
                <div class="card">
                    <div class="card-header bg-light-subtle">
                        <h5 class="card-title fs-15 m-0 text-dark"><i data-feather="activity" class="icon-sm me-1 text-info"></i> Ticket Resolution Activity Timeline</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex mb-3 pb-3 border-bottom">
                                <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center;">
                                    <i data-feather="plus" class="icon-xs"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-14">Issue Logged by Resident</div>
                                    <small class="text-muted">Nov 04, 2025 at 14:10 · Via Mobile App Support</small>
                                </div>
                            </li>
                            <li class="d-flex mb-3 pb-3 border-bottom">
                                <div class="bg-warning-subtle text-warning rounded-circle p-2 me-3" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center;">
                                    <i data-feather="user-check" class="icon-xs"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-14">Assigned to Estate Support Admin</div>
                                    <small class="text-muted">Nov 04, 2025 at 14:15 · Auto-routed to Substation Desk</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Right Column: Estate Admin Action & Resolution Controls -->
            <div class="col-lg-4">

                <!-- Admin Action Card -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title fs-15 m-0 text-white"><i data-feather="settings" class="icon-sm me-1"></i> Ticket Resolution Controls</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" onsubmit="event.preventDefault(); alert('Issue status updated successfully!');">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Update Ticket Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="0" selected>Open / Pending</option>
                                    <option value="1">In Progress (Investigating)</option>
                                    <option value="2">Resolved & Closed</option>
                                    <option value="3">Escalate to Senior Engineer</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign Technician / Staff</label>
                                <select class="form-select" name="technician">
                                    <option value="">Unassigned</option>
                                    <option value="1" selected>Engr. Samuel (STS Token Desk)</option>
                                    <option value="2">Technician Michael (Feeder Line)</option>
                                    <option value="3">Support Lead Grace</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Admin Response / Resolution Note</label>
                                <textarea class="form-control" name="response" rows="4" placeholder="Enter response to resident or internal technician note..." required></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success text-white fw-bold">
                                    <i data-feather="check-circle" class="icon-xs me-1"></i> Update Ticket & Send Response
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="confirm('Are you sure you want to escalate this issue to Senior Technical Team?');">
                                    <i data-feather="alert-triangle" class="icon-xs me-1"></i> Escalate Issue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- STS Quick Action Tools -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title fs-14 m-0 text-dark"><i data-feather="zap" class="icon-sm me-1 text-success"></i> STS Quick Token Tools</h5>
                    </div>
                    <div class="card-body">
                        <p class="fs-13 text-muted mb-3">Instant STS token generation tools for resolving resident vending issues:</p>
                        <div class="d-grid gap-2">
                            <a href="{{ url('admin/credit-token') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="repeat" class="icon-xs me-1"></i> Regenerate Credit Token
                            </a>
                            <a href="{{ url('admin/tamper-token') }}" class="btn btn-sm btn-outline-warning">
                                <i data-feather="shield" class="icon-xs me-1"></i> Generate Clear Tamper Code
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
