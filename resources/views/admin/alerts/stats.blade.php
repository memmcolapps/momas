@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Alert Statistics</h4>
                    <p class="text-muted fs-13 mb-0">Track alert delivery, views, and expiry across the platform.</p>
                </div>
                <div class="flex-shrink-0 d-flex gap-2">
                    <a href="{{ route('alerts.index') }}" class="btn btn-outline-primary">
                        <i data-feather="list" class="me-1"></i> All Alerts
                    </a>
                    <a href="{{ route('alerts.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="me-1"></i> Create Alert
                    </a>
                </div>
            </div>

            @include('admin.alerts._filters', ['action' => route('alerts.stats')])

            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                    <i data-feather="bell" class="text-primary" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Total Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-black me-2">{{ number_format($stats['total']) }}</h3>
                                <span class="badge text-bg-light text-muted">All alerts</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                    <i data-feather="activity" class="text-success" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Active Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-success me-2">{{ number_format($stats['active']) }}</h3>
                                <span class="badge text-bg-success-subtle text-success">Currently displaying</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info-subtle rounded-circle p-2 me-2 border border-dashed border-info">
                                    <i data-feather="eye" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Viewed Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-info me-2">{{ number_format($stats['viewed']) }}</h3>
                                <span class="badge text-bg-info-subtle text-info">{{ number_format($stats['total_recipients']) }} recipients</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                    <i data-feather="clock" class="text-secondary" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Expired Alerts</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-secondary me-2">{{ number_format($stats['expired']) }}</h3>
                                <span class="badge text-bg-secondary-subtle text-secondary">Past expiry</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-info-subtle rounded-circle p-2 me-2 border border-dashed border-info">
                                    <i data-feather="bar-chart-2" class="text-info" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">View Percentage</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-info me-2">{{ $stats['view_percentage'] }}%</h3>
                                <span class="badge text-bg-info-subtle text-info">Viewed / recipients</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning-subtle rounded-circle p-2 me-2 border border-dashed border-warning">
                                    <i data-feather="pie-chart" class="text-warning" style="width: 20px; height: 20px;"></i>
                                </div>
                                <p class="mb-0 text-dark fs-14 fw-medium">Expire Percentage</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 fs-24 text-warning me-2">{{ $stats['expire_percentage'] }}%</h3>
                                <span class="badge text-bg-warning-subtle text-warning">Expired / all alerts</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
