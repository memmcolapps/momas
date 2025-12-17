@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Credit Token</h4>
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
                                            <h5 class="card-title text-black mb-0">Meter Information</h5>
                                        </div>

                                        <div class="col-xl-12 col-sm-12">

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <form action="validate-meter" method="POST"
                                                              enctype="multipart/form-data">
                                                            @csrf
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Estate</label>
                                                                <select class="form-control" required name="estate_id">
                                                                    <option value="">-- Select Estate --</option>
                                                                    @foreach($estate as $data)
                                                                        <option value="{{$data->id}}">{{$data->title}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Enter Meter No</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="meterNo" required>
                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Amount</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="amount" required>
                                                            </div>

                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Continue</button>
                                                        </form>

                                                    @else

                                                        <div class="row">


                                                            <div class="col-xl-5  my-2 col-sm-12">

                                                                <div>
                                                                    <label class="my-2">Enter Meter No</label>
                                                                    <input type="number" disabled
                                                                           class="form-control mb-3"
                                                                           value="{{$meter->meterNo}}" name="meterNo"
                                                                           required>
                                                                </div>


                                                                <div>
                                                                    <label class="my-2">Tarrif Amount</label>
                                                                    <input type="number" disabled
                                                                           value="{{$tarrif_amount}}"
                                                                           class="form-control mb-3"
                                                                           name="tarrif_amount"
                                                                           required>
                                                                </div>


                                                                <div>
                                                                    <label class="my-2">Amount</label>
                                                                    <input type="number" disabled value="{{$amount}}"
                                                                           class="form-control mb-3" name="amount"
                                                                           required>
                                                                </div>


                                                            </div>

                                                            <div class="col-xl-7 my-2 col-sm-12">

                                                                <form action="generate-credit-meter-token" method="POST"
                                                                      enctype="multipart/form-data">
                                                                    @csrf

                                                                    <div class="modal-body">

                                                                        <div class="">
                                                                            <h5 class="card-title text-black mb-0">Credit Token
                                                                                Preview</h5>
                                                                        </div>


                                                                        <div class="row">
                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Estate</label>
                                                                                <h6>{{$estate->title}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Customer</label>
                                                                                <input required name="user_id"
                                                                                       value="{{$user->id}}"
                                                                                       hidden="">
                                                                                <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Meter No</label>
                                                                                <input required name="meterNo"
                                                                                       value="{{$meter->meterNo}}" hidden="">
                                                                                <h6>{{$meter->meterNo}}</h6>
                                                                            </div>

                                                                        </div>


                                                                        <hr>

                                                                        <div class="row">

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Unit</label>
                                                                                @php
                                                                                    $unnit = $costOfUnit / $tarrif_amount;
                                                                                @endphp
                                                                                <input required name="unit"
                                                                                       value="{{$unnit}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($unnit, 2)}}kw/h</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Vat Amount</label>
                                                                                <input required name="vatAmount"
                                                                                       value="{{$vatAmount}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($vatAmount, 2)}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Cost Of Unit</label>
                                                                                <input required name="costOfUnit"
                                                                                       value="{{$costOfUnit}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($costOfUnit, 2)}}</h6>
                                                                            </div>

                                                                            <input required name="vat" value="{{$vat}}"
                                                                                   hidden="">
                                                                            <input required name="estate_id"
                                                                                   value="{{$estate_id}}"
                                                                                   hidden="">
                                                                            <input required name="estate_name"
                                                                                   value="{{$estate_name}}"
                                                                                   hidden="">
                                                                            <input required name="amount" value="{{$amount}}"
                                                                                   hidden="">
                                                                            <input required name="tariff_amount"
                                                                                   value="{{$tarrif_amount}}" hidden="">

                                                                            <input required name="t_index"
                                                                                   value="{{$tarrif_index}}" hidden="">
                                                                            <input required name="tariff_id"
                                                                                   value="{{$tariff_id}}" hidden="">

                                                                        </div>

                                                                        <hr>


                                                                        <div
                                                                            class="col-xl-4 my-4 d-flex justify-content-start col-sm-12">
                                                                            <select class="form-control" required
                                                                                    name="pay_type">
                                                                                <option value=" ">--Choose Payment Gateway---
                                                                                </option>
                                                                                <option value="paystack">Pay with Paystack
                                                                                </option>
                                                                                <option value="flutterwave">Pay with Flutterwave
                                                                                </option>
                                                                                <option value="enkpay">Pay with Enkpay</option>
                                                                                <option value="test_bypass">Payment Bypass (Testing Only)</option>
                                                                            </select>
                                                                        </div>


                                                                        <div
                                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                                            <button type="submit" class="btn btn-primary">Pay
                                                                                Now
                                                                            </button>
                                                                        </div>


                                                                    </div>


                                                                </form>


                                                            </div>

                                                            </div>

                                                    @endif


                                                </div>

                                        </div>




                                    </div>
                                    <hr>
                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">Customer Name</th>
                                                <th scope="col" class="cursor-pointer">Meter Number</th>
                                                <th scope="col" class="cursor-pointer">Estate</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer">Tariff Index</th>
                                                <th scope="col" class="cursor-pointer desc">Unit</th>
                                                <th scope="col" class="cursor-pointer desc">Status</th>
                                                <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                <th scope="col" class="cursor-pointer desc">Action</th>


                                            </tr>
                                            </thead>
                                            <tbody>


                                            @foreach($credit_tokens as $data)

                                                <tr>
                                                    <td>
                                                        <a href="view-user?id={{$data->id}}">{{$data->user->last_name ?? "name"}} {{$data->user->first_name ?? "name"}}</a>
                                                    </td>
                                                    <td>{{$data->meterNo}}</a> </td>
                                                    <td>{{$data->estate->title ?? "name"}}</td>
                                                    <td>{{number_format($data->amount, 2)}}</td>
                                                    <td>{{$data->tariff_id}}</td>
                                                    <td>{{$data->unitkwh}}kw/N</td>
                                                    <td>
                                                        @if($data->status == 2)
                                                            <span class="badge text-bg-primary">Successful</span>
                                                        @elseif($data->status == 0)
                                                            <span class="badge text-bg-warning">Pending</span>
                                                        @elseif($data->status == 3)
                                                            <span class="badge text-bg-danger">Declined</span>
                                                        @endif

                                                    </td>
                                                    <td>{{$data->created_at}}</td>


                                                    <td>
                                                        @if($data->status == 2)
                                                            <a href="recepit?trx_id={{$data->trx_id}}&type=credit_token"
                                                               onclick="return confirmreprint();"
                                                               class="btn btn-primary">Reprint</a>
                                                            <script>

                                                                function confirmreprint() {
                                                                    return confirm('Are you sure you want to reprint');
                                                                }
                                                            </script>

                                                        @elseif($data->status == 0)

                                                            <a href="retry-generate-token?trx_id={{$data->trx_id}}&type=credit_token"
                                                               onclick="return confirmgenertetoken();"
                                                               class="btn btn-secondary">Generate Token</a>
                                                            <script>

                                                                function confirmgenertetoken() {
                                                                    return confirm('Are you sure you want to generate token');
                                                                }
                                                            </script>

                                                        @elseif($data->status == 3)
                                                            <span class="badge text-bg-danger">Declined</span>
                                                        @endif

                                                    </td>


                                                </tr>

                                            @endforeach


                                            </tbody><!-- end tbody -->

                                            <tfoot>


                                            </tfoot>
                                        </table><!-- end table -->
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
                        <h4 class="fs-18 fw-semibold m-0">Credit Token</h4>
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
                                            <h5 class="card-title text-black mb-0">Generate Credit Token</h5>
                                        </div>

                                        <div class="col-xl-12 col-sm-12">

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <form action="validate-meter" method="POST"
                                                              enctype="multipart/form-data">
                                                            @csrf
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Enter Meter No</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="meterNo" required>
                                                            </div>


                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Amount</label>
                                                                <input type="number" class="form-control mb-3"
                                                                       name="amount" required>
                                                            </div>

                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Continue</button>
                                                        </form>

                                                    @else

                                                        <div class="row">


                                                            <div class="col-xl-5  my-2 col-sm-12">

                                                                <div>
                                                                    <label class="my-2">Enter Meter No</label>
                                                                    <input type="number" disabled
                                                                           class="form-control mb-3"
                                                                           value="{{$meter->meterNo}}" name="meterNo"
                                                                           required>
                                                                </div>


                                                                <div>
                                                                    <label class="my-2">Tarrif Amount</label>
                                                                    <input type="number" disabled
                                                                           value="{{$tarrif_amount}}"
                                                                           class="form-control mb-3"
                                                                           name="tarrif_amount"
                                                                           required>
                                                                </div>


                                                                <div>
                                                                    <label class="my-2">Amount</label>
                                                                    <input type="number" disabled value="{{$amount}}"
                                                                           class="form-control mb-3" name="amount"
                                                                           required>
                                                                </div>


                                                            </div>

                                                            <div class="col-xl-7 my-2 col-sm-12">

                                                                <form action="generate-credit-meter-token" method="POST"
                                                                      enctype="multipart/form-data">
                                                                    @csrf

                                                                    <div class="modal-body">

                                                                        <div class="">
                                                                            <h5 class="card-title text-black mb-0">Credit Token
                                                                                Preview</h5>
                                                                        </div>


                                                                        <div class="row">
                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Estate</label>
                                                                                <h6>{{$estate->title}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Customer</label>
                                                                                <input required name="user_id"
                                                                                       value="{{$user->id}}"
                                                                                       hidden="">
                                                                                <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Meter No</label>
                                                                                <input required name="meterNo"
                                                                                       value="{{$meter->meterNo}}" hidden="">
                                                                                <h6>{{$meter->meterNo}}</h6>
                                                                            </div>

                                                                        </div>


                                                                        <hr>

                                                                        <div class="row">

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Unit</label>
                                                                                @php
                                                                                    $unnit = $costOfUnit / $tarrif_amount;
                                                                                @endphp
                                                                                <input required name="unit"
                                                                                       value="{{$unnit}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($unnit, 2)}}kw/h</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Vat Amount</label>
                                                                                <input required name="vatAmount"
                                                                                       value="{{$vatAmount}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($vatAmount, 2)}}</h6>
                                                                            </div>

                                                                            <div class="col-xl-4 my-2 col-sm-12">
                                                                                <label class="my-2">Cost Of Unit</label>
                                                                                <input required name="costOfUnit"
                                                                                       value="{{$costOfUnit}}"
                                                                                       hidden="">
                                                                                <h6>{{number_format($costOfUnit, 2)}}</h6>
                                                                            </div>

                                                                            <input required name="vat" value="{{$vat}}"
                                                                                   hidden="">
                                                                            <input required name="estate_id"
                                                                                   value="{{$estate_id}}"
                                                                                   hidden="">
                                                                            <input required name="estate_name"
                                                                                   value="{{$estate_name}}"
                                                                                   hidden="">
                                                                            <input required name="amount" value="{{$amount}}"
                                                                                   hidden="">
                                                                            <input required name="tariff_amount"
                                                                                   value="{{$tarrif_amount}}" hidden="">

                                                                            <input required name="t_index"
                                                                                   value="{{$tarrif_index}}" hidden="">
                                                                            <input required name="tariff_id"
                                                                                   value="{{$tariff_id}}" hidden="">

                                                                        </div>

                                                                        <hr>


                                                                        <div
                                                                            class="col-xl-4 my-4 d-flex justify-content-start col-sm-12">
                                                                            <select class="form-control" required
                                                                                    name="pay_type">
                                                                                <option value=" ">--Choose Payment Gateway---
                                                                                </option>
                                                                                <option value="paystack">Pay with Paystack
                                                                                </option>
                                                                                <option value="flutterwave">Pay with Flutterwave
                                                                                </option>
                                                                                <option value="enkpay">Pay with Enkpay</option>
                                                                                <option value="test_bypass">Payment Bypass (Testing Only)</option>
                                                                            </select>
                                                                        </div>


                                                                        <div
                                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                                            <button type="submit" class="btn btn-primary">Pay
                                                                                Now
                                                                            </button>
                                                                        </div>


                                                                    </div>


                                                                </form>


                                                            </div>

                                                        </div>

                                                    @endif


                                                </div>

                                        </div>

                                    </div>


                                    <hr>




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
