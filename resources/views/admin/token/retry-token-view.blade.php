@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Retry Token</h4>
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

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">

                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Pending Transactions</h5>
                                </div><!-- end card header -->

                                <div class="card-body">

                                    <form action="retry-token-transactions" method="GET">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <input type="text" name="search" placeholder="Search by meter no, trx id or customer name" class="form-control" value="{{ request('search') }}">
                                            </div>
                                            <div class="col-2">
                                                <button type="submit" class="btn btn-primary w-100">Search</button>
                                            </div>
                                        </div>
                                    </form>

                                    <table id=""
                                           class="table table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="cursor-pointer">Customer Name</th>
                                            <th scope="col" class="cursor-pointer">Meter Number</th>
                                            <th scope="col" class="cursor-pointer">Estate</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Service</th>
                                            <th scope="col" class="cursor-pointer desc">Status</th>
                                            <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                            <th scope="col" class="cursor-pointer desc">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($transactions as $data)

                                            @php
                                                $payload = json_decode($data->action_payload, true) ?? [];
                                                $meterNo = $data->creditToken
                                                    ? ($data->creditToken->receiver_meterNo ?: $data->creditToken->meterNo)
                                                    : ($payload['receiver_meterNo'] ?? $data->user?->meterNo);
                                            @endphp

                                            <tr>
                                                <td>
                                                    <a href="view-user?id={{$data->user?->id ?? null}}">{{$data->user?->last_name ?? "name"}} {{$data->user?->first_name ?? "name"}}</a>
                                                </td>
                                                <td>{{$meterNo}}</td>
                                                <td>{{$data->estate->title ?? "name"}}</td>
                                                <td>{{number_format($data->amount, 2)}}</td>
                                                <td>{{$data->service_type ?? $data->service}}</td>
                                                <td>
                                                    @if($data->status == 0)
                                                        <span class="badge text-bg-warning">Pending</span>
                                                    @elseif($data->status == 3)
                                                        <span class="badge text-bg-danger">Service Pending</span>
                                                    @else
                                                        <span class="badge text-bg-secondary">{{$data->status}}</span>
                                                    @endif
                                                </td>
                                                <td>{{$data->created_at}}</td>
                                                <td>
                                                    <a href="retry-generate-token-web?trx_id={{$data->trx_id}}"
                                                       onclick="return confirmRetry();"
                                                       class="btn btn-secondary">Retry</a>
                                                    <script>
                                                        function confirmRetry() {
                                                            return confirm('Are you sure you want to retry this transaction?');
                                                        }
                                                    </script>
                                                </td>
                                            </tr>

                                        @endforeach

                                        </tbody><!-- end tbody -->

                                        <tfoot>

                                        </tfoot>
                                    </table><!-- end table -->

                                    <div class="d-flex justify-content-end mt-3">
                                        {{ $transactions->links() }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div> <!-- container-fluid -->

        </div>

    @else
        <div class="content">
            <div class="container-fluid">
                <div class="alert alert-danger">Unauthorized</div>
            </div>
        </div>
    @endif

@endsection
