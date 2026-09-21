@extends('layouts.main')
@section('content')





    @if(Auth::user()->role == 0)
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
                    </div>
                </div>

                <!-- start row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Users</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$users}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Meter</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$meter}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Estate</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$estate}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Revenue</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">NGN{{number_format($total_in, 2)}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Estate Token</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$token}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Meter Token</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$meter_token}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->

                <!-- Estate transactions chart -->
                <div class="row mb-4">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title text-black mb-0">Estates by Transaction Count</h5>
                                </div>
                            </div>

                            <div class="card-body">
                                <form action="{{ url('admin/admin-dashboard') }}" method="GET" id="estate-chart-filter">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="fs-13">Date From</label>
                                            <input type="date" class="form-control" name="from" value="{{ request('from') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-13">Date To</label>
                                            <input type="date" class="form-control" name="to" value="{{ request('to') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-13">Transaction Status</label>
                                            <select class="form-select" name="status">
                                                <option value="">All Status</option>
                                                @foreach ([
                                                    0 => 'Pending',
                                                    2 => 'Completed',
                                                    1 => 'Payment Failed',
                                                    3 => 'Service Pending',
                                                    4 => 'Pending Review',
                                                ] as $value => $label)
                                                    <option value="{{ $value }}" @selected(request('status') !== null && request('status') == $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="fs-13">Transaction Type</label>
                                            <select class="form-select" name="transaction_type">
                                                <option value="">All Types</option>
                                                @foreach($service_types as $type)
                                                    <option value="{{ $type }}" @selected(request('transaction_type') == $type)>{{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $type))) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ url('admin/admin-dashboard') }}" class="btn btn-light btn-sm">Reset</a>
                                        </div>
                                    </div>
                                </form>

                                <div class="mt-3">
                                    <div id="estate-trx-chart" class="apex-charts"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end Estate transactions chart -->

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">

                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title text-black mb-0">Latest Transaction</h5>
                                </div>
                            </div>

                            <div class="card-body mt-0">
                                <div class="table-responsive table-card mt-0">
                                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                        <thead class="text-muted table-light">
                                        <tr>
                                            <th scope="col" class="cursor-pointer">Transaction ID</th>
                                            <th scope="col" class="cursor-pointer">User</th>
                                            <th scope="col" class="cursor-pointer">Trx Type</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Status</th>
                                            <th scope="col" class="cursor-pointer desc">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>


                                        @foreach($transaction as $data)

                                            <tr>
                                                <td>{{$data->trx_id}}</td>
                                                <td>{{$data->user->first_name ?? null}} {{$data->user->last_name ?? null}}</td>
                                                <td>
                                                    @if($data->service_type == "meter_token")
                                                        <span class="badge text-bg-dark">Meter Token</span>
                                                    @elseif($data->service_type == "airtime")
                                                        <span class="badge text-bg-dark">Airtime</span>
                                                    @elseif($data->service_type == "data")
                                                        <span class="badge text-bg-dark">Data</span>
                                                    @endif
                                                </td>
                                                <td>{{number_format($data->amount, 2)}}</td>
                                                <td>
                                                    @if($data->status == 2)
                                                        <span class="badge text-bg-primary">Completed</span>
                                                    @elseif($data->status == 3)
                                                        <span class="badge text-bg-dark">Reversed</span>
                                                    @elseif($data->status == 1)
                                                        <span class="badge text-bg-warning">Pending</span>
                                                    @elseif($data->status == 0)
                                                        <span class="badge text-bg-warning">initiated</span>
                                                    @elseif($data->status == 4)
                                                        <span class="badge text-bg-secondary">Payment Completed</span>
                                                    @else
                                                        <span class="badge text-bg-danger">Failed</span>
                                                    @endif

                                                </td>
                                                <td>{{$data->created_at}}</td>

                                            </tr>

                                        @endforeach


                                        </tbody><!-- end tbody -->

                                        <tfoot>

                                        {{ $transaction->links() }}


                                        </tfoot>
                                    </table><!-- end table -->
                                </div>
                            </div>

                        </div>
                    </div>


                </div>


            </div>
        </div>

        <script src="{{url('')}}/public/asset/ass/libs/apexcharts/apexcharts.min.js"></script>
        <script>
            (function () {
                var labels = @json($chart_labels);
                var values = @json($chart_values);

                var el = document.querySelector('#estate-trx-chart');
                if (!el || typeof ApexCharts === 'undefined') {
                    return;
                }

                if (values.length === 0) {
                    el.innerHTML = '<p class="text-muted text-center my-5">No transactions found for the selected filters</p>';
                    return;
                }

                var options = {
                    chart: {
                        type: 'bar',
                        height: Math.max(150, labels.length * 50),
                        toolbar: {show: false}
                    },
                    series: [{name: 'Transactions', data: values}],
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '20px',
                            borderRadius: 3
                        }
                    },
                    dataLabels: {enabled: false},
                    colors: ['#287F71'],
                    grid: {
                        strokeDashArray: 4,
                        xaxis: {lines: {show: true}},
                        yaxis: {lines: {show: false}}
                    },
                    xaxis: {
                        categories: labels,
                        labels: {style: {colors: '#001b2f', fontWeight: 500}},
                        title: {text: 'Transactions'}
                    },
                    yaxis: {
                        labels: {style: {colors: '#001b2f', fontWeight: 500}}
                    },
                    tooltip: {
                        theme: 'light',
                        y: {formatter: function (val) { return val + ' transaction' + (val === 1 ? '' : 's'); }}
                    }
                };

                var chart = new ApexCharts(el, options);
                chart.render();
            })();
        </script>
    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
                    </div>
                </div>

                <!-- start row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Users</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$users}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Total Customer</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$customers}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Estate Meter</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$meter}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="fs-16 mb-1">Estate Token</div>
                                </div>

                                <div class="d-flex align-items-baseline mb-2">
                                    <div class="fs-22 mb-0 me-2 fw-semibold text-black">{{$token}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div> <!-- content -->
    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
    @endif



@endsection
