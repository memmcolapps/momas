@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Meter Transaction Report</h4>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                            <i class="fas fa-money-bill-wave text-primary"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total Amount</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">NGN {{number_format($total_amount ?? 0, 2)}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                            <i class="fas fa-bolt text-success"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total Units</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{number_format($total_units ?? 0, 2)}} kWh</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-warning-subtle rounded-circle p-2 me-2 border border-dashed border-warning">
                                            <i class="fas fa-percentage text-warning"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total VAT</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">NGN {{number_format($total_vat ?? 0, 2)}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title text-black mb-0">All Meter Transactions</h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="cursor-pointer">Meter No</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Customer</th>
                                            <th scope="col" class="cursor-pointer">VAT</th>
                                            <th scope="col" class="cursor-pointer">Units (kWh)</th>
                                            <th scope="col" class="cursor-pointer">Fixed Charges</th>
                                            <th scope="col" class="cursor-pointer">Transaction Date</th>
                                            <th scope="col" class="cursor-pointer">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($meter_transactions as $transaction)
                                            <tr>
                                                <td>{{$transaction->meterNo}}</td>
                                                <td>NGN {{number_format($transaction->amount, 2)}}</td>
                                                <td>
                                                    <div>{{$transaction->user->first_name ?? 'N/A'}} {{$transaction->user->last_name ?? ''}}</div>
                                                    <small class="text-muted">{{$transaction->user->email ?? ''}}</small>
                                                </td>
                                                <td>NGN {{number_format($transaction->vatAmount ?? 0, 2)}}</td>
                                                <td>{{number_format($transaction->unitkwh ?? 0, 2)}}</td>
                                                <td>NGN {{number_format($transaction->fee ?? 0, 2)}}</td>
                                                <td>{{$transaction->created_at->format('d/m/Y H:i')}}</td>
                                                <td>
                                                    @if($transaction->status == 2)
                                                        <span class="badge text-bg-success">Completed</span>
                                                    @elseif($transaction->status == 1)
                                                        <span class="badge text-bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge text-bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        {{ $meter_transactions->links() }}
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Meter Transaction Report | {{Auth::user()->estate_name}}</h4>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary-subtle rounded-circle p-2 me-2 border border-dashed border-primary">
                                            <i class="fas fa-money-bill-wave text-primary"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total Amount</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">NGN {{number_format($total_amount ?? 0, 2)}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-success-subtle rounded-circle p-2 me-2 border border-dashed border-success">
                                            <i class="fas fa-bolt text-success"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total Units</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{number_format($total_units ?? 0, 2)}} kWh</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-warning-subtle rounded-circle p-2 me-2 border border-dashed border-warning">
                                            <i class="fas fa-percentage text-warning"></i>
                                        </div>
                                        <p class="mb-0 text-dark fs-15">Total VAT</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">NGN {{number_format($total_vat ?? 0, 2)}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="col-md-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="element-box">
                                    <h6 class="element-header">Filter Transactions</h6>
                                    <form action="search-meter-transactions" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-4">
                                                <label>Date From</label>
                                                <input type="date" class="form-control" name="from">
                                            </div>
                                            <div class="col-4">
                                                <label>Date To</label>
                                                <input type="date" class="form-control" name="to">
                                            </div>
                                            <div class="col-4">
                                                <label>Meter Number</label>
                                                <input type="text" class="form-control" name="meter_no" placeholder="Enter meter number">
                                            </div>
                                        </div>
                                        <div class="row my-3">
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title text-black mb-0">Meter Transactions</h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="cursor-pointer">Meter No</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Customer</th>
                                            <th scope="col" class="cursor-pointer">VAT Applied</th>
                                            <th scope="col" class="cursor-pointer">Units (kWh)</th>
                                            <th scope="col" class="cursor-pointer">Fixed Charges</th>
                                            <th scope="col" class="cursor-pointer">Transaction Date</th>
                                            <th scope="col" class="cursor-pointer">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($meter_transactions as $transaction)
                                            <tr>
                                                <td>{{$transaction->meterNo}}</td>
                                                <td>NGN {{number_format($transaction->amount, 2)}}</td>
                                                <td>
                                                    <div>{{$transaction->user->first_name ?? 'N/A'}} {{$transaction->user->last_name ?? ''}}</div>
                                                    <small class="text-muted">{{$transaction->user->email ?? ''}}</small>
                                                    @if($transaction->user->phone ?? false)
                                                        <br><small class="text-muted">{{$transaction->user->phone}}</small>
                                                    @endif
                                                </td>
                                                <td>NGN {{number_format($transaction->vatAmount ?? 0, 2)}}</td>
                                                <td>{{number_format($transaction->unitkwh ?? 0, 2)}}</td>
                                                <td>NGN {{number_format($transaction->fee ?? 0, 2)}}</td>
                                                <td>{{$transaction->created_at->format('d/m/Y H:i')}}</td>
                                                <td>
                                                    @if($transaction->status == 2)
                                                        <span class="badge text-bg-success">Completed</span>
                                                    @elseif($transaction->status == 1)
                                                        <span class="badge text-bg-warning">Pending</span>
                                                    @else
                                                        <span class="badge text-bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        {{ $meter_transactions->links() }}
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
    @endif

@endsection