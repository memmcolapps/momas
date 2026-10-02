@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <a href="/admin/logged-issues" class="btn btn-sm btn-outline-secondary">
                            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back to Issues
                        </a>
                        <h4 class="fs-18 fw-semibold m-0">Issue Ticket: {{ $issue->ticket_id }}</h4>
                        <span class="badge text-bg-{{ $issue->status_badge }} fs-13">{{ $issue->status_label }}</span>
                        <span class="badge text-bg-{{ $issue->priority_badge }} text-uppercase fs-12">{{ $issue->priority }} Priority</span>
                    </div>
                </div>
                <div class="text-end mt-2 mt-sm-0">
                    <a href="/admin/delete-issue?id={{ $issue->id }}"
                       onclick="return confirm('Are you sure you want to delete this issue ticket? This cannot be undone.');"
                       class="btn btn-sm btn-outline-danger">
                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i> Delete Ticket
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

            <div class="row">
                <!-- Left Column: Issue Details & Updates -->
                <div class="col-lg-8">
                    <!-- Issue Information Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-black mb-0">Issue Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted fs-12 text-uppercase fw-semibold">Subject / Title</label>
                                <h5 class="text-dark mt-1">{{ $issue->title }}</h5>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Category</label>
                                    <p class="fs-15 mb-0"><span class="badge bg-secondary-subtle text-dark fs-13">{{ $issue->category }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Date Logged</label>
                                    <p class="fs-15 text-dark mb-0">{{ $issue->created_at->format('l, F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="mb-4">
                                <label class="text-muted fs-12 text-uppercase fw-semibold">Description</label>
                                <div class="p-3 bg-light rounded border text-dark fs-14" style="white-space: pre-wrap; line-height: 1.6;">
                                    {{ $issue->description }}
                                </div>
                            </div>

                            <!-- Attachment -->
                            @if($issue->attachment)
                                <div class="mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Attachment</label>
                                    <div class="mt-2">
                                        @php
                                            $ext = strtolower(pathinfo($issue->attachment, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp

                                        @if($isImage)
                                            <div class="border rounded p-2 d-inline-block bg-white shadow-sm mb-2">
                                                <a href="/{{ $issue->attachment }}" target="_blank">
                                                    <img src="/{{ $issue->attachment }}" alt="Attachment" class="img-fluid rounded" style="max-height: 250px;">
                                                </a>
                                            </div>
                                            <div>
                                                <a href="/{{ $issue->attachment }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i data-feather="external-link" style="width: 14px; height: 14px;"></i> View Full Image
                                                </a>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center p-3 border rounded bg-white">
                                                <i data-feather="file" class="text-primary me-2" style="width: 24px; height: 24px;"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold text-dark">{{ basename($issue->attachment) }}</div>
                                                    <span class="fs-12 text-muted">{{ strtoupper($ext) }} Document</span>
                                                </div>
                                                <a href="/{{ $issue->attachment }}" download class="btn btn-sm btn-primary">
                                                    <i data-feather="download" style="width: 14px; height: 14px;"></i> Download
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Resolution & Action Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-black mb-0">Status & Resolution Management</h5>
                            <span class="badge text-bg-{{ $issue->status_badge }}">{{ $issue->status_label }}</span>
                        </div>
                        <div class="card-body">
                            <form action="/admin/update-issue-status" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $issue->id }}">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Update Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="0" {{ $issue->status === 0 ? 'selected' : '' }}>Open / Pending (Awaiting Action)</option>
                                            <option value="1" {{ $issue->status === 1 ? 'selected' : '' }}>In Progress (Under Investigation)</option>
                                            <option value="2" {{ $issue->status === 2 ? 'selected' : '' }}>Resolved (Issue Fixed)</option>
                                            <option value="3" {{ $issue->status === 3 ? 'selected' : '' }}>Closed (Ticket Terminated)</option>
                                        </select>
                                    </div>

                                    @if($issue->resolved_at)
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-muted">Resolution Timestamp</label>
                                            <p class="fs-14 text-dark mb-0 pt-2">
                                                <i data-feather="check-circle" class="text-success me-1" style="width: 14px; height: 14px;"></i>
                                                {{ $issue->resolved_at->format('M d, Y H:i') }}
                                                @if($issue->resolver)
                                                    by {{ $issue->resolver->first_name }} {{ $issue->resolver->last_name }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Admin Resolution Notes & Feedback</label>
                                    <textarea name="resolution_notes" rows="4" class="form-control"
                                              placeholder="Provide details on how the issue was investigated or resolved, actions taken, or instructions for the resident...">{{ $issue->resolution_notes }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i data-feather="save" class="me-1"></i> Save Status & Resolution
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Reporter / Customer Profile -->
                <div class="col-lg-4">
                    <!-- Resident Details Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-black mb-0">Resident / Reporter Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-md bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-weight: bold; font-size: 18px;">
                                    {{ strtoupper(substr($issue->customer_name, 0, 2)) }}
                                </div>
                                <div>
                                    <h5 class="fs-15 mb-1 text-dark">{{ $issue->customer_name }}</h5>
                                    @if($issue->user)
                                        <span class="badge bg-success-subtle text-success fs-11">Registered Resident</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-muted fs-11">Guest / Unlinked</span>
                                    @endif
                                </div>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i data-feather="phone" style="width: 14px; height: 14px;" class="me-1"></i> Phone:</span>
                                    <span class="fw-medium text-dark">
                                        @if($issue->customer_phone)
                                            <a href="tel:{{ $issue->customer_phone }}" class="text-primary">{{ $issue->customer_phone }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i data-feather="mail" style="width: 14px; height: 14px;" class="me-1"></i> Email:</span>
                                    <span class="fw-medium text-dark">
                                        @if($issue->customer_email)
                                            <a href="mailto:{{ $issue->customer_email }}" class="text-primary">{{ $issue->customer_email }}</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i data-feather="home" style="width: 14px; height: 14px;" class="me-1"></i> Unit / Flat:</span>
                                    <span class="fw-medium text-dark">{{ $issue->house_no ?? '-' }}</span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i data-feather="cpu" style="width: 14px; height: 14px;" class="me-1"></i> Meter Number:</span>
                                    <span class="fw-medium text-dark">
                                        @if($issue->meter_no)
                                            <span class="badge text-bg-light border text-dark">{{ $issue->meter_no }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i data-feather="map-pin" style="width: 14px; height: 14px;" class="me-1"></i> Estate:</span>
                                    <span class="fw-medium text-dark">{{ $issue->estate->title ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Ticket Meta Card -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title text-black mb-0">Ticket Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted fs-13">Ticket ID</span>
                                <span class="fw-semibold text-dark fs-13">{{ $issue->ticket_id }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span class="text-muted fs-13">Created At</span>
                                <span class="text-dark fs-13">{{ $issue->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted fs-13">Last Updated</span>
                                <span class="text-dark fs-13">{{ $issue->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
