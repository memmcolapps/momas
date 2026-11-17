@extends('layouts.main')
@section('content')

    <div class="content">

        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">General Audit Logs</h4>
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

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 512">
                                        <path fill="#963b68" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m32 32h-64c-17.6 0-33.5 7.1-45.1 18.6c40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64m-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32S208 82.1 208 144s50.1 112 112 112m76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2m-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4"/>
                                    </svg>
                                </div>
                                <p class="mb-0 text-dark fs-15">Total Audit Logs</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">{{ number_format($total) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title text-black mb-0">All Audit Logs</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Recorded Name</th>
                                        <th scope="col">Action</th>
                                        <th scope="col">Old Values</th>
                                        <th scope="col">New Values</th>
                                        <th scope="col">Date/Time</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($audit_logs as $data)
                                        @php
                                            $oldValues = json_decode($data->old_values ?? '', true);
                                            $newValues = json_decode($data->new_values ?? '', true);
                                        @endphp
                                        <tr>
                                            <td>{{ $data->id }}</td>
                                            <td>
                                                @if($data->user)
                                                    {{ $data->user->first_name }} {{ $data->user->last_name }}<br>
                                                    <small class="text-muted">{{ $data->user->email }}</small>
                                                @else
                                                    {{ $data->user_id ?? '-' }}
                                                @endif
                                            </td>
                                            <td>{{ $data->name ?? '-' }}</td>
                                            <td>{{ $data->action ?? '-' }}</td>
                                            <td>
                                                <pre class="mb-0 small text-wrap" style="max-width: 320px; white-space: pre-wrap; word-break: break-word;">{{ $oldValues !== null ? json_encode($oldValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($data->old_values ?? '-') }}</pre>
                                            </td>
                                            <td>
                                                <pre class="mb-0 small text-wrap" style="max-width: 320px; white-space: pre-wrap; word-break: break-word;">{{ $newValues !== null ? json_encode($newValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($data->new_values ?? '-') }}</pre>
                                            </td>
                                            <td>{{ $data->created_at ? $data->created_at->format('d M Y, h:i A') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No audit logs found.</td>
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

@endsection
