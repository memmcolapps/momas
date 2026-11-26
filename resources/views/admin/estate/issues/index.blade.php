@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title / Breadcrumb -->
            <div class="py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fs-16 fw-semibold text-dark m-0">Logged Issue List</h5>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#logIssueModal" style="border-radius: 6px;">
                        <i data-feather="plus" style="width: 14px; height: 14px;" class="me-1"></i> Log New Issue
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                    {{ session()->get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- KPI Widget (Figma Image 1) -->
            <div class="row mb-3">
                <div class="col-auto">
                    <div class="card shadow-none mb-0" style="min-width: 260px; border-radius: 12px; border: 1px solid #E5E7EB; background: #ffffff;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="color: #6B7280; font-size: 13px; font-weight: 500;">Total Logged Issue</span>
                                <div style="width: 36px; height: 36px; border-radius: 50%; background-color: #E8F8EE; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="target" style="width: 18px; height: 18px; color: #1E7E34;"></i>
                                </div>
                            </div>
                            <h3 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0;">{{ $totalLoggedIssue ?? $issues->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issues Table Card (Figma Image 1) -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-none" style="border-radius: 12px; border: 1px solid #E5E7EB; background: #ffffff; overflow: hidden;">
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #F3F4F6;">
                            <h5 class="m-0" style="font-size: 16px; font-weight: 600; color: #111827;">Logged Issue List</h5>
                            @if(request('search'))
                                <a href="/admin/logged-issues" class="btn btn-sm btn-link text-muted p-0 fs-12">Clear Search</a>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                                    <thead>
                                    <tr style="background-color: #FAFAFA; border-bottom: 1px solid #F3F4F6;">
                                        <th style="padding: 14px 24px; font-size: 13px; font-weight: 600; color: #6B7280; border: none;">Ticket ID</th>
                                        <th style="padding: 14px 20px; font-size: 13px; font-weight: 600; color: #6B7280; border: none;">Customer Name</th>
                                        <th style="padding: 14px 20px; font-size: 13px; font-weight: 600; color: #6B7280; border: none;">Email</th>
                                        <th style="padding: 14px 20px; font-size: 13px; font-weight: 600; color: #6B7280; border: none;">Date</th>
                                        <th style="padding: 14px 20px; font-size: 13px; font-weight: 600; color: #6B7280; border: none;">Status</th>
                                        <th style="padding: 14px 24px; font-size: 13px; font-weight: 600; color: #6B7280; border: none; text-align: right;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($issues as $issue)
                                        <tr style="border-bottom: 1px solid #F3F4F6;">
                                            <td style="padding: 16px 24px; font-size: 14px; font-weight: 600; color: #15803D; border: none;">
                                                #{{ $issue->ticket_id_number }}
                                            </td>
                                            <td style="padding: 16px 20px; font-size: 14px; font-weight: 500; color: #1F2937; border: none;">
                                                {{ $issue->customer_name }}
                                            </td>
                                            <td style="padding: 16px 20px; font-size: 14px; color: #4B5563; border: none;">
                                                {{ $issue->customer_email ?: '-' }}
                                            </td>
                                            <td style="padding: 16px 20px; font-size: 14px; color: #4B5563; border: none;">
                                                {{ $issue->created_at ? $issue->created_at->format('Y-m-d H:i:s') : '-' }}
                                            </td>
                                            <td style="padding: 16px 20px; border: none;">
                                                @if($issue->status == 0 || $issue->status == 1)
                                                    <span class="badge" style="background-color: #FEF3E6; color: #D97706; font-size: 12px; font-weight: 500; padding: 5px 12px; border-radius: 9999px;">Pending</span>
                                                @elseif($issue->status == 2)
                                                    <span class="badge" style="background-color: #E6F8F6; color: #0D9488; font-size: 12px; font-weight: 500; padding: 5px 12px; border-radius: 9999px;">Resolved</span>
                                                @else
                                                    <span class="badge" style="background-color: #F3F4F6; color: #6B7280; font-size: 12px; font-weight: 500; padding: 5px 12px; border-radius: 9999px;">Closed</span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px 24px; border: none; text-align: right;">
                                                <a href="/admin/view-issue?id={{ $issue->id }}"
                                                   class="btn btn-sm"
                                                   style="background-color: #1E7E34; color: #FFFFFF; font-size: 13px; font-weight: 500; padding: 6px 16px; border-radius: 6px; border: none; text-decoration: none; display: inline-flex; align-items: center;">
                                                    View Issue
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5" style="border: none;">
                                                <div class="text-muted">
                                                    <i data-feather="inbox" style="width: 42px; height: 42px;" class="mb-2 text-muted"></i>
                                                    <p class="mb-0 fs-14">No logged issues found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($issues->hasPages())
                                <div class="px-4 py-3" style="border-top: 1px solid #F3F4F6;">
                                    {{ $issues->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Log Issue Modal (Kept for creating new issues if needed) -->
    <div class="modal fade" id="logIssueModal" tabindex="-1" aria-labelledby="logIssueModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid #F3F4F6; padding: 16px 24px;">
                    <h5 class="modal-title fw-semibold text-dark" id="logIssueModalLabel">Log New Resident Issue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/admin/store-issue" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Customer / Resident Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" placeholder="e.g. John Doe" required style="border-radius: 6px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Customer Email</label>
                                <input type="email" name="customer_email" class="form-control" placeholder="resident@email.com" style="border-radius: 6px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Customer Phone <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" class="form-control" placeholder="08012345678" required style="border-radius: 6px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Meter Number</label>
                                <input type="text" name="meter_no" class="form-control" placeholder="0142..." style="border-radius: 6px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required style="border-radius: 6px;">
                                    <option value="Power / Meter">Power / Meter</option>
                                    <option value="Token Generation">Token Generation</option>
                                    <option value="Billing / Tariff">Billing / Tariff</option>
                                    <option value="Water / Utility">Water / Utility</option>
                                    <option value="Security / Gate">Security / Gate</option>
                                    <option value="Facility Maintenance">Facility Maintenance</option>
                                    <option value="General" selected>General</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium fs-13">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required style="border-radius: 6px;">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium fs-13">Issue Subject / Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Brief summary of the issue" required style="border-radius: 6px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium fs-13">Issue Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Detailed explanation..." required style="border-radius: 6px;"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium fs-13">Attachment (Optional)</label>
                                <input type="file" name="attachment" class="form-control" accept="image/*,.pdf" style="border-radius: 6px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #F3F4F6; padding: 14px 24px;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 6px;">Cancel</button>
                        <button type="submit" class="btn btn-success" style="background-color: #1E7E34; border: none; border-radius: 6px; padding: 8px 20px;">Save Issue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
