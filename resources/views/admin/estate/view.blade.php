<?php

    use App\Models\ModFeature;

    $handled_status = ModFeature::HANDLED_STATUS;
    // dd(array_keys($handled_status));
    function statusValue ($status) {
        return ModFeature::HANDLED_STATUS[$status];
    }

    // function statusString ($statuString)
?>

@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)


        <div class="content">
            <div class="container-fluid">

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






                    <div class="col-xl-6">
                        <div class="card">
                            <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop"
                                 data-bs-backdrop="static" data-bs-keyboard="false"
                                 tabindex="-1" aria-labelledby="staticBackdropLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5"
                                                id="staticBackdropLabel">Setup Paystack Information</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <form action="setup_paystack" method="POST">
                                            @csrf

                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Bank select -->
                                                    <div class="col-xl-6 col-sm-12">
                                                        <label class="my-2">Bank</label>
                                                        <select name="bank" id="bank-select" class="form-control">
                                                            @foreach($paystackbank['data'] as $bank)
                                                                <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-6 col-sm-12">
                                                        <label class="my-2">Account No</label>
                                                        <input type="text" name="account_no" id="account_no" class="form-control" placeholder="Enter account number">
                                                        <input type="text" name="id" value="{{$org->id}}" hidden>



                                                    </div>

                                                    <div class="col-xl-12 col-sm-12">
                                                        <label class="my-2">Account Name</label>
                                                        <input type="text" name="account_name" id="account_name" class="form-control" placeholder="Account name will appear here" readonly>
                                                    </div>
                                                </div>
                                            </div>




                                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                            <script>
                                                $(document).ready(function(){
                                                    // When the account number input loses focus
                                                    $('#account_no').on('blur', function(){
                                                        var accountNo = $(this).val().trim();
                                                        var bankCode   = $('#bank-select').val();

                                                        // Make sure the account number is of typical length (usually 10 digits in Nigeria)
                                                        if(accountNo.length === 10 && bankCode) {
                                                            $.ajax({
                                                                url: '/resolve-account', // This is your custom API route
                                                                method: 'GET',
                                                                data: {
                                                                    account_number: accountNo,
                                                                    bank_code: bankCode
                                                                },
                                                                success: function(response) {
                                                                    if(response.status) {
                                                                        // Assuming Paystack returns a data object with account_name
                                                                        $('#account_name').val(response.data.account_name);
                                                                    } else {
                                                                        alert('Could not retrieve account details.');
                                                                        $('#account_name').val('');
                                                                    }
                                                                },
                                                                error: function() {
                                                                    alert('An error occurred while resolving the account.');
                                                                    $('#account_name').val('');
                                                                }
                                                            });
                                                        }
                                                    });
                                                });
                                            </script>





                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Paystack Info</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card -->
                    </div> <!-- end col -->









                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Add New Estate</h4>
                    </div>
                </div>



                <div class="row">

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">

                                    <div class="d-flex align-items-center mb-2">
                                        <div
                                            class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                            <svg width="20" height="20" viewBox="0 0 100 105" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M50 0C22.4444 0 0 21.21 0 47.25C0 67.7775 13.8889 85.26 33.3333 91.7175V105H44.4444V94.185C46.2778 94.5 48.1111 94.5 50 94.5C51.8889 94.5 53.7222 94.5 55.5556 94.185V105H66.6667V91.7175C86.1111 85.2075 100 67.725 100 47.25C100 21.21 77.5556 0 50 0ZM62.5 63L45.8333 78.75L37.5 70.875L44.4444 64.3125L37.5 57.75L54.1667 42L62.5 49.875L55.5556 56.4375L62.5 63ZM72.2222 36.75H27.7778V26.25H72.2222V36.75Z" fill="#E50086" fill-opacity="0.52"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Meters</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$total_meters}}</h3>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="widget-first">

                                    <div class="d-flex align-items-center mb-2">
                                        <div
                                            class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                 viewBox="0 0 640 512">
                                                <path fill="#963b68"
                                                      d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64s-64 28.7-64 64s28.7 64 64 64m32 32h-64c-17.6 0-33.5 7.1-45.1 18.6c40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64m-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32S208 82.1 208 144s50.1 112 112 112m76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2m-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4"/>
                                            </svg>
                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Customers</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$customers}}</h3>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>





                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="estate-update-info" method="post">
                                @csrf

                                <div class="row">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="d-flex justify-content-start my-4">Estate Information</h6>
                                            <div class="d-flex justify-content-between">
                                                <div class="justify-content-end">
                                                    <div class="justify-content-end">
                                                        <a href="#" class="btn btn-primary text-white " data-bs-toggle="modal"
                                                           data-bs-target="#staticBackdrop">Paystack Payment</a>
                                                        <a href="#" class="btn btn-primary text-white">FlutterWave payment</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Estate Name</label>
                                        <input type="text" name="title" value="{{$org->title}}" class="form-control"
                                               required>
                                        <input hidden name="id" value="{{$org->id}}" class="form-control" required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">State</label>
                                        <input type="text" name="state" value="{{$org->state}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">City</label>
                                        <input type="text" name="city" value="{{$org->city}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">LGA</label>
                                        <input type="text" name="lga" value="{{$org->lga}}" class="form-control" required>

                                    </div>

                                    <div class="col-3">
                                        <label class="my-2">Status</label>
                                        <select type="text" name="status" class="form-control" required>
                                            <option value="2">Activate</option>
                                            <option value="0">Deactivate</option>

                                        </select>

                                    </div>



                                    <div class="col-3">
                                        <label class="my-2">Payment Type</label>
                                        <select type="text" name="ptype" class="form-control" required>
                                            @if($org->ptype == null)
                                                <option value="">--select type---</option>
                                                {{-- <option value="1">APP Only</option> --}}
                                                <option value="2">Web Only</option>
                                                <option value="3">App & Web</option>
                                           @elseif($org->ptype == 1){{-- <option value="1">APP Only</option> --}}
                                            <option value="2">Web Only</option>
                                            <option value="3">App & Web</option>
                                            @elseif($org->ptype == 2)<option value="2">Web Only</option>
                                            {{-- <option value="1">App Only</option> --}}
                                            <option value="3">App & Web</option>
                                            @elseif($org->ptype == 3)<option value="3">App & Web</option>
                                            {{-- <option value="1">App Only</option> --}}
                                            <option value="2">Web Only</option>
                                            @endif

                                        </select>

                                    </div>

                                </div>

                                <hr class="my-4">


                                <div class="row">
                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Paystak Subaccount</label>
                                        <input type="text" value="{{$org->paystack_subaccount}}" name="paystack_subaccount" class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Flutterwave Subaccount</label>
                                        <input type="text"  value="{{$org->flutterwave_subaccount}}" name="flutterwave_subaccount" class="form-control" >
                                    </div>



                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Charge Fee %</label>
                                        <input type="number" value="{{$org->charge_fee_precent}}" step="0.01" name="charge_fee_precent"  class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Charge Fee (Flat)</label>
                                        <input type="number" value="{{$org->charge_fee_flat}}" name="charge_fee_flat"  class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Estate Admin Fee</label>
                                        <input type="number" value="{{$org->admin_fee}}" name="estate_admin_fee"  class="form-control" >
                                    </div>


                                </div>


                                <button type="submit" class="col-2 d-flex btn btn-primary my-4">
                                    Update Info
                                </button>


                            </form>

                        </div>


                    </div>

                    <div class="card">

                        <div class="card-body">

                            <form action="estate-feature-update" method="post">
                                @csrf

                                <div class="row">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="d-flex justify-content-start my-4">Features Toggle</h6>
                                            <div class="d-flex justify-content-between">
                                                <div class="justify-content-end">
                                                </div>

                                            </div>
                                        </div>

                                    </div>


                                    @foreach ($estate_features as $feature)
                                    {{-- <?php dump($feature->toArray(), $org->id, $handled_status); ?> --}}
                                        <div class="col-2">
                                            <label class="my-2">{{ $feature->title }}</label>
                                            <select type="text" name="{{ $feature->slug }}" value="{{ $feature->status }}" class="form-control" required>
                                                @foreach (array_keys(ModFeature::HANDLED_STATUS) as $my_status)
                                                    <option value="{{ $my_status }}"
                                                        @selected($feature->status == $my_status)
                                                    >
                                                        {{ statusValue($my_status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input hidden name="id" value="{{$org->id}}" class="form-control" required>

                                        </div>
                                    @endforeach

                                </div>

                                <hr class="my-4">


                                <button type="submit" class="col-2 d-flex btn btn-primary my-4">
                                    Update Info
                                </button>


                            </form>

                        </div>


                    </div>

                    <div class="card " style="background: #f3ffff">
                        <div class="card-body">
                            <form action="estate-update-vat" method="post">
                                @csrf


                                <h6 class="d-flex justify-content-start my-4">Vat Information</h6>


                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">VAT % </label>
                                    <input type="text" step="0.01" name="vat" value="{{$org->estate_vat }}"
                                           class="form-control"
                                           required>

                                    <input type="text"  name="estate_id"
                                           value="{{$org->id}}"
                                           hidden>


                                </div>


                                <button type="submit" class="col-xl-2 col-sm-12 my-2 d-flex btn btn-primary">
                                    Update
                                </button>

                            </form>

                            <hr class="my-4">


                            <form action="estate-update-minpur" method="post">
                                @csrf


                                <h6 class="d-flex justify-content-start my-4">MIN/MAX VEND Information</h6>

                                <div class="row">
                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">MIN Vend</label>
                                        <input type="number" step="0.01" name="min_pur"
                                               value="{{$org->min_pur ?? 1000}}"
                                               class="form-control"
                                               required>


                                    </div>


                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">MAX venc</label>
                                        <input type="number" step="0.01" name="max_pur"
                                               value="{{$org->max_pur ?? 1000000}}"
                                               class="form-control"
                                               required>


                                        <input type="text"  name="estate_id"
                                               value="{{$org->id}}"
                                               hidden>

                                    </div>

                                </div>




                                <button type="submit" class="col-xl-2 col-sm-12 my-2 d-flex btn btn-primary">
                                    Update
                                </button>


                            </form>
                        </div>
                    </div>



                </div>


                    <div class="row">

                        <div class="card" style="background: #d1fff1">

                            <div class="card-body">


                                <div class="d-flex justify-content-between my-4">
                                    <h6 class="">Utilities Information</h6>
                                    <h6 class="mb-0">Total Utilities <span
                                            class="badge text-bg-secondary">NGN {{number_format($total_utility, 2)}}</span>
                                    </h6>

                                    @if($org != null)
                                        @if($org->duration != null)
                                            <h6 class="mb-0">Duration <span
                                                    class="badge text-bg-danger">{{strtoupper($org->duration)}}</span>
                                            </h6>
                                        @else
                                            <h6 class="mb-0">Duration <span class="badge text-bg-secondary">Set Duration</span>
                                            </h6>
                                        @endif

                                    @else


                                    @endif
                                </div>


                                <hr class="my-2">

                                <form action="update-duration" method="POST" class="">
                                    @csrf


                                    <div class="col-xl-4 col-sm-12">
                                        <h6 class="my-3">Add New Utility</h6>

                                        <label class="my-2">Set Duration</label>
                                        <select name="duration" required class="form-control">
                                            {{-- Placeholder, disabled so the user must pick a real one --}}
                                            <option value="" disabled
                                                {{ old('duration', $org->duration ?? '') === '' ? 'selected' : '' }}>
                                                Choose Duration
                                            </option>

                                            <!-- <option value="per_transaction"
                                                {{ old('duration', $org->duration ?? '') === 'per_transaction' ? 'selected' : '' }}>
                                                Per Transaction
                                            </option> -->

                                            <option value="weekly"
                                                {{ old('duration', $org->duration ?? '') === 'weekly' ? 'selected' : '' }}>
                                                Weekly
                                            </option>

                                            <option value="monthly"
                                                {{ old('duration', $org->duration ?? '') === 'monthly' ? 'selected' : '' }}>
                                                Monthly
                                            </option>

                                            <option value="yearly"
                                                {{ old('duration', $org->duration ?? '') === 'yearly' ? 'selected' : '' }}>
                                                Yearly
                                            </option>
                                        </select>

                                        <input type="hidden" name="id"
                                               value="{{ $org->id ?? '' }}"
                                               class="form-control"
                                               required>
                                    </div>


                                    <button type="submit" class="btn btn-primary my-3">Update duration
                                    </button>


                                </form>


                                <hr class="my-4">


                                @if($utility != null)

                                    <h6 class="my-3">Available Utilities</h6>






                                    @foreach($utility as $index => $data)

                                        <form action="update-utility" method="get" class="row">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$data->id}}"
                                                   required>
                                            <input type="hidden" name="type" value="{{ $data->type ?? 'service_charge' }}">

                                            @if(($data->type ?? 'service_charge') == 'service_charge')
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Title</label>
                                                    <input type="text" name="title" id="title_{{$index}}"
                                                           class="form-control" value="{{$data->title ?? "name"}}"
                                                           required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Amount</label>
                                                    <input type="text" name="amount" id="amount_{{$index}}"
                                                           class="form-control" value="{{$data->amount}}"
                                                           required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Duration</label>
                                                    <select name="duration" class="form-control">
                                                        <option value="per_transaction" @selected($data->duration == 'per_transaction')>Per Transaction</option>
                                                        <option value="weekly" @selected($data->duration == 'weekly')>Weekly</option>
                                                        <option value="monthly" @selected(($data->duration ?? 'monthly') == 'monthly')>Monthly</option>
                                                        <option value="yearly" @selected($data->duration == 'yearly')>Yearly</option>
                                                    </select>
                                                </div>
                                            @else
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Title</label>
                                                    <input type="text" name="title" id="title_{{$index}}"
                                                           class="form-control" value="{{$data->title ?? "name"}}"
                                                           required>
                                                </div>

                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Amount</label>
                                                    <input type="text" name="amount" id="amount_{{$index}}"
                                                           class="form-control" value="{{$data->amount}}"
                                                           required>
                                                </div>

                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Start Date</label>
                                                    <input type="date" name="start_date"
                                                           class="form-control" value="{{$data->start_date ? \Carbon\Carbon::parse($data->start_date)->format('Y-m-d') : ''}}">
                                                </div>

                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Mode of Payment</label>
                                                    <select name="mode_of_payment" class="form-control mode-of-payment" onchange="togglePaymentFields(this)">
                                                        <option value="">Select</option>
                                                        <option value="monthly_payment" @selected($data->mode_of_payment == 'monthly_payment')>Monthly Payment</option>
                                                        <option value="one_off" @selected($data->mode_of_payment == 'one_off')>One-Off</option>
                                                        <!-- <option value="fixed_charge" @selected($data->mode_of_payment == 'fixed_charge')>Fixed Charge</option> -->
                                                    </select>
                                                </div>

                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Activated</label>
                                                    <select name="activated" class="form-control">
                                                        <option value="0" @selected(!$data->activated)>No</option>
                                                        <option value="1" @selected($data->activated)>Yes</option>
                                                    </select>
                                                </div>

                                                @if($data->user_id && $data->user)
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-1">Customer</label>
                                                    <input type="text" class="form-control" value="{{ $data->user->first_name }} {{ $data->user->last_name }}" disabled>
                                                </div>
                                                @endif

                                                <div class="col-xl-3 col-sm-12 my-1 months-field" style="display: {{ $data->mode_of_payment == 'monthly_payment' ? 'block' : 'none' }};">
                                                    <label class="my-1">Number of Months</label>
                                                    <input type="number" name="payment_months" min="1" max="60"
                                                           class="form-control" value="{{$data->payment_months}}">
                                                </div>
                                            @endif

                                            <div class="col-12 my-3">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <a href="delete-utility?id={{$data->id}}"
                                                   class="btn btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                            </div>
                                        </form>

                                    @endforeach

                                @else
                                    <p>No utilities available.</p>
                                @endif


                                <hr class="my-2">


                                <form id="utility-form" action="estate-update-utilities" method="post">
                                    @csrf

                                    <div class="row">


                                        <!-- Container to hold dynamic fields -->
                                        <div id="utility-fields"></div>

                                        <!-- Add more button -->
                                        <div class="col-12 my-4">
                                            <button type="button" class="btn btn-success" id="add-more">
                                                Add Utility
                                            </button>
                                        </div>

                                        <!-- Hidden template for the new fields -->
                                        <div id="template" class="d-none" data-is-template="true">
                                            <div class="row">
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-2">Title</label>
                                                    <input type="text" name="title[]" class="form-control" required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 sc-field" style="display: none;">
                                                    <label class="my-2">Amount</label>
                                                    <input type="number" step="0.01" name="amount[]" class="form-control" required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 sc-field" style="display: none;">
                                                    <label class="my-2">Duration</label>
                                                    <select name="duration[]" class="form-control" required>
                                                        <!-- <option value="per_transaction">Per Transaction</option> -->
                                                        <option value="weekly">Weekly</option>
                                                        <option value="monthly" selected>Monthly</option>
                                                        <option value="yearly">Yearly</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 debt-field">
                                                    <label class="my-2">Amount</label>
                                                    <input type="number" step="0.01" name="amount[]" class="form-control" required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1">
                                                    <label class="my-2">Type</label>
                                                    <select name="type[]" class="form-control utility-type" onchange="toggleUtilityTypeFields(this)" required>
                                                        <option value="service_charge" selected>Service Charge</option>
                                                        <option value="debt">Debt</option>
                                                    </select>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 debt-field">
                                                    <label class="my-2">Start Date</label>
                                                    <input type="date" name="start_date[]" class="form-control" required>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 debt-field">
                                                    <label class="my-2">Mode of Payment</label>
                                                    <select name="mode_of_payment[]" class="form-control mode-of-payment" onchange="togglePaymentFields(this)" required>
                                                        <option value="">Select</option>
                                                        <option value="monthly_payment">Monthly Payment</option>
                                                        <option value="one_off">One-Off</option>
                                                        <!-- <option value="fixed_charge">Fixed Charge</option> -->
                                                    </select>
                                                </div>
                                                <div class="col-xl-3 col-sm-12 my-1 months-field debt-field" style="display: none;">
                                                    <label class="my-2">Number of Months</label>
                                                    <input type="number" name="payment_months[]" min="1" max="60" class="form-control" required>
                                                </div>
                                                <input name="estate_id" value="{{$org->id}}" hidden>
                                            </div>
                                        </div>

                                        <input type="hidden" id="utilities-data" name="utilities_data">

                                        <hr class="my-4">

                                        <button style="width: 200px" type="button"
                                                class="btn btn-primary w-40" id="save">Save Utility
                                        </button>
                                    </div>
                                </form>

                                <script>
                                    function toggleUtilityTypeFields(select) {
                                        var container = select.closest('.row');
                                        if (!container) return;
                                        var scFields = container.querySelectorAll('.sc-field');
                                        var debtFields = container.querySelectorAll('.debt-field');
                                        var percentField = container.querySelector('.percent-field');
                                        var monthsField = container.querySelector('.months-field');

                                        if (select.value === 'service_charge') {
                                            scFields.forEach(f => f.style.display = '');
                                            debtFields.forEach(f => f.style.display = 'none');
                                        } else {
                                            scFields.forEach(f => f.style.display = 'none');
                                            debtFields.forEach(f => f.style.display = '');
                                        }
                                    }

                                    function togglePaymentFields(select) {
                                        var container = select.closest('.row');
                                        if (!container) return;
                                        var monthsField = container.querySelector('.months-field');

                                        if (monthsField) monthsField.style.display = 'none';

                                        if (select.value === 'monthly_payment' && monthsField) {
                                            monthsField.style.display = 'block';
                                        }
                                    }

                                    function initTypeToggle(container) {
                                        var typeSelect = container.querySelector('.utility-type');
                                        if (typeSelect) toggleUtilityTypeFields(typeSelect);
                                    }

                                    let utilities = [];

                                    document.getElementById('add-more').addEventListener('click', function () {
                                        var template = document.getElementById('template').cloneNode(true);
                                        template.classList.remove('d-none');
                                        template.removeAttribute('id');

                                        document.getElementById('utility-fields').appendChild(template);
                                        initTypeToggle(template);
                                    });

                                    document.querySelectorAll('.utility-type').forEach(function(el) {
                                        initTypeToggle(el.closest('.row'));
                                    });

                                    document.getElementById('save').addEventListener('click', function () {
                                        // Get type selects only from the utility-fields container
                                        let types = document.querySelectorAll('#utility-fields select[name="type[]"]');
                                        let utilities = [];

                                        // Validate that all utilities have titles
                                        for (let i = 0; i < types.length; i++) {
                                            let container = types[i].closest('.row');
                                            let titleInput = container.querySelector('input[name="title[]"]');
                                            if (!titleInput || !titleInput.value.trim()) {
                                                alert('Title is required for utility at position ' + (i + 1));
                                                titleInput?.focus();
                                                return;
                                            }
                                        }

                                        for (let i = 0; i < types.length; i++) {
                                            let container = types[i].closest('.row');
                                            let type = types[i].value;

                                            if (type === 'service_charge') {
                                                let titles = container.querySelectorAll('input[name="title[]"]');
                                                let amounts = container.querySelectorAll('input[name="amount[]"]');
                                                let durations = container.querySelectorAll('select[name="duration[]"]');
                                                        utilities.push({
                                                            title: titles[0]?.value || '',
                                                            amount: amounts[amounts.length - 1]?.value || amounts[0]?.value || '',
                                                            type: 'service_charge',
                                                            duration: durations[0]?.value || 'monthly',
                                                            start_date: null,
                                                            mode_of_payment: null,
                                                            percent_payment: null,
                                                            payment_months: null,
                                                        });
                                            } else {
                                                let titles = container.querySelectorAll('input[name="title[]"]');
                                                let amounts = container.querySelectorAll('input[name="amount[]"]');
                                                let startDates = container.querySelectorAll('input[name="start_date[]"]');
                                                let modeOfPayments = container.querySelectorAll('select[name="mode_of_payment[]"]');
                                                let paymentMonthsList = container.querySelectorAll('input[name="payment_months[]"]');
                                                utilities.push({
                                                    title: titles[0]?.value || '',
                                                    amount: amounts[amounts.length - 1]?.value || amounts[0]?.value || '',
                                                    type: 'debt',
                                                    duration: null,
                                                    start_date: startDates[0]?.value || null,
                                                    mode_of_payment: modeOfPayments[0]?.value || null,
                                                    payment_months: paymentMonthsList[0]?.value || null,
                                                });
                                            }
                                        }

                                        console.log("Utilities Data: ", utilities);
                                        document.getElementById('utilities-data').value = JSON.stringify(utilities);
                                        console.log("Hidden Input Value: ", document.getElementById('utilities-data').value);
                                        document.getElementById('utility-form').submit();
                                    });
                                </script>


                            </div>


                        </div>


                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title text-black mb-0">Service Charges</h5>
                                        <span class="badge text-bg-info">{{ $service_charges->count() }} Total</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="cursor-pointer">ID</th>
                                            <th scope="col" class="cursor-pointer">Title</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Mode of Payment</th>
                                            <th scope="col" class="cursor-pointer">Start Date</th>
                                            <th scope="col" class="cursor-pointer">Activated</th>
                                            <th scope="col" class="cursor-pointer">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($service_charges as $sc)
                                            <tr>
                                                <td>{{ $sc->id }}</td>
                                                <td>{{ $sc->title }}</td>
                                                <td>NGN {{ number_format($sc->amount, 2) }}</td>
                                                <td>{{ str_replace('_', ' ', ucwords($sc->mode_of_payment ?? 'N/A')) }}</td>
                                                <td>{{ $sc->created_at ? \Carbon\Carbon::parse($sc->created_at)->format('Y-m-d') : '-' }}</td>
                                                <td>
                                                    @if($sc->activated)
                                                        <span class="badge text-bg-success">Yes</span>
                                                    @else
                                                        <span class="badge text-bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($sc->status == 2)
                                                        <span class="badge text-bg-primary">Active</span>
                                                    @elseif($sc->status == 0)
                                                        <span class="badge text-bg-warning">Inactive</span>
                                                    @else
                                                        <span class="badge text-bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No service charge utilities found.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card overflow-hidden">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="card-title text-black mb-0">Debt Type Utilities</h5>
                                        <span class="badge text-bg-warning">{{ $debt_utilities->count() }} Total</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="cursor-pointer">ID</th>
                                            <th scope="col" class="cursor-pointer">Title</th>
                                            <th scope="col" class="cursor-pointer">Amount</th>
                                            <th scope="col" class="cursor-pointer">Mode of Payment</th>
                                            <th scope="col" class="cursor-pointer">Start Date</th>
                                            <th scope="col" class="cursor-pointer">Activated</th>
                                            <th scope="col" class="cursor-pointer">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($debt_utilities as $debt)
                                            <tr>
                                                <td>{{ $debt->id }}</td>
                                                <td>{{ $debt->title }}</td>
                                                <td>NGN {{ number_format($debt->amount, 2) }}</td>
                                                <td>{{ str_replace('_', ' ', ucwords($debt->mode_of_payment ?? 'N/A')) }}</td>
                                                <td>{{ $debt->start_date ? \Carbon\Carbon::parse($debt->start_date)->format('Y-m-d') : '-' }}</td>
                                                <td>
                                                    @if($debt->activated)
                                                        <span class="badge text-bg-success">Yes</span>
                                                    @else
                                                        <span class="badge text-bg-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($debt->status == 2)
                                                        <span class="badge text-bg-primary">Active</span>
                                                    @elseif($debt->status == 0)
                                                        <span class="badge text-bg-warning">Inactive</span>
                                                    @else
                                                        <span class="badge text-bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No debt type utilities found.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


            </div>
        </div>


    @elseif(Auth::user()->role == 1)


    @elseif(Auth::user()->role == 2)


    @elseif(Auth::user()->role == 3)



    @elseif(Auth::user()->role == 4)


    @elseif(Auth::user()->role == 5)


    @else


    @endif


@endsection
