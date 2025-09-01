@php use App\Models\Meter; @endphp
@extends('layouts.main')
@section('content')


@if(Auth::user()->role == 0)
    {{-- SUPER ADMIN VIEW --}}
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">{{$meter->meterNo}}</h4>
                </div>
            </div>


            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <form action="update-meter-info" method="post">
                            @csrf

                            <div class="row">

                                <h6 class="d-flex justify-content-start my-4">Meter Information</h6>
                                <div class="col-3">
                                    <label class="my-2">Meter Number</label>
                                    <input type="number" disabled name="meterNo" value="{{$meter->meterNo}}"
                                           class="form-control"
                                           required>
                                    <input type="text" name="id" value="{{$meter->id}}"
                                           hidden>

                                </div>


                                <div class="col-3">
                                    <label class="my-2">Meter Model</label>
                                    <select type="text" name="meterModel" class="form-control" required>
                                        <option
                                            value="{{$meter->meterModel}}">{{strtoupper($meter->meterModel)}}</option>
                                        <option value="prepaid">Prepaid</option>
                                        <option value="postpaid">Postpaid</option>
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label class="my-2">Account No</label>
                                    <input type="text" name="AccountNo" value="{{$meter->AccountNo}}"
                                           class="form-control" required>
                                </div>

                                <div class="col-3">
                                    <label class="my-2">Estate</label>
                                    <select type="text" name="estate_id" class="form-control" required>
                                        <option
                                            value="{{$meter->estate_id}}">{{strtoupper($meter->estate->title)}}</option>
                                        @foreach($estate as $data)
                                            <option value="{{$data->id}}">{{$data->title}} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr class="my-4">


                                <div class="col-3">
                                    <label class="my-2">Transformer</label>
                                    <select type="text" name="TransformerID" class="form-control" required>
                                        <option value="{{$meter->TransformerID}}">{{strtoupper($trans_title)}}</option>
                                        @foreach($transformer as $data)
                                            <option value="{{$data->id}}">{{$data->Title}} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-3 mt-4">
                                    @if($meter->isDualTariff == 1)
                                        <input type="checkbox" id="isDualTariff" checked name="isDualTariff"
                                               class="form-check-input" style="border: 10px">

                                        <script>
                                            document.getElementById('isDualTariff').addEventListener('change', function () {
                                                var isChecked = this.checked;
                                                document.getElementById('newtar').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('newTariffDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('newSGCDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('oldTariffDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('oldSGCDualContainer').style.display = isChecked ? 'block' : 'none';


                                            });
                                        </script>

                                    @else
                                        <input type="checkbox" id="isDualTariff" name="isDualTariff"
                                               class="form-check-input" style="border: 10px">

                                        <script>
                                            document.getElementById('isDualTariff').addEventListener('change', function () {
                                                var isChecked = this.checked;
                                                document.getElementById('newtar').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('newTariffDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('newSGCDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('oldTariffDualContainer').style.display = isChecked ? 'block' : 'none';
                                                document.getElementById('oldSGCDualContainer').style.display = isChecked ? 'block' : 'none';


                                            });
                                        </script>

                                    @endif
                                    <label class="form-check-label">Is Dual Tariff</label>

                                </div>


                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">Old SGC</label>
                                    <select name="OldSGC" class="form-control" required>
                                        <option value="{{$meter->OldSGC}}">@if($meter->OldSGC == "999962") MOMAS Default (9***2)@else MOMAS System Nig Ltd (6***9) @endif</option>
                                        <option value="999962">MOMAS Default (9***2)</option>
                                        <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                    </select>
                                </div>


                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">New SGC</label>
                                    <select name="NewSGC" class="form-control" required>
                                        <option value="{{$meter->NewSGC}}">@if($meter->NewSGC == "600849") MOMAS System Nig Ltd (6***9) @else MOMAS Default (9***2)  @endif</option>
                                        <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        <option value="999962">MOMAS Default (9***2)</option>
                                    </select>
                                </div>


                                <hr class="my-4">


                                <div class="col-2" id="oldTariffDualContainer" style="display: none;">
                                    <label class="my-2">Old Tariff Dual</label>
                                    <select name="OldTariffDual" class="form-control">
                                        <option
                                            value="{{$meter->OldTariffDualID}}">{{strtoupper($meter->OldTariffDualID)}}</option>
                                        @foreach($tariffdual as $data)
                                            <option value="{{$data->OldTariffDual}}">{{$data->title}}</option>
                                        @endforeach
                                    </select>


                                </div>


                                <div class="col-2" id="newtar" style="display: none;">
                                    <label class="my-2">New Tariff Dual ID</label>
                                    <select name="NewTariffDual" class="form-control">
                                        <option
                                            value="{{$meter->NewTariffDualID}}">{{strtoupper($meter->NewTariffDualID)}}</option>
                                        @foreach($tariffdual as $data)
                                            <option value="{{$data->NewTariffDual}}">{{$data->title}}</option>
                                        @endforeach
                                    </select>

                                </div>


                                <div class="col-2">
                                    <label class="my-2">New Tariff</label>
                                    <select name="NewTariffID" class="form-control">
                                        <option
                                            value="{{$meter->NewTariffID}}">{{strtoupper($new_tariff_title)}}</option>
                                        @foreach($tariff as $data)
                                            <option value="{{$data->id}}">{{$data->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2">
                                    <label class="my-2">Old Tariff</label>
                                    <select type="text" name="OldTariffID" class="form-control" required>
                                        <option
                                            value="{{$meter->OldTariffID}}">{{strtoupper($old_tariff_title)}}</option>
                                        @foreach($tariff as $data)
                                            <option value="{{$data->id}}">{{$data->title}} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2 " id="newTariffDualContainer" style="display: none;">
                                    <label class="my-2">New SGC Dual</label>
                                    <input type="text" value="{{$meter->NewSGCDual}}" name="NewSGCDual"
                                           class="form-control">
                                </div>


                                <div class="col-2 " id="newSGCDualContainer" style="display: none;">
                                    <label class="my-2">OLD SGC Dual</label>
                                    <input type="text" value="{{$meter->OldSGCDual}}" name="OldSGCDual"
                                           class="form-control">
                                </div>


                                @if($meter->isDualTariff == "on")





                                @else







                                @endif


                                <hr class="my-4">


                                <div class="col-3">
                                    <label class="my-2">KRN1</label>
                                    <input type="text" value="{{$meter->KRN1}}" name="KRN1" class="form-control"
                                           required>
                                </div>

                                <div class="col-3">
                                    <label class="my-2">KRN2</label>
                                    <input type="text" value="{{$meter->KRN2}}" name="KRN2" class="form-control"
                                           required>
                                </div>


                                <div class="col-3 mt-4">
                                    @if($meter->NeedKCT == "on" || $meter->NeedKCT == 1)
                                        <input type="checkbox" name="NeedKCT" checked class="form-check-input"
                                               style="border: 10px">
                                        <label class="form-check-label">Need KCT</label>
                                    @else

                                        <input type="checkbox" name="NeedKCT" class="form-check-input"
                                               style="border: 10px">
                                        <label class="form-check-label">Need KCT</label>

                                    @endif


                                </div>


                                <div class="col-3">
                                    <label class="my-2">Credit Type</label>
                                    <select type="text" name="CreditTypeID" class="form-control" required>
                                        <option
                                            value="{{$meter->CreditTypeID}}">{{strtoupper($meter->CreditTypeID)}}</option>
                                        <option value="water">Water</option>
                                        <option value="gas">Gas</option>
                                        <option value="electricity">Electricity</option>
                                    </select>
                                </div>


                            </div>


                            <hr class="my-4">

                            <button type="submit" class="col-2 d-flex btn btn-primary">
                                Update Meter
                            </button>


                        </form>


                    </div>


                </div>


            </div>

            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">

                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">All Transaction</h5>
                                            <a href="/export-metertransactions?meterNo={{$meter->meterNo}}"
                                               class="btn btn-primary mb-3">Export</a>

                                        </div>


                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                <tr>
                                                    <th scope="col" class="cursor-pointer">Trx ID</th>
                                                    <th scope="col" class="cursor-pointer">Meter No</th>
                                                    <th scope="col" class="cursor-pointer">Customer</th>
                                                    <th scope="col" class="cursor-pointer">Estate</th>
                                                    <th scope="col" class="cursor-pointer">Amount</th>
                                                    <th scope="col" class="cursor-pointer">Status</th>
                                                    <th scope="col" class="cursor-pointer desc">Date</th>


                                                </tr>
                                                </thead>
                                                <tbody>


                                                @foreach($transactions as $data)

                                                    <tr>
                                                        <td><a href="#" class="" data-bs-toggle="modal"
                                                               data-bs-target="#staticBackdrop{{$data->trx_id}}">{{$data->trx_id}}</a>

                                                            <div class="col-xl-6">
                                                                <div class="card">
                                                                    <div class="modal fade"
                                                                         id="staticBackdrop{{$data->trx_id}}"
                                                                         data-bs-backdrop="static"
                                                                         data-bs-keyboard="false"
                                                                         tabindex="-1"
                                                                         aria-labelledby="staticBackdropLabel"
                                                                         aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h1 class="modal-title fs-5"
                                                                                        id="staticBackdropLabel">{{$data->trx_id}}</h1>
                                                                                    <button type="button"
                                                                                            class="btn-close"
                                                                                            data-bs-dismiss="modal"
                                                                                            aria-label="Close"></button>
                                                                                </div>


                                                                                <div class="modal-body">

                                                                                    <div class="row">
                                                                                        <div class="col-4">

                                                                                            <label>Transaction
                                                                                                ID</label>
                                                                                            <div>{{$data->trx_id}}</div>

                                                                                        </div>

                                                                                        @if($data->service_type == "credit_token")
                                                                                            <div class="col-4">
                                                                                                <label>Meter No</label>
                                                                                                <div>{{$data->creditToken->meterNo ?? "123456"}}</div>
                                                                                            </div>
                                                                                        @endif

                                                                                        <div class="col-4">
                                                                                            <label>Amount</label>
                                                                                            <div>
                                                                                                NGN {{number_format($data->amount, 2)}}</div>
                                                                                        </div>

                                                                                    </div>

                                                                                    <hr>

                                                                                    <div class="row">

                                                                                        @if($data->pay_type == "paystack")
                                                                                            <div class="col-4">
                                                                                                <label>Pay
                                                                                                    Channel</label>
                                                                                                <div>{{"Paystack"}}</div>
                                                                                            </div>

                                                                                            <div class="col-4">
                                                                                                <label>Pay Ref</label>
                                                                                                <div>{{$data->payment_ref}}</div>
                                                                                            </div>
                                                                                        @endif

                                                                                        <div class="col-4">

                                                                                            <label>Customer Name</label>
                                                                                            <div>{{$data->user->last_name ?? "name"}}</div>

                                                                                        </div>

                                                                                    </div>

                                                                                    <hr>


                                                                                    <div class="row">


                                                                                    </div>


                                                                                </div>


                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </td>


                                                        <td>
                                                            <div>{{$data->meterNo ?? "123456"}}</div>
                                                        </td>

                                                        <td>
                                                            <a href="view-user?id={{$data->user->first_name ?? "name"}}">{{$data->user->last_name ?? "name"}}</a>
                                                        </td>
                                                        <td>{{$data->estate->title ?? "Estate"}}</td>
                                                        <td>{{number_format($data->amount, 2)}}</td>
                                                        <td>
                                                            @if($data->status == 2)
                                                                <span class="badge text-bg-primary">Approved</span>
                                                            @elseif($data->status == 0)
                                                                <span class="badge text-bg-dark">Pending</span>
                                                            @elseif($data->status == 3)
                                                                <span class="badge text-bg-dark">Refunded</span>
                                                            @endif
                                                        </td>
                                                        <td>{{$data->created_at}}</td>

                                                    </tr>

                                                @endforeach


                                                </tbody><!-- end tbody -->

                                                <tfoot>

                                                {{ $transactions->links() }}


                                                </tfoot>
                                            </table><!-- end table -->
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
    </div>

@elseif(Auth::user()->role == 1)
    {{-- ROLE 1 PLACEHOLDER --}}

@elseif(Auth::user()->role == 2)
    {{-- ROLE 2 PLACEHOLDER --}}

@elseif(Auth::user()->role == 3)
    {{-- ESTATE ADMIN VIEW --}}
    <div class="content">
        <div class="container-fluid">
            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">{{$meter->meterNo}}</h4>
                </div>
            </div>

            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <form action="update-meter-info" method="post">
                            @csrf
                            <div class="row">
                                <h6 class="d-flex justify-content-start my-4">Meter Information</h6>
                                
                                <div class="col-3">
                                    <label class="my-2">Meter Number</label>
                                    <input type="number" disabled name="meterNo" value="{{$meter->meterNo}}" class="form-control" required>
                                    <input type="text" name="id" value="{{$meter->id}}" hidden>
                                </div>

                                <div class="col-3">
                                    <label class="my-2">Meter Model</label>
                                    <select type="text" name="meterModel" class="form-control" required>
                                        <option value="{{$meter->meterModel}}">{{strtoupper($meter->meterModel)}}</option>
                                        <option value="prepaid">Prepaid</option>
                                        <option value="postpaid">Postpaid</option>
                                    </select>
                                </div>

                                {{-- Read-only Account No for Estate Admin --}}
                                <div class="col-3">
                                    <label class="my-2">Account No</label>
                                    <input type="text" value="{{$meter->AccountNo}}" class="form-control" disabled>
                                    <input type="hidden" name="AccountNo" value="{{$meter->AccountNo}}">
                                </div>

                                {{-- Read-only Estate for Estate Admin --}}
                                <div class="col-3">
                                    <label class="my-2">Estate</label>
                                    <input type="text" value="{{$meter->estate->title}}" class="form-control" disabled>
                                    <input type="hidden" name="estate_id" value="{{$meter->estate_id}}">
                                </div>

                                <hr class="my-4">

                                <!-- <div class="col-3">
                                    <label class="my-2">Transformer</label>
                                    <select type="text" name="TransformerID" class="form-control" required>
                                        <option value="{{$meter->TransformerID}}">{{strtoupper($trans_title)}}</option>
                                        @foreach($transformer as $data)
                                            <option value="{{$data->id}}">{{$data->Title}}</option>
                                        @endforeach
                                    </select>
                                </div> -->

                                {{-- Only show tariff selections for Estate Admin --}}
                                <div class="col-2">
                                    <label class="my-2">New Tariff</label>
                                    <select name="NewTariffID" class="form-control">
                                        <option value="{{$meter->NewTariffID}}">{{$new_tariff_title}}</option>
                                        @foreach($tariff as $data)
                                            @if($data->id != $meter->NewTariffID)
                                                <option value="{{$data->id}}">{{$data->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2">
                                    <label class="my-2">Old Tariff</label>
                                    <select type="text" name="OldTariffID" class="form-control" required>
                                        <option value="{{$meter->OldTariffID}}">{{$old_tariff_title}}</option>
                                        @foreach($tariff as $data)
                                            @if($data->id != $meter->OldTariffID)
                                                <option value="{{$data->id}}">{{$data->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">Update Meter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Transaction History Section --}}
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">All Transaction</h5>
                                            <a href="/export-metertransactions?meterNo={{$meter->meterNo}}" class="btn btn-primary mb-3">Export</a>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="cursor-pointer">Trx ID</th>
                                                        <th scope="col" class="cursor-pointer">Meter No</th>
                                                        <th scope="col" class="cursor-pointer">Customer</th>
                                                        <th scope="col" class="cursor-pointer">Estate</th>
                                                        <th scope="col" class="cursor-pointer">Amount</th>
                                                        <th scope="col" class="cursor-pointer">Status</th>
                                                        <th scope="col" class="cursor-pointer desc">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transactions as $data)
                                                        <tr>
                                                            <td><a href="#" class="" data-bs-toggle="modal" data-bs-target="#staticBackdrop{{$data->trx_id}}">{{$data->trx_id}}</a>
                                                                {{-- Modal content here --}}
                                                            </td>
                                                            <td><div>{{$data->meterNo ?? "123456"}}</div></td>
                                                            <td><a href="view-user?id={{$data->user->first_name ?? "name"}}">{{$data->user->last_name ?? "name"}}</a></td>
                                                            <td>{{$data->estate->title ?? "Estate"}}</td>
                                                            <td>{{number_format($data->amount, 2)}}</td>
                                                            <td>
                                                                @if($data->status == 2)
                                                                    <span class="badge text-bg-primary">Approved</span>
                                                                @elseif($data->status == 0)
                                                                    <span class="badge text-bg-dark">Pending</span>
                                                                @elseif($data->status == 3)
                                                                    <span class="badge text-bg-dark">Refunded</span>
                                                                @endif
                                                            </td>
                                                            <td>{{$data->created_at}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    {{ $transactions->links() }}
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif(Auth::user()->role == 4)
    {{-- ROLE 4 PLACEHOLDER --}}

@elseif(Auth::user()->role == 5)
    {{-- ROLE 5 PLACEHOLDER --}}

@else
    {{-- DEFAULT FALLBACK --}}

@endif

@endsection