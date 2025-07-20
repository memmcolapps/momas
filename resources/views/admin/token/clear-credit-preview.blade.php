@php use App\Models\Estate;use App\Models\User; @endphp
@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Clear Credit Token</h4>
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

                                        <div class="col-xl-6 col-sm-12">
                                            <form action="validate-meter" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Estate</label>
                                                                <input class="form-control" required name="estate_id"
                                                                       list="datalistOptions" id="exampleDataList"
                                                                       placeholder="Type to search...">
                                                                <datalist id="datalistOptions">
                                                                    @foreach($estate as $data)
                                                                        <option value="{{$data->title}}">
                                                                    @endforeach
                                                                </datalist>
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Enter Meter No</label>
                                                            <input type="number" class="form-control mb-3"
                                                                   name="meterNo" required>
                                                        </div>


                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" class="form-control mb-3" name="amount"
                                                                   required>
                                                        </div>

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

                                        <div class="col-xl-6 col-sm-12">

                                            <form action="generate-clear-credit-meter-token" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    <div class="">
                                                        <h5 class="card-title text-black mb-0">Clear Credit Token Preview</h5>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Estate</label>
                                                            <input required name="estate_id" value="{{$estate->title}}"
                                                                   hidden="">
                                                            <h6>{{$estate->title}}</h6>
                                                        </div>

                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Customer</label>
                                                            <input required name="user_id" value="{{$user->id}}"
                                                                   hidden="">
                                                            <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                        </div>

                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Meter No</label>
                                                            <input required name="meterNo" value="{{$meter->meterNo}}"
                                                                   hidden="">
                                                            <h6>{{$meter->meterNo}}</h6>
                                                        </div>

                                                    </div>


                                                    @if($amount > 0)

                                                        <input required name="vat" value="{{$vat}}" hidden="">
                                                        <input required name="estate_id" value="{{$estate_id}}"
                                                               hidden="">
                                                        <input required name="estate_name" value="{{$estate_name}}"
                                                               hidden="">
                                                        <input required name="amount" value="{{$amount}}" hidden="">


                                                        <hr>


                                                        <div
                                                            class="col-xl-4 my-4 d-flex justify-content-start col-sm-12">
                                                            <select class="form-control" required name="pay_type">
                                                                <option value=" ">--Choose Payment Gateway---</option>
                                                                <option value="paystack">Pay with Paystack</option>
                                                                <option value="flutterwave">Pay with Flutterwave
                                                                </option>
                                                                <option value="enkpay">Pay with Enkpay</option>
                                                            </select>
                                                        </div>


                                                        <div
                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Pay Now
                                                            </button>
                                                        </div>

                                                    @else

                                                        <input required name="vat" value="{{$vat}}" hidden="">
                                                        <input required name="estate_id" value="{{$estate_id}}"
                                                               hidden="">
                                                        <input required name="estate_name" value="{{$estate_name}}"
                                                               hidden="">
                                                        <input required name="amount" value="{{$amount}}" hidden="">
                                                        <input required name="pay_type" value="vend" hidden="">

                                                        <hr>
                                                        <div
                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Vend Now
                                                            </button>
                                                        </div>

                                                    @endif


                                                </div>


                                            </form>

                                        </div>


                                    </div>
                                    <hr>
                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">Customer Name</th>
                                                <th scope="col" class="cursor-pointer">Estate</th>
                                                <th scope="col" class="cursor-pointer">Meter Number</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer desc">Status</th>
                                                <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                <th scope="col" class="cursor-pointer desc">Action</th>


                                            </tr>
                                            </thead>
                                            <tbody>


                                            @foreach($credit_tokens as $data)

                                                <tr>
                                                    <td>
                                                        @php
                                                            $user = User::where('id', $data->user_id)->first();
                                                        @endphp
                                                        <a href="view-user?id={{$data->user_id}}">{{$user->last_name ?? "name"}} {{$user->first_name ?? "name"}}</a>

                                                    </td>

                                                    <td>

                                                        @php
                                                            $estate = Estate::where('id', $data->estate_id)->first();
                                                        @endphp
                                                        {{$estate->title ?? "name"}}

                                                    </td>

                                                    <td>{{$data->meterNo}}</a> </td>
                                                    <td>{{number_format($data->amount, 2)}}</td>
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
                                                            <a href="recepit?trx_id={{$data->trx_id}}"
                                                               onclick="return confirmreprint();"
                                                               class="btn btn-primary">Reprint</a>
                                                            <script>

                                                                function confirmreprint() {
                                                                    return confirm('Are you sure you want to reprint');
                                                                }
                                                            </script>

                                                        @elseif($data->status == 0)

                                                            <a href="retry-generate-token?trx_id={{$data->trx_id}}"
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

                                        <div class="col-xl-6 col-sm-12">
                                            <form action="validate-meter" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    @if($preview == null)
                                                        <div class="row">
                                                            <div class="col-xl-6 my-2 col-sm-12">
                                                                <label class="my-2">Estate</label>
                                                                <input class="form-control" required name="estate_id"
                                                                       value="{{$title}}" readonly>

                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Enter Meter No</label>
                                                            <input type="number" class="form-control mb-3"
                                                                   name="meterNo" required>
                                                        </div>


                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" class="form-control mb-3" name="amount"
                                                                   required>
                                                        </div>

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


                                        <div class="col-xl-6 col-sm-12">

                                            <form action="generate-clear-credit-meter-token" method="POST"
                                                  enctype="multipart/form-data">
                                                @csrf

                                                <div class="modal-body">

                                                    <div class="">
                                                        <h5 class="card-title text-black mb-0">Clear Credit Token Preview</h5>
                                                    </div>


                                                    <div class="row">
                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Estate</label>
                                                            <input required name="estate_id" value="{{$estate->title}}"
                                                                   hidden="">
                                                            <h6>{{$estate->title}}</h6>
                                                        </div>

                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Customer</label>
                                                            <input required name="user_id" value="{{$user->id}}"
                                                                   hidden="">
                                                            <h6>{{$user->first_name}} {{$user->last_name}}</h6>
                                                        </div>

                                                        <div class="col-xl-4 my-2 col-sm-12">
                                                            <label class="my-2">Meter No</label>
                                                            <input required name="meterNo" value="{{$meter->meterNo}}"
                                                                   hidden="">
                                                            <h6>{{$meter->meterNo}}</h6>
                                                        </div>

                                                    </div>


                                                    @if($amount > 0)

                                                        <input required name="vat" value="{{$vat}}" hidden="">
                                                        <input required name="estate_id" value="{{$estate_id}}"
                                                               hidden="">
                                                        <input required name="estate_name" value="{{$estate_name}}"
                                                               hidden="">
                                                        <input required name="amount" value="{{$amount}}" hidden="">


                                                        <hr>


                                                        <div
                                                            class="col-xl-4 my-4 d-flex justify-content-start col-sm-12">
                                                            <select class="form-control" required name="pay_type">
                                                                <option value=" ">--Choose Payment Gateway---</option>
                                                                <option value="paystack">Pay with Paystack</option>
                                                                <option value="flutterwave">Pay with Flutterwave
                                                                </option>
                                                                <option value="enkpay">Pay with Enkpay</option>
                                                            </select>
                                                        </div>


                                                        <div
                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Pay Now
                                                            </button>
                                                        </div>

                                                    @else

                                                        <input required name="vat" value="{{$vat}}" hidden="">
                                                        <input required name="estate_id" value="{{$estate_id}}"
                                                               hidden="">
                                                        <input required name="estate_name" value="{{$estate_name}}"
                                                               hidden="">
                                                        <input required name="amount" value="{{$amount}}" hidden="">
                                                        <input required name="pay_type" value="vend" hidden="">

                                                        <hr>
                                                        <div
                                                            class="col-xl-12 my-4 d-flex justify-content-start col-sm-12">
                                                            <button type="submit" class="btn btn-primary">Vend Now
                                                            </button>
                                                        </div>

                                                    @endif


                                                </div>


                                            </form>

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
                                                        <a href="view-user?id={{$data->id}}">{{$data->user->last_name ?? "Name"}} {{$data->user->first_name ?? "Name"}}</a>
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
                                                            <a href="recepit?trx_id={{$data->trx_id}}"
                                                               onclick="return confirmreprint();"
                                                               class="btn btn-primary">Reprint</a>
                                                            <script>

                                                                function confirmreprint() {
                                                                    return confirm('Are you sure you want to reprint');
                                                                }
                                                            </script>

                                                        @elseif($data->status == 0)

                                                            <a href="retry-generate-token?trx_id={{$data->trx_id}}"
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


            </div> <!-- container-fluid -->

        </div>

    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
    @endif

@endsection
