@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Project Settings</h4>
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

                    <div class="card">

                        <div class="card-body">

                            <form action="features" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Project Features</h6>

                                    <div class="col-2">
                                        <label class="my-2">Momas Meter</label>
                                        <select type="text" name="momas_meter" class="form-control" required>
                                            @if($fea->momas_meter == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->momas_meter == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>


                                    <div class="col-2">
                                        <label class="my-2">Other Meter</label>
                                        <select type="text" name="other_meter" class="form-control" required>
                                            @if($fea->other_meter == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->other_meter == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>


                                    <div class="col-2">
                                        <label class="my-2">Print Token</label>
                                        <select type="text" name="print_token" class="form-control" required>
                                            @if($fea->print_token == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->print_token == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>

                                    <div class="col-2">
                                        <label class="my-2">Access Token</label>
                                        <select type="text" name="access_token" class="form-control" required>
                                            @if($fea->access_token == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->access_token == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>

                                    <div class="col-2">
                                        <label class="my-2">Services</label>
                                        <select type="text" name="services" class="form-control" required>
                                            @if($fea->services == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->services == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>

                                    <div class="col-2">
                                        <label class="my-2">Bill Payment</label>
                                        <select type="text" name="bill_payment" class="form-control" required>
                                            @if($fea->bill_payment == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->bill_payment == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>

                                    <div class="col-2">
                                        <label class="my-2">Support</label>
                                        <select type="text" name="support" class="form-control" required>
                                            @if($fea->support == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->support == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>

                                    <div class="col-2">
                                        <label class="my-2">Analysis</label>
                                        <select type="text" name="analysis" class="form-control" required>
                                            @if($fea->analysis == 1)
                                                <option value="1">ON</option>
                                            @elseif($fea->analysis == 0)
                                                <option value="0">OFF</option>
                                            @endif
                                            <option value="1">ON</option>
                                            <option value="0">OFF</option>
                                        </select>

                                    </div>


                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Features
                                </button>


                            </form>


                        </div>


                    </div>


                </div>

                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="payment-keys" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Payment Keys</h6>

                                    <div class="col-6">
                                        <label class="my-2">Flutterwave Secret</label>
                                        <input type="text" name="flutterwave_secret" class="form-control"
                                               value="{{$set->flutterwave_secret}}">

                                    </div>

                                    <div class="col-6">
                                        <label class="my-2">Flutterwave Public</label>
                                        <input type="text" name="flutterwave_public" class="form-control"
                                               value="{{$set->flutterwave_public}}">

                                    </div>

                                    <div class="col-6">
                                        <label class="my-2">Paystack Secret</label>
                                        <input type="text" name="paystack_secret" class="form-control"
                                               value="{{$set->paystack_secret}}">

                                    </div>

                                    <div class="col-6">
                                        <label class="my-2">Paystack Public</label>
                                        <input type="text" name="paystack_public" class="form-control"
                                               value="{{$set->paystack_public}}">

                                    </div>


                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Payment Keys
                                </button>


                            </form>


                        </div>


                    </div>


                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="support-set" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Support Settings</h6>

                                    <div class="col-6">
                                        <label class="my-2">Payment Support</label>
                                        <input type="text" name="payment_support" class="form-control"
                                               value="{{$set->payment_support}}">

                                    </div>

                                    <div class="col-6">
                                        <label class="my-2">Meter Support</label>
                                        <input type="text" name="meter_support" class="form-control"
                                               value="{{$set->meter_support}}">

                                    </div>

                                    <div class="col-6">
                                        <label class="my-2">General Support</label>
                                        <input type="text" name="general_support" class="form-control"
                                               value="{{$set->general_support}}">

                                    </div>


                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Support
                                </button>


                            </form>


                        </div>


                    </div>


                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="admin-fee-update" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Administrative Charge</h6>

                                    <div class="col-3">
                                        <label class="my-2">Charge</label>
                                        <input type="number" name="admin_fee" class="form-control" value="{{$set->admin_fee}}">
                                    </div>

                                    <div class="col-3">
                                        <label class="my-2">KCT Fee</label>
                                        <input type="text" name="kct_fee" class="form-control" value="{{$set->kct_fee}}">
                                    </div>

                                    <div class="col-3">
                                        <label class="my-2">Clear Tamper Fee</label>
                                        <input type="text" name="clear_tamper_fee" class="form-control" value="{{$set->clear_tamper_fee}}">
                                    </div>

                                    <div class="col-3">
                                        <label class="my-2">Clear Credit Fee</label>
                                        <input type="text" name="clear_credit_fee" class="form-control" value="{{$set->clear_credit_fee}}">
                                    </div>




                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Fee
                                </button>


                            </form>


                        </div>


                    </div>


                </div>

                <!-- Utilities Payment Management Section -->
                <div class="row" id="utilities-section">
                    <div class="card" style="background: #d1fff1">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center my-4">
                                <h6 class="mb-0">Utilities Payment Management</h6>
                                <span class="badge text-bg-warning">{{$outstanding_utilities_count ?? 0}} Outstanding</span>
                            </div>

                            <!-- Estate Selector and Bulk Clear -->
                            <div class="row mb-4">
                                <div class="col-xl-6 col-sm-12">
                                    <form action="clear-utility-payment-by-estate" method="post" id="clearByEstateForm">
                                        @csrf
                                        <label class="my-2">Select Estate to Clear All Outstanding Payments</label>
                                        <div class="d-flex gap-2">
                                            <select name="estate_id" class="form-control" required>
                                                <option value="">-- Select Estate --</option>
                                                @foreach($estates as $estate)
                                                <option value="{{$estate->id}}">{{$estate->title}}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirmClearByEstate();">
                                                Clear All
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Only clears unpaid/outstanding payments</small>
                                    </form>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Filter Section -->
                            <div class="row mb-3">
                                <div class="col-xl-4 col-sm-12">
                                    <label class="my-2">Filter by Estate</label>
                                    <select id="estateFilter" class="form-control">
                                        <option value="">All Estates</option>
                                        @foreach($estates as $estate)
                                        <option value="{{$estate->title}}">{{$estate->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-xl-4 col-sm-12">
                                    <label class="my-2">Filter by Status</label>
                                    <select id="statusFilter" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Partial">Partial</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Utilities Payments Table -->
                            <div class="table-responsive">
                                <table class="table table-striped utilities-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Estate</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Total Amount</th>
                                            <th>Duration</th>
                                            <th>Next Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($utilities_payments as $payment)
                                        <tr>
                                            <td>{{$payment->id}}</td>
                                            <td>
                                                @if($payment->user)
                                                <a href="view-user?id={{$payment->user_id}}">
                                                    {{$payment->user->first_name ?? 'N/A'}} {{$payment->user->last_name ?? ''}}
                                                </a>
                                                @else
                                                N/A
                                                @endif
                                            </td>
                                            <td>{{$payment->estate->title ?? 'N/A'}}</td>
                                            <td>{{$payment->type ?? 'N/A'}}</td>
                                            <td>
                                                <span class="badge text-bg-primary">
                                                    NGN {{number_format($payment->amount, 2)}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-info">
                                                    NGN {{number_format($payment->total_amount, 2)}}
                                                </span>
                                            </td>
                                            <td>{{$payment->duration ?? 'N/A'}}</td>
                                            <td>{{$payment->next_due_date ?? 'N/A'}}</td>
                                            <td>
                                                @if($payment->status == 0)
                                                    <span class="badge text-bg-danger">Unpaid</span>
                                                @elseif($payment->status == 1)
                                                    <span class="badge text-bg-warning">Partial</span>
                                                @elseif($payment->status == 2)
                                                    <span class="badge text-bg-success">Paid</span>
                                                @else
                                                    <span class="badge text-bg-secondary">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->status != 2)
                                                <form action="clear-single-utility-payment" method="post" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="payment_id" value="{{$payment->id}}">
                                                    <button type="submit" class="btn btn-warning btn-sm"
                                                            onclick="return confirmClearSingle('{{$payment->estate->title ?? 'Unknown'}}', '{{number_format($payment->amount, 2)}}');">
                                                        Clear
                                                    </button>
                                                </form>
                                                @else
                                                <span class="text-muted small">Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function confirmClearByEstate() {
                    var estateSelect = document.querySelector('#clearByEstateForm select[name="estate_id"]');
                    var selectedOption = estateSelect.options[estateSelect.selectedIndex];

                    if (!estateSelect.value) {
                        alert('Please select an estate first');
                        return false;
                    }

                    var estateName = selectedOption.text;
                    return confirm(
                        'Are you sure you want to clear ALL outstanding utilities payments for ' + estateName + '?\n\n' +
                        'This will set both amount and total_amount to zero for all unpaid/outstanding payments.\n\n' +
                        'This action cannot be undone.'
                    );
                }

                function confirmClearSingle(estateName, amount) {
                    return confirm(
                        'Are you sure you want to clear this utilities payment?\n\n' +
                        'Estate: ' + estateName + '\n' +
                        'Amount to zero: NGN ' + amount + '\n\n' +
                        'This action cannot be undone.'
                    );
                }

                // Initialize DataTables for utilities
                $(document).ready(function() {
                    var table = $('.utilities-table').DataTable({
                        pageLength: 20,
                        order: [[0, 'desc']], // Sort by ID descending
                        language: {
                            search: "Search utilities payments:"
                        }
                    });

                    // Estate filter
                    $('#estateFilter').on('change', function() {
                        var estateValue = this.value;
                        table.column(2).search(estateValue).draw(); // Column 2 is Estate
                    });

                    // Status filter
                    $('#statusFilter').on('change', function() {
                        var statusValue = this.value;
                        table.column(8).search(statusValue).draw(); // Column 8 is Status
                    });
                });
                </script>

                <div class="row">
                    <div class="card" style="background: #fff3cd">
                        <div class="card-body">
                            <form action="update-passsword-now" method="post">
                                @csrf

                                <h6 class="d-flex justify-content-start my-4">Change Password</h6>

                                <div class="row">
                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required minlength="6">
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                    </div>
                                </div>

                                <button type="submit" class="col-xl-2 col-sm-12 my-2 d-flex btn btn-primary">
                                    Update Password
                                </button>

                            </form>
                        </div>
                    </div>
                </div>


            </div>


        </div>
    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Estate Settings</h4>
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

                    <div class="card">

                        <div class="card-body">


                            <div class="row">

                                <div class="card">

                                    <div class="card-body">

                                        <form action="estate-update-info" method="post">
                                            @csrf

                                            <div class="row">

                                                <h6 class="d-flex justify-content-start my-4">Estate Information</h6>

                                                <div class="col-xl-4 col-sm-12">
                                                    <label class="my-2">Estate Name</label>
                                                    <input type="text" readonly name="title"
                                                           value="{{$org->title ?? "name"}}"
                                                           class="form-control"
                                                           required>
                                                    <input hidden name="id" value="{{$org->id ?? "id" }}"
                                                           class="form-control"
                                                           required>

                                                </div>

                                                <div class="col-xl-4 col-sm-12">
                                                    <label class="my-2">State</label>
                                                    <input type="text" name="state" value="{{$org->state ?? "state"}}"
                                                           class="form-control"
                                                           required>

                                                </div>

                                                <div class="col-xl-4 col-sm-12">
                                                    <label class="my-2">City</label>
                                                    <input type="text" name="city" value="{{$org->city ?? "city"}}"
                                                           class="form-control"
                                                           required>

                                                </div>

                                                <div class="col-xl-4 col-sm-12">
                                                    <label class="my-2">LGA</label>
                                                    <input type="text" name="lga" value="{{$org->lga ?? "lga"}}"
                                                           class="form-control"
                                                           required>

                                                </div>

                                                <div class="col-xl-3 col-sm-12">
                                                    <label class="my-2">Status</label>
                                                    @if($org->status == 2)
                                                        <input type="text" readonly value="Active" class="form-control"
                                                               required>
                                                    @else
                                                        <input type="text" readonly value="Inactive"
                                                               class="form-control" required>
                                                    @endif


                                                </div>


                                                <div class="col-xl-3 col-sm-12">
                                                    <label class="my-2">Payment Type</label>
                                                    <select type="text" disabled name="ptype" class="form-control"
                                                            required>
                                                        @if($org->ptype == null)
                                                            <option value="">--select type---</option>
                                                            {{--                                                            <option value="1">APP Only</option>--}}
                                                            {{--                                                            <option value="2">Web Only</option>--}}
                                                            {{--                                                            <option value="3">App & Web</option>--}}
                                                        @elseif($org->ptype == 1)
                                                            <option value="1">APP Only</option>
                                                            {{--                                                        <option value="2">Web Only</option>--}}
                                                            {{--                                                        <option value="3">App & Web</option>--}}
                                                        @elseif($org->ptype == 2)
                                                            <option value="2">Web Only</option>
                                                            {{--                                                        <option value="1">App Only</option>--}}
                                                            {{--                                                        <option value="3">App & Web</option>--}}
                                                        @elseif($org->ptype == 3)
                                                            <option value="3">App & Web</option>
                                                            {{--                                                        <option value="1">App Only</option>--}}
                                                            {{--                                                        <option value="2">Web Only</option>--}}
                                                        @endif

                                                    </select>

                                                </div>

                                            </div>

{{--                                            <hr class="col-xl-4 col-sm-12">--}}

{{--                                            <button type="submit" class=" d-flex btn btn-primary">--}}
{{--                                                Update Info--}}
{{--                                            </button>--}}


                                        </form>

                                    </div>


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
                                                <label class="my-2">MAX Vend</label>
                                                <input type="number" step="0.01" name="max_pur"
                                                       value="{{$org->max_pur ?? 1000000}}"
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

                                            @if($utl != null)
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

                                                    <option value="per_transaction"
                                                        {{ old('duration', $org->duration ?? '') === 'per_transaction' ? 'selected' : '' }}>
                                                        Per Transaction
                                                    </option>

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
                                                    <div class="col-xl-4 col-sm-12 my-1">
                                                        <label class="my-1">Utility</label>
                                                        <input type="text" name="title" id="title_{{$index}}"
                                                               class="form-control" value="{{$data->title ?? "name"}}"
                                                               required>
                                                    </div>

                                                    <div class="col-xl-4 col-sm-12 my-1">
                                                        <label class="my-1">Amount</label>
                                                        <input type="text" name="amount" id="amount_{{$index}}"
                                                               class="form-control" value="{{$data->amount}}"
                                                               required>
                                                    </div>

                                                    <div class="col-xl-4 col-sm-12 my-4">
                                                        <button type="submit" class="btn btn-primary">Update
                                                        </button>
                                                        <a href="delete-utility?id={{$data->id}}"
                                                           class="btn btn-danger"
                                                           onsubmit="return confirmDelete();"> Delete</a>

                                                    </div>
                                                    <script>

                                                        function confirmDelete() {
                                                            return confirm('Are you sure you want to delete this item?');
                                                        }
                                                    </script>
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
                                                <div id="template" class="d-none">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <label class="my-2">Title</label>
                                                            <input type="text" name="title[]"
                                                                   class="form-control"
                                                                   required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" name="amount[]"
                                                                   class="form-control"
                                                                   required>
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
                                            let utilities = [];

                                            document.getElementById('add-more').addEventListener('click', function () {
                                                var template = document.getElementById('template').cloneNode(true);
                                                template.classList.remove('d-none');
                                                template.removeAttribute('id');

                                                document.getElementById('utility-fields').appendChild(template);
                                            });


                                            document.getElementById('save').addEventListener('click', function () {
                                                let titles = document.querySelectorAll('input[name="title[]"]');
                                                let amounts = document.querySelectorAll('input[name="amount[]"]');

                                                utilities = [];

                                                for (let i = 0; i < titles.length; i++) {
                                                    utilities.push({
                                                        title: titles[i].value,
                                                        amount: amounts[i].value
                                                    });
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


                        </div>


                    </div>


                </div>

                <div class="row">
                    <div class="card" style="background: #fff3cd">
                        <div class="card-body">
                            <form action="update-passsword-now" method="post">
                                @csrf

                                <h6 class="d-flex justify-content-start my-4">Change Password</h6>

                                <div class="row">
                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">New Password</label>
                                        <input type="password" name="new_password" class="form-control" required minlength="6">
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Confirm New Password</label>
                                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                    </div>
                                </div>

                                <button type="submit" class="col-xl-2 col-sm-12 my-2 d-flex btn btn-primary">
                                    Update Password
                                </button>

                            </form>
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
