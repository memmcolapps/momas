@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Postpaid Token</h4>
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
                                    <h5 class="card-title mb-0">Vending Information</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">


                                        <div class="d-flex justify-content-between my-4">
                                            <h5 class="card-title text-black mb-0">Generate Postpaid Token</h5>
                                        </div>

                                        <div class="col-xl-8 col-sm-12">
                                            <form action="postpaid-validate-meter" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Estate</label>
                                                                <select class="form-control" required name="estate_id"
                                                                        id="estate_id">
                                                                    <option value="">--Select Estate--</option>
                                                                    @foreach($estate as $data)
                                                                        <option
                                                                            value="{{$data->id}}">{{$data->title}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Enter Meter No</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="meterNo" id="meterNo" required>
                                                            </div>



                                                                <div class="col-xl-6 my-2 col-sm-12">
                                                                    <label class="my-2">Power Source</label>
                                                                    <select class="form-control" required
                                                                            name="tariff_id"

                                                                            id="tariff_id" disabled>
                                                                        <option value="">--Select Tariff--</option>
                                                                    </select>
                                                                </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Amount</label>
                                                                <input type="number" class="form-control mb-3" name="amount"
                                                                       required>
                                                            </div>


                                                        </div>



                                                        <script>
                                                            $(document).ready(function () {
                                                                $('#estate_id, #meterNo').on('change input', function () {
                                                                    var estate_id = $('#estate_id').val();
                                                                    var meterNo = $('#meterNo').val();

                                                                    if (estate_id && (meterNo.length === 11 || meterNo.length === 13)) {
                                                                        $.ajax({
                                                                            url: '/fetch-tariff',
                                                                            method: 'GET',
                                                                            data: {
                                                                                estate_id: estate_id,
                                                                                meterNo: meterNo
                                                                            },
                                                                            success: function (response) {
                                                                                if (response == 1) {
                                                                                    alert("Error: User is not attached to any estate.");
                                                                                    return;
                                                                                }
                                                                                if(response == 2){
                                                                                    alert("Error: Estate does not have any tariff");
                                                                                    return;
                                                                                }
                                                                                if(response == 3){
                                                                                    alert("Error: Tariff index not set for customer.");
                                                                                    return;
                                                                                }

                                                                                if (response && response.tariffs) {
                                                                                    console.log(response);
                                                                                    var tariffSelect = $('#tariff_id');
                                                                                    tariffSelect.empty();
                                                                                    tariffSelect.append('<option value="">--Select Tariff--</option>');

                                                                                    response.tariffs.forEach(function (tariff) {
                                                                                        tariffSelect.append('<option value="' + tariff.id + '">' + tariff.title + ' (' + tariff.type + ')' + '</option>');

                                                                                    });

                                                                                    tariffSelect.prop('disabled', false);
                                                                                } else {
                                                                                    $('#tariff_id').prop('disabled', true).empty();
                                                                                }
                                                                            },
                                                                            error: function () {
                                                                                $('#tariff_id').prop('disabled', true).empty();
                                                                                alert("Error fetching tariff data. Please try again.");
                                                                            }
                                                                        });
                                                                    } else {
                                                                        $('#tariff_id').prop('disabled', true).empty();
                                                                    }
                                                                });
                                                            });

                                                        </script>




                                                    @else
                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Enter Meter No</label>
                                                            <input type="number" disabled class="form-control mb-3"
                                                                   value="{{$meter->meterNo}}" name="meterNo" required>
                                                        </div>




                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" disabled value="{{$amount}}"
                                                                   class="form-control mb-3" name="amount" required>
                                                        </div>

                                                    @endif




                                                    @if($preview == null)

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Continue
                                                            </button>
                                                        </div>

                                                    @else



                                                    @endif


                                                </div>


                                            </form>
                                        </div>


                                    </div>
                                    <hr>
                                    <div class="card-body">

                                        <form action="search-postpaid-token" method="GET">
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
                                                <th scope="col" class="cursor-pointer">Expected Fee</th>
                                                <th scope="col" class="cursor-pointer desc">Status</th>
                                                <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                <th scope="col" class="cursor-pointer desc">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($token_ledgers as $data)

                                                <tr>
                                                    <td>
                                                        <a href="view-user?id={{$data->user?->id ?? null}}">{{$data->user->last_name ?? "name"}} {{$data->user->first_name ?? "name"}}</a>
                                                    </td>
                                                    <td>{{$data->meterNo}}</td>
                                                    <td>{{$data->estate->title ?? "name"}}</td>
                                                    <td>{{number_format($data->trx_amount, 2)}}</td>
                                                    <td>{{number_format($data->expected_fee, 2)}}</td>
                                                    <td>
                                                        @if($data->paid_at)
                                                            <span class="badge text-bg-primary">Paid</span>
                                                        @else
                                                            <span class="badge text-bg-warning">Unpaid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{$data->created_at}}</td>
                                                    <td>
                                                        <a href="recepit?trx_id={{$data->trx_id}}&type=credit_token"
                                                           onclick="return confirmreprint();"
                                                           class="btn btn-primary">View Receipt</a>
                                                        <script>
                                                            function confirmreprint() {
                                                                return confirm('Are you sure you want to view receipt');
                                                            }
                                                        </script>
                                                    </td>
                                                </tr>

                                            @endforeach

                                            </tbody>

                                            <tfoot>

                                            </tfoot>
                                        </table><!-- end table -->

                                        <!-- ADD THIS -->
                                        <div class="d-flex justify-content-end mt-3">
                                            {{ $token_ledgers->links() }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div> <!-- container-fluid -->

        </div>

    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)

        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Postpaid Token</h4>
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
                                    <h5 class="card-title mb-0">Vending Information</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">


                                        <div class="d-flex justify-content-between my-4">
                                            <h5 class="card-title text-black mb-0">Generate Postpaid Token</h5>
                                        </div>

                                        <div class="col-xl-8 col-sm-12">
                                            <form action="postpaid-validate-meter" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Estate</label>
                                                                <input class="form-control" value="{{$title}}" required name="title" id="estate_title">
                                                                <input class="form-control" value="{{$estate_id}}" hidden required name="estate_id" id="estate_id">

                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Enter Meter No</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="meterNo" id="meterNo" required>
                                                            </div>



                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Power Source</label>
                                                                <select class="form-control" required
                                                                        name="tariff_id"
                                                                        id="tariff_id" disabled>
                                                                    <option value="">--Select Tariff--</option>
                                                                </select>
                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Amount</label>
                                                                <input type="number" class="form-control mb-3" name="amount"
                                                                       required>
                                                            </div>


                                                        </div>



                                                        <script>
                                                            $(document).ready(function () {
                                                                $('#estate_id, #meterNo').on('change input', function () {
                                                                    var estate_id = $('#estate_id').val();
                                                                    var meterNo = $('#meterNo').val();

                                                                    if (estate_id && (meterNo.length === 11 || meterNo.length === 13)) {
                                                                        $.ajax({
                                                                            url: '/admin/fetch-meter-tariffs',
                                                                            method: 'GET',
                                                                            data: {
                                                                                estate_id: estate_id,
                                                                                meterNo: meterNo
                                                                            },
                                                                            success: function (response) {
                                                                                if (response == 1) {
                                                                                    alert("Error: Meter is not assigned to any User yet.");
                                                                                    return;
                                                                                }
                                                                                if (response == 2) {
                                                                                    alert("Error: Estate meter does not have any tariff");
                                                                                    return;
                                                                                }
                                                                                if (response == 3) {
                                                                                    alert("Error: Tariff index not set for customer.");
                                                                                    return;
                                                                                }

                                                                                if (response && response.tariffs) {
                                                                                    populateTariffOptions(response.tariffs, response.meter);
                                                                                } else {
                                                                                    $('#tariff_id').prop('disabled', true).empty();
                                                                                }
                                                                            },
                                                                            error: function () {
                                                                                $('#tariff_id').prop('disabled', true).empty();
                                                                                alert("Error fetching tariff data. Please try again.");
                                                                            }
                                                                        });
                                                                    } else {
                                                                        $('#tariff_id').prop('disabled', true).empty();
                                                                    }
                                                                });



                                                        });

                                                        function populateTariffOptions(tariffs, meter) {
                                                            var tariffSelect = $('#tariff_id');
                                                            tariffSelect.empty();
                                                            tariffSelect.append('<option value="">--Select Tariff--</option>');

                                                            var isDualTariff = meter.isDualTariff === 'on' || meter.isDualTariff === '1' || meter.isDualTariff === 1 || meter.isDualTariff === true;

                                                            var nepaTariffs = tariffs.filter(t => t.type === 'nepa' || t.type === 'Grid');
                                                            var genTariffs = tariffs.filter(t => t.type === 'gen' || t.type === 'Off Grid');

                                                            if (isDualTariff) {
                                                                if (nepaTariffs.length > 0) {
                                                                    tariffSelect.append('<optgroup label="NEPA Tariffs">');
                                                                    nepaTariffs.forEach(function (tariff) {
                                                                        var label = getActiveTariffLabel(tariff, meter, 'nepa');
                                                                        tariffSelect.append('<option value="' + tariff.id + '">' + label + '</option>');
                                                                    });
                                                                    tariffSelect.append('</optgroup>');
                                                                }

                                                                if (genTariffs.length > 0) {
                                                                    tariffSelect.append('<optgroup label="Generator Tariffs">');
                                                                    genTariffs.forEach(function (tariff) {
                                                                        var label = getActiveTariffLabel(tariff, meter, 'gen');
                                                                        tariffSelect.append('<option value="' + tariff.id + '">' + label + '</option>');
                                                                    });
                                                                    tariffSelect.append('</optgroup>');
                                                                }
                                                            } else {
                                                                nepaTariffs.forEach(function (tariff) {
                                                                    var label = getActiveTariffLabel(tariff, meter, 'nepa');
                                                                    tariffSelect.append('<option value="' + tariff.id + '">' + label + '</option>');
                                                                });
                                                            }

                                                            tariffSelect.prop('disabled', false);

                                                        }

                                                            function getActiveTariffLabel(tariff, meter, type) {
                                                               var label = tariff.title;
                                                               var isCurrentlyActive = false;
                                                               var tariffStatus = '';

                                                               if (type === 'nepa' || type === 'Grid') {
                                                                   if (meter.NewTariffID == tariff.id) {
                                                                       isCurrentlyActive = true;
                                                                       tariffStatus = ' (New Grid)';
                                                                   } else if (meter.OldTariffID == tariff.id) {
                                                                       isCurrentlyActive = true;
                                                                       tariffStatus = ' (Old Grid)';
                                                                   }
                                                               } else if (type === 'gen' || type === 'Off Grid') {
                                                                   if (meter.NewTariffDual == tariff.id) {
                                                                       isCurrentlyActive = true;
                                                                       tariffStatus = ' (New Off Grid)';
                                                                   } else if (meter.OldTariffDual == tariff.id) {
                                                                       isCurrentlyActive = true;
                                                                       tariffStatus = ' (Old Off Grid)';
                                                                   }
                                                               }

                                                               return label + tariffStatus;
                                                           }


                                                    </script>




                                                    @else
                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Enter Meter No</label>
                                                            <input type="number" disabled class="form-control mb-3"
                                                                   value="{{$meter->meterNo}}" name="meterNo" required>
                                                        </div>




                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" disabled value="{{$amount}}"
                                                                   class="form-control mb-3" name="amount" required>
                                                        </div>

                                                    @endif




                                                    @if($preview == null)

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Continue
                                                            </button>
                                                        </div>

                                                    @else



                                                    @endif


                                                </div>


                                            </form>
                                        </div>


                                    </div>
                                    <hr>
                                    <div class="card-body">

                                        <form action="search-postpaid-token" method="GET">
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
                                                <th scope="col" class="cursor-pointer">Expected Fee</th>
                                                <th scope="col" class="cursor-pointer desc">Status</th>
                                                <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                <th scope="col" class="cursor-pointer desc">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($token_ledgers as $data)

                                                <tr>
                                                    <td>
                                                        <a href="view-user?id={{$data->user?->id ?? null}}">{{$data->user->last_name ?? "name"}} {{$data->user->first_name ?? "name"}}</a>
                                                    </td>
                                                    <td>{{$data->meterNo}}</td>
                                                    <td>{{$data->estate->title ?? "name"}}</td>
                                                    <td>{{number_format($data->trx_amount, 2)}}</td>
                                                    <td>{{number_format($data->expected_fee, 2)}}</td>
                                                    <td>
                                                        @if($data->paid_at)
                                                            <span class="badge text-bg-primary">Paid</span>
                                                        @else
                                                            <span class="badge text-bg-warning">Unpaid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{$data->created_at}}</td>
                                                    <td>
                                                        <a href="recepit?trx_id={{$data->trx_id}}&type=credit_token"
                                                           onclick="return confirmreprint();"
                                                           class="btn btn-primary">View Receipt</a>
                                                        <script>
                                                            function confirmreprint() {
                                                                return confirm('Are you sure you want to view receipt');
                                                            }
                                                        </script>
                                                    </td>
                                                </tr>

                                            @endforeach

                                            </tbody>

                                            <tfoot>

                                            </tfoot>
                                        </table><!-- end table -->

                                        <!-- ADD THIS -->
                                        <div class="d-flex justify-content-end mt-3">
                                            {{ $token_ledgers->links() }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div> <!-- container-fluid -->

        </div>

    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)

    @else
    @endif

@endsection
