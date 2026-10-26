@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('alerts.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Back
                        </a>
                        <h4 class="fs-18 fw-semibold m-0">{{ $alert->title }}</h4>
                        <span class="badge text-bg-{{ $alert->status_badge }}">{{ $alert->status_label }}</span>
                        <span class="badge text-bg-{{ $alert->priority_badge }} text-uppercase">{{ $alert->priority }}</span>
                        @if($alert->is_urgent)
                            <span class="badge text-bg-danger text-uppercase">Urgent</span>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0 d-flex gap-2 mt-2 mt-sm-0">
                    <a href="{{ route('alerts.stats') }}" class="btn btn-outline-primary">
                        <i data-feather="bar-chart-2" class="me-1"></i> Statistics
                    </a>
                    <a href="{{ route('alerts.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> New Alert
                    </a>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title text-black mb-0">Alert Details</h5>
                        </div>
                        <div class="card-body">
                            @if($alert->image_url)
                                <div class="mb-3">
                                    <img src="{{ $alert->image_url }}" alt="{{ $alert->title }}" class="img-fluid rounded border" style="max-height: 320px;">
                                </div>
                            @endif

                            <h4 class="text-dark mb-3">{{ $alert->title }}</h4>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Displays At</label>
                                    <p class="fs-14 text-dark mb-0">
                                        {{ $alert->displays_at ? $alert->displays_at->format('M d, Y H:i') : 'Immediate' }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Expires At</label>
                                    <p class="fs-14 text-dark mb-0">
                                        {{ $alert->expires_at ? $alert->expires_at->format('M d, Y H:i') : 'Never' }}
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold">Created At</label>
                                    <p class="fs-14 text-dark mb-0">{{ $alert->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title text-black mb-0">Alert Users</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $recipients->total() }} recipient(s)</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Acknowledged At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recipients as $recipient)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    {{ $recipient->user ? trim($recipient->user->first_name.' '.$recipient->user->last_name) : 'Unknown user' }}
                                                </div>
                                                <span class="text-muted fs-12">User ID: {{ $recipient->user_id }}</span>
                                            </td>
                                            <td class="fs-13">{{ $recipient->user->email ?? '—' }}</td>
                                            <td class="fs-13">{{ $recipient->user->phone ?? '—' }}</td>
                                            <td>
                                                @if($recipient->acknowledged_at)
                                                    <span class="badge text-bg-success">Acknowledged</span>
                                                @else
                                                    <span class="badge text-bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="fs-13 text-muted">
                                                {{ $recipient->acknowledged_at ? $recipient->acknowledged_at->format('M d, Y H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No alert users found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                {{ $recipients->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title text-black mb-0">Recipient Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted fs-13">Total Recipients</span>
                                <span class="fw-semibold text-dark">{{ number_format($totalRecipients) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted fs-13">Acknowledged</span>
                                <span class="fw-semibold text-success">{{ number_format($acknowledged) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted fs-13">Pending</span>
                                <span class="fw-semibold text-warning">{{ number_format($unacknowledged) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted fs-13">Acknowledgement Rate</span>
                                <span class="fw-semibold text-info">{{ $acknowledgedPercentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-black mb-0">Target Group</h5>
                        </div>
                        <div class="card-body">
                            @if(!$group)
                                <p class="text-muted mb-0">No group was attached to this alert.</p>
                            @elseif($group->all_users)
                                <span class="badge text-bg-primary">All Users</span>
                            @else
                                <div class="mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold d-block mb-1">Estates</label>
                                    @forelse($estateNames as $estateName)
                                        <span class="badge text-bg-light border text-dark me-1 mb-1">{{ $estateName }}</span>
                                    @empty
                                        <span class="text-muted fs-13">None selected</span>
                                    @endforelse
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted fs-12 text-uppercase fw-semibold d-block mb-1">Customers</label>
                                    @forelse($selectedCustomers as $customer)
                                        <span class="badge text-bg-light border text-dark me-1 mb-1">
                                            {{ trim($customer->first_name.' '.$customer->last_name) }}
                                        </span>
                                    @empty
                                        <span class="text-muted fs-13">None selected</span>
                                    @endforelse
                                </div>

                                <div>
                                    <label class="text-muted fs-12 text-uppercase fw-semibold d-block mb-1">Meter Numbers</label>
                                    @forelse(($group->meter_numbers ?? []) as $meterNumber)
                                        <span class="badge text-bg-light border text-dark me-1 mb-1">{{ $meterNumber }}</span>
                                    @empty
                                        <span class="text-muted fs-13">None selected</span>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
