@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">All Alerts</h4>
                    <p class="text-muted fs-13 mb-0">View every alert and open one to inspect its recipients.</p>
                </div>
                <div class="flex-shrink-0 d-flex gap-2">
                    <a href="{{ route('alerts.stats') }}" class="btn btn-outline-primary">
                        <i data-feather="bar-chart-2" class="me-1"></i> Statistics
                    </a>
                    <a href="{{ route('alerts.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Create Alert
                    </a>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session()->get('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @include('admin.alerts._filters', ['action' => route('alerts.index')])

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-black mb-0">Alerts List</h5>
                    <span class="badge bg-primary-subtle text-primary">{{ $alerts->total() }} record(s)</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Displays At</th>
                                <th>Expires At</th>
                                <th>Recipients</th>
                                <th>Acknowledged</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($alerts as $alert)
                                @php($showUrl = route('alerts.show', ['id' => $alert->id]))
                                <tr style="cursor: pointer;" onclick="window.location='{{ $showUrl }}'">
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $alert->title }}</div>
                                        @if($alert->is_urgent)
                                            <span class="badge text-bg-danger text-uppercase fs-11">Urgent</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $alert->priority_badge }} text-uppercase fs-11">
                                            {{ $alert->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fs-12 text-muted">
                                            {{ $alert->displays_at ? $alert->displays_at->format('M d, Y H:i') : 'Immediate' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fs-12 text-muted">
                                            {{ $alert->expires_at ? $alert->expires_at->format('M d, Y H:i') : 'Never' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-light border text-dark">{{ number_format($alert->recipients_count) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-info-subtle text-info">{{ number_format($alert->acknowledged_count) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $alert->status_badge }}">{{ $alert->status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="fs-12 text-muted">{{ $alert->created_at->format('M d, Y H:i') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ $showUrl }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i data-feather="inbox" style="width: 40px; height: 40px;" class="mb-2"></i>
                                            <p class="mb-0">No alerts found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $alerts->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
