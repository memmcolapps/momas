@extends('layouts.main')
@section('content')

    <style>
        .audit-stat-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            flex: 0 0 42px;
        }

        .actor-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #eef2ff;
            color: #4457c2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex: 0 0 34px;
        }

        .request-agent {
            max-width: 220px;
        }

        .audit-table > :not(caption) > * > * {
            vertical-align: middle;
        }

        .audit-json {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: .5rem;
            padding: 1rem;
            font-size: .78rem;
            line-height: 1.5;
            max-height: 55vh;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
    </style>

    <div class="content">

        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">General Audit Logs</h4>
                    <p class="text-muted mb-0">Track every recorded action across users, estates, meters and services.</p>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            <div class="row g-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="audit-stat-icon bg-primary-subtle text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M2 4.5A.5.5 0 0 1 2.5 4h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0 3.5A.5.5 0 0 1 2.5 7h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1 fs-13">Total Logs</p>
                                <h4 class="mb-0">{{ number_format($total) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="audit-stat-icon bg-success-subtle text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1 fs-13">Today's Logs</p>
                                <h4 class="mb-0">{{ number_format($todayTotal) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="audit-stat-icon bg-warning-subtle text-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1 fs-13">Unique Actors</p>
                                <h4 class="mb-0">{{ number_format($uniqueActors) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="audit-stat-icon bg-info-subtle text-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-muted mb-1 fs-13">Unique Actions</p>
                                <h4 class="mb-0">{{ number_format($uniqueActions) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <form method="GET" action="{{ url('admin/general_audit') }}" class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Search</label>
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                   placeholder="User, action, entity, IP...">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Action</label>
                            <select name="action_identifier" class="form-select">
                                <option value="">All actions</option>
                                @foreach ($actionOptions as $action)
                                    <option value="{{ $action }}" @selected(request('action_identifier') === $action)>{{ $action }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Entity</label>
                            <select name="entity_type" class="form-select">
                                <option value="">All entities</option>
                                @foreach ($entityOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(request('entity_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Estate</label>
                            <select name="estate_id" class="form-select">
                                <option value="">All estates</option>
                                @foreach ($estates as $id => $title)
                                    <option value="{{ $id }}" @selected((string) request('estate_id') === (string) $id)>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Per page</label>
                            <select name="per_page" class="form-select">
                                @foreach ($perPageOptions as $option)
                                    <option value="{{ $option }}" @selected((int) $perPage === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-lg-8 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Apply filters</button>
                            <a href="{{ url('admin/general_audit') }}" class="btn btn-light border">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="card-title text-black mb-0">All Audit Logs</h5>
                                    <small class="text-muted">
                                        Showing {{ $audit_logs->firstItem() ?? 0 }}&ndash;{{ $audit_logs->lastItem() ?? 0 }}
                                        of {{ number_format($audit_logs->total()) }} logs
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover audit-table mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">When</th>
                                        <th scope="col">Actor</th>
                                        <th scope="col">Action</th>
                                        <th scope="col">Entity</th>
                                        <th scope="col">Estate</th>
                                        <th scope="col">Request</th>
                                        <th scope="col" class="text-end">Changes</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($audit_logs as $log)
                                        @php
                                            $actorName = $log->user
                                                ? trim(($log->user->first_name ?? '').' '.($log->user->last_name ?? ''))
                                                : null;
                                            $actorName = $actorName ?: ($log->user_name ?: '-');
                                            $actorEmail = $log->user->email ?? null;
                                            $hasChanges = ! empty($log->old_values) || ! empty($log->new_values);
                                        @endphp
                                        <tr>
                                            <td class="text-muted">#{{ $log->id }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $log->created_at?->format('d M Y') ?? '-' }}</div>
                                                <small class="text-muted">{{ $log->created_at?->format('h:i A') ?? '' }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="actor-avatar">{{ strtoupper(substr($actorName, 0, 2)) }}</span>
                                                    <div>
                                                        <div class="fw-semibold">{{ $actorName }}</div>
                                                        @if($actorEmail)
                                                            <small class="text-muted d-block">{{ $actorEmail }}</small>
                                                        @elseif($log->user_id)
                                                            <small class="text-muted d-block">ID: {{ $log->user_id }}</small>
                                                        @endif
                                                        @if($log->user_type)
                                                            <span class="badge bg-light text-dark border">{{ class_basename($log->user_type) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $log->action ?: ($log->description ?: '-') }}</div>
                                                @if($log->action_identifier)
                                                    <span class="badge bg-primary-subtle text-primary">{{ $log->action_identifier }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->entity_type)
                                                    <div class="fw-semibold">{{ class_basename($log->entity_type) }}</div>
                                                    <small class="text-muted">#{{ $log->entity_id ?? '-' }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->estate)
                                                    <div class="fw-semibold">{{ $log->estate->title }}</div>
                                                    <small class="text-muted">#{{ $log->estate_id }}</small>
                                                @elseif($log->estate_id)
                                                    <span class="text-muted">#{{ $log->estate_id }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div><small class="text-muted">IP:</small> {{ $log->ip_address ?? '-' }}</div>
                                                @if($log->user_agent)
                                                    <small class="text-muted d-inline-block text-truncate request-agent"
                                                           title="{{ $log->user_agent }}">{{ $log->user_agent }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($hasChanges)
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal" data-bs-target="#auditChangesModal"
                                                            data-title="{{ $log->action_identifier ?: ($log->action ?: 'Audit log') }}"
                                                            data-old="{{ json_encode($log->old_values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}"
                                                            data-new="{{ json_encode($log->new_values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}">
                                                        View changes
                                                    </button>
                                                @else
                                                    <span class="text-muted small">No changes</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                No audit logs found for the selected filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $audit_logs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="modal fade" id="auditChangesModal" tabindex="-1" aria-labelledby="auditChangesTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="auditChangesTitle">Audit changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase fs-12 mb-2">Old values</h6>
                            <pre class="audit-json mb-0" id="auditOldValues">-</pre>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase fs-12 mb-2">New values</h6>
                            <pre class="audit-json mb-0" id="auditNewValues">-</pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('auditChangesModal');

            if (!modal) {
                return;
            }

            var formatValue = function (value) {
                if (value === null || value === undefined || value === '' || value === 'null') {
                    return 'No values recorded.';
                }

                try {
                    var parsed = typeof value === 'string' ? JSON.parse(value) : value;

                    if (parsed === null || (typeof parsed === 'object' && Object.keys(parsed).length === 0)) {
                        return 'No values recorded.';
                    }

                    return JSON.stringify(parsed, null, 2);
                } catch (error) {
                    return String(value);
                }
            };

            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;

                if (!button) {
                    return;
                }

                document.getElementById('auditChangesTitle').textContent =
                    button.getAttribute('data-title') || 'Audit changes';
                document.getElementById('auditOldValues').textContent =
                    formatValue(button.getAttribute('data-old'));
                document.getElementById('auditNewValues').textContent =
                    formatValue(button.getAttribute('data-new'));
            });
        });
    </script>

@endsection
