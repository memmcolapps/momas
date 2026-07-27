@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)

        <div class="content">

            <!-- Start Content-->
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


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">{{$user->first_name }} {{$user->last_name }}</h4>
                    </div>
                </div>


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
                                                id="staticBackdropLabel">Update Email</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <form action="update_user_email" method="POST">
                                            @csrf

                                            <div class="modal-body">

                                                <p>Update Email</p>
                                                <label class="mt-3">Old Email</label>
                                                <input name="old_email" class="form-control" readonly value="{{$user->email}}">

                                                <input name="user_id" class="form-control" readonly value="{{$user->id}}" hidden>

                                                <label class="mt-3">New Email</label>
                                                <input type="email" name="email" class="form-control" required >


                                                <label class="mt-3">Confirm Email</label>
                                                <input type="email" name="confirm_email" class="form-control" required>


                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Update Email
                                                </button>
                                            </div>

                                        </form>


                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card -->
                    </div> <!-- end col -->



                    <div class="row">

                    <div class="card">

                        <div class="card-body">
                            <div class="row">
                                <form action="update-user" method="post">
                                    @csrf

                                    <div class="row">

                                        <div class="card-header">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="d-flex justify-content-start my-4">Customer Information</h6>
                                                <div class="justify-content-end">
                                                    <div class="justify-content-end">
                                                        <a href="#" class="btn btn-primary text-white " data-bs-toggle="modal"
                                                           data-bs-target="#staticBackdrop">Update Email</a>
                                                    </div>
                                                </div>

                                            </div>


                                        </div>




                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">First Name</label>
                                            <input type="text" value="{{$user->first_name}}" name="first_name" class="form-control" required>

                                            <input type="text" value="{{$user->email}}" name="email"  hidden>


                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Last Name</label>
                                            <input type="text" value="{{$user->last_name}}" name="last_name"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Email</label>
                                            <input type="email" value="{{$user->email}}" name="email" class="form-control" readonly>

                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Phone</label>
                                            <input type="text" value="{{$user->phone}}" name="phone" class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Estate</label>
                                            <input type="text" value="{{$estate_title}}" name="estate_title"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Address</label>
                                            <input type="text" value="{{$user->address}}" name="address"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">City</label>
                                            <input type="text" value="{{$user->city}}" name="city" class="form-control"
                                                   >
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">State</label>
                                            <select type="text" name="state" class="form-control" required>
                                                @if($user->state == null)
                                                    <option disabled selected>--Select State--</option>
                                                @else
                                                    <option value="{{$user->state}}">{{$user->state}}</option>
                                                @endif
                                                <option value="Abia">Abia</option>
                                                <option value="Adamawa">Adamawa</option>
                                                <option value="Akwa Ibom">Akwa Ibom</option>
                                                <option value="Anambra">Anambra</option>
                                                <option value="Bauchi">Bauchi</option>
                                                <option value="Bayelsa">Bayelsa</option>
                                                <option value="Benue">Benue</option>
                                                <option value="Borno">Borno</option>
                                                <option value="Cross River">Cross River</option>
                                                <option value="Delta">Delta</option>
                                                <option value="Ebonyi">Ebonyi</option>
                                                <option value="Edo">Edo</option>
                                                <option value="Ekiti">Ekiti</option>
                                                <option value="Enugu">Enugu</option>
                                                <option value="FCT">Federal Capital Territory</option>
                                                <option value="Gombe">Gombe</option>
                                                <option value="Imo">Imo</option>
                                                <option value="Jigawa">Jigawa</option>
                                                <option value="Kaduna">Kaduna</option>
                                                <option value="Kano">Kano</option>
                                                <option value="Katsina">Katsina</option>
                                                <option value="Kebbi">Kebbi</option>
                                                <option value="Kogi">Kogi</option>
                                                <option value="Kwara">Kwara</option>
                                                <option value="Lagos">Lagos</option>
                                                <option value="Nasarawa">Nasarawa</option>
                                                <option value="Niger">Niger</option>
                                                <option value="Ogun">Ogun</option>
                                                <option value="Ondo">Ondo</option>
                                                <option value="Osun">Osun</option>
                                                <option value="Oyo">Oyo</option>
                                                <option value="Plateau">Plateau</option>
                                                <option value="Rivers">Rivers</option>
                                                <option value="Sokoto">Sokoto</option>
                                                <option value="Taraba">Taraba</option>
                                                <option value="Yobe">Yobe</option>
                                                <option value="Zamfara">Zamfara</option>
                                            </select>

                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">LGA</label>
                                            <input type="text" value="{{$user->lga}}" name="lga" class="form-control"
                                                   >
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Designation</label>
                                            <input type="text" value="{{$user->desgination}}" name="desgination"
                                                   class="form-control"
                                            >
                                        </div>


                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Status</label>
                                            <select type="text" name="status" class="form-control" required>
                                                @if($user->status == 2)
                                                    <option value="{{$user->status}}">Active</option>
                                                @elseif($user->status == 0)
                                                    <option value="{{$user->status}}">Pending</option>
                                                @elseif($user->status == 3)
                                                    <option value="{{$user->status}}"><span
                                                            style="color: red">Inactive</span></option>
                                                @endif
                                                <option value="3">Deactivate</option>
                                                <option value="1">Activate</option>

                                            </select>
                                        </div>


                                    </div>

                                    <button type="submit" class="col-xl-3 col-sm-12 d-flex btn btn-primary my-3">
                                        Update Customer Information
                                    </button>

                                </form>
                            </div>
                        </div>
                    </div>


                    @if($user->role == 2)
                        <div class="row ">

                            <div class="card col-xl-6 mr-3 col-sm-12 card mr-3">
                                <div class="card-body">
                                    <div class="row">
                                        <form action="update-meter" method="post">
                                            @csrf
                                            <div class="row">

                                                <h6 class="d-flex justify-content-start my-2">Attach Information</h6>


                                                <div class="col-xl-5 col-sm-12" style="position: relative;">


                                                    <label class="my-2">Choose Meter</label>
                                                    @if($meter_count == 0)
                                                        <span
                                                            class="badge text-bg-danger">No meter found</span>
                                                    @else
                                                        <span class="badge text-bg-danger">{{$meterNo}}</span>
                                                    @endif


                                                    <input type="text" name="meterNo"
                                                           value="{{$meterNo }}" id="searchMeter"
                                                           placeholder="Type meter number..." class="form-control" required
                                                           autocomplete="off">
                                                    <div id="meterResult" class="search-result"></div>

                                                    <input type="text" name="user_id" value="{{$user->id}}" hidden>

                                                    <input type="text" name="old_value" value="{{$user->meterNo}}" hidden>


                                                    <script>
                                                        document.getElementById('searchMeter').addEventListener('keyup', function () {
                                                            let query = this.value;
                                                            console.log('User input:', query); // Log user input

                                                            if (query.length > 2) { // Only search if input has more than 2 characters
                                                                let xhr = new XMLHttpRequest();
                                                                xhr.open('GET', '/search-meter?q=' + query, true); // Replace with the correct URL

                                                                xhr.onreadystatechange = function () {
                                                                    if (xhr.readyState == 4 && xhr.status == 200) {
                                                                        console.log('Response received:', xhr.responseText); // Log the response

                                                                        let meters = JSON.parse(xhr.responseText);
                                                                        let meterResultDiv = document.getElementById('meterResult');
                                                                        meterResultDiv.innerHTML = ''; // Clear previous results

                                                                        if (meters.length > 0) {
                                                                            meters.forEach(meter => {
                                                                                let div = document.createElement('div');
                                                                                div.textContent = meter.meterNo;
                                                                                div.setAttribute('data-id', meter.id);

                                                                                // Add click event to populate the input with the selected suggestion
                                                                                div.addEventListener('click', function () {
                                                                                    document.getElementById('searchMeter').value = meter.meterNo;
                                                                                    meterResultDiv.style.display = 'none'; // Hide suggestions
                                                                                });

                                                                                meterResultDiv.appendChild(div);
                                                                            });
                                                                            meterResultDiv.style.display = 'block'; // Show results
                                                                        } else {
                                                                            let noResultDiv = document.createElement('div');
                                                                            noResultDiv.textContent = 'No meters found';
                                                                            noResultDiv.style.color = 'red';
                                                                            meterResultDiv.appendChild(noResultDiv);
                                                                            meterResultDiv.style.display = 'block'; // Show the "No meters found" message
                                                                        }
                                                                    } else if (xhr.readyState == 4) {
                                                                        console.log('Error: Status', xhr.status); // Log error status
                                                                    }
                                                                };

                                                                xhr.onerror = function () {
                                                                    console.error('Request error'); // Log any request errors
                                                                };

                                                                xhr.send();
                                                            } else {
                                                                document.getElementById('meterResult').style.display = 'none'; // Hide if input is too short
                                                            }
                                                        });
                                                    </script>


                                                </div>

                                                <div class="col-xl-6 col-sm-12">

                                                </div>



                                                @if($meterNo == null)

                                                    <div class="col-xl-4 col-sm-12">
                                                        <button type="submit" class="col-12 d-flex w-100 btn btn-primary my-3">
                                                            Attach Meter
                                                        </button>
                                                    </div>

                                                @else
                                                    <a href="detach-meter?meterNo={{$meterNo}}" class="col-4 d-flex  btn btn-danger my-3">
                                                        Detach Meter
                                                    </a>
                                                @endif


                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                            <div class="card col-xl-6 mr-3 col-sm-12 card">
                                <div class="card-body">
                                    <div class="row">
                                        <form action="set-percentage" method="post">
                                            @csrf
                                            <div class="row">

                                                <h6 class="d-flex justify-content-start my-2">Set Utilities percentage</h6>


                                                <div class="col-xl-4 col-sm-12" style="position: relative;">

                                                    <label class="my-2">Set percentage %</label>
                                                    <input type="number" step="0.01" name="percent" class="form-control"
                                                           value="{{$percentage}}" disabled>
                                                    <input type="text" name="user_id" value="{{$user->id}}" hidden>
                                                    <input type="text" name="estate_id" value="{{$user->estate_id}}" hidden>

                                                </div>

                                                <div class="col-xl-6 col-sm-12">

                                                </div>

                                                <div class="col-xl-3 col-sm-12">
                                                    <button type="submit" class="col-12 d-flex w-100 btn btn-primary my-3" disabled>
                                                        Update
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="card my-4">
                            <div class="card-body">
                                <h6 class="d-flex justify-content-start my-2">Create Customer Utility</h6>
                                <form action="customer-store-utility" method="post" class="row">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <input type="hidden" name="estate_id" value="{{$user->estate_id}}">

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Type</label>
                                        <select name="type" id="utility-type" class="form-control" onchange="toggleCustomerUtilityType(this)" required>
                                            <option value="service_charge">Service Charge</option>
                                            <option value="debt">Debt</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Title</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 sc-field">
                                        <label class="my-1">Duration</label>
                                        <select name="duration" class="form-control">
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly" selected>Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 debt-field" style="display: none;">
                                        <label class="my-1">Start Date</label>
                                        <input type="date" name="start_date" class="form-control">
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 debt-field" style="display: none;">
                                        <label class="my-1">Mode of Payment</label>
                                        <select name="mode_of_payment" class="form-control mode-of-payment" onchange="togglePaymentFields(this)">
                                            <option value="">Select</option>
                                            <option value="monthly_payment">Monthly Payment</option>
                                            <option value="one_off">One-Off</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 months-field debt-field" style="display: none;">
                                        <label class="my-1">Number of Months</label>
                                        <input type="number" name="payment_months" min="1" max="60" class="form-control">
                                    </div>

                                    <div class="col-12 my-3">
                                        <button type="submit" class="btn btn-primary">Save Utility</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <script>
                            function toggleCustomerUtilityType(select) {
                                var form = select.closest('form');
                                if (!form) return;
                                var scFields = form.querySelectorAll('.sc-field');
                                var debtFields = form.querySelectorAll('.debt-field');

                                if (select.value === 'service_charge') {
                                    scFields.forEach(f => f.style.display = '');
                                    debtFields.forEach(f => f.style.display = 'none');
                                } else {
                                    scFields.forEach(f => f.style.display = 'none');
                                    debtFields.forEach(f => f.style.display = '');
                                }
                            }

                            function togglePaymentFields(select) {
                                var container = select.closest('.row') || select.closest('form');
                                if (!container) return;
                                var monthsField = container.querySelector('.months-field');

                                if (monthsField) monthsField.style.display = 'none';

                                if (select.value === 'monthly_payment' && monthsField) {
                                    monthsField.style.display = 'block';
                                }
                            }

                            document.addEventListener('DOMContentLoaded', function () {
                                var typeSelect = document.getElementById('utility-type');
                                if (typeSelect) toggleCustomerUtilityType(typeSelect);
                            });
                        </script>











                        {{--                    <div class="col-xl-12">--}}

                        {{--                        <div class="row">--}}
                        {{--                            <div class="col-xl-6 mr-3 col-sm-12 card">--}}
                        {{--                                <div class="card-body">--}}
                        {{--                                    <div class="row">--}}
                        {{--                                        <form action="update-nepa" method="post">--}}
                        {{--                                            @csrf--}}
                        {{--                                            <div class="row">--}}

                        {{--                                                <h6 class="d-flex justify-content-start my-2">Tariff--}}
                        {{--                                                    Information</h6>--}}


                        {{--                                                <div class="col-xl-6 col-sm-12"--}}
                        {{--                                                     style="position: relative;">--}}

                        {{--                                                    <h6 class="mb-4">NEPA TARIFF <span--}}
                        {{--                                                            class="badge text-bg-secondary">{{$nepa_tariff_title   ?? "Set Tariff"}} | ID - {{$tariff_index_nepa ?? " "}}</span>--}}
                        {{--                                                    </h6>--}}

                        {{--                                                    <select class="form-control my-3" name="id">--}}

                        {{--                                                        @foreach($tariff as $data)--}}
                        {{--                                                            <option--}}
                        {{--                                                                value="{{$data->id}}">{{$data->title}}--}}
                        {{--                                                                |--}}
                        {{--                                                                ID - {{$data->tariff_index}}</option>--}}
                        {{--                                                        @endforeach--}}

                        {{--                                                        <input name="user_id" hidden--}}
                        {{--                                                               value="{{$user->id}}">--}}


                        {{--                                                    </select>--}}


                        {{--                                                    @if($tariff_count_nepa == 0)--}}

                        {{--                                                        <button type="submit"--}}
                        {{--                                                                class="col-12 d-flex w-100 btn btn-primary my-3">--}}
                        {{--                                                            Attach Tariff--}}
                        {{--                                                        </button>--}}

                        {{--                                                    @else--}}

                        {{--                                                        <a href="detach-nepa-tariff?id={{$tariff_id_nepa}}" class="col-12 d-flex w-100 btn btn-danger my-3">--}}
                        {{--                                                            Detach  Tariff--}}
                        {{--                                                        </a>--}}



                        {{--                                                    @endif--}}




                        {{--                                                </div>--}}


                        {{--                                            </div>--}}

                        {{--                                        </form>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-xl-6 col-sm-12 card">--}}
                        {{--                                <div class="card-body">--}}
                        {{--                                    <div class="row">--}}
                        {{--                                        <form action="update-gen" method="post">--}}
                        {{--                                            @csrf--}}
                        {{--                                            <div class="row">--}}

                        {{--                                                <h6 class="d-flex justify-content-start my-2">Tariff--}}
                        {{--                                                    Information</h6>--}}


                        {{--                                                <div class="col-xl-6 col-sm-12"--}}
                        {{--                                                     style="position: relative;">--}}

                        {{--                                                    <h6 class="mb-4">GENERATOR TARIFF <span--}}
                        {{--                                                            class="badge text-bg-secondary">{{$gen_tariff_title   ?? "Set Tariff"}} | ID - {{$tariff_index_gen ?? " "}}</span>--}}
                        {{--                                                    </h6>--}}

                        {{--                                                    <select class="form-control my-3" name="tariff">--}}

                        {{--                                                        @foreach($tariff as $data)--}}
                        {{--                                                            <option--}}
                        {{--                                                                value="{{$data->id}}">{{$data->title}}--}}
                        {{--                                                                |--}}
                        {{--                                                                ID - {{$data->tariff_index}}</option>--}}
                        {{--                                                        @endforeach--}}


                        {{--                                                    </select>--}}


                        {{--                                                    <input name="user_id" hidden--}}
                        {{--                                                           value="{{$user->id}}">--}}


                        {{--                                                    @if($tariff_count_gen == 0)--}}

                        {{--                                                        <button type="submit"--}}
                        {{--                                                                class="col-12 d-flex w-100 btn btn-primary my-3">--}}
                        {{--                                                            Attach Tariff--}}
                        {{--                                                        </button>--}}

                        {{--                                                    @else--}}

                        {{--                                                        <a href="detach-gen-tariff?id={{$tariff_id_gen}}" class="col-12 d-flex w-100 btn btn-danger my-3">--}}
                        {{--                                                            Detach  Tariff--}}
                        {{--                                                        </a>--}}



                        {{--                                                    @endif--}}

                        {{--                                                </div>--}}


                        {{--                                            </div>--}}

                        {{--                                        </form>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        {{--                    </div> <!-- end col -->--}}



                        {{-- Service Charges Table --}}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">Service Charges</h5>
                                            <span class="badge text-bg-info">{{ $customer_service_charges->count() }} Total</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">ID</th>
                                                <th scope="col" class="cursor-pointer">Title</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer">Duration</th>
                                                <th scope="col" class="cursor-pointer">Activated</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($customer_service_charges as $sc)
                                                <tr>
                                                    <td>{{ $sc->id }}</td>
                                                    <td>{{ $sc->title }}</td>
                                                    <td>NGN {{ number_format($sc->amount, 2) }}</td>
                                                    <td>{{ strtoupper($sc->duration ?? 'N/A') }}</td>
                                                    <td>
                                                        @if($sc->activated)
                                                            <span class="badge text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sc->status == 1)
                                                            <span class="badge text-bg-success">Active</span>
                                                        @elseif($sc->status == 0)
                                                            <span class="badge text-bg-warning">Inactive</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $sc->created_at }}</td>
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

                        {{-- Debt Type Utilities Table --}}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">Debt Type Utilities</h5>
                                            <span class="badge text-bg-warning">{{ $customer_debt_utilities->count() }} Total</span>
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
                                                <th scope="col" class="cursor-pointer">% Payment</th>
                                                <th scope="col" class="cursor-pointer">Payment Months</th>
                                                <th scope="col" class="cursor-pointer">Monthly End Date</th>
                                                <th scope="col" class="cursor-pointer">Start Date</th>
                                                <th scope="col" class="cursor-pointer">Activated</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($customer_debt_utilities as $debt)
                                                <tr>
                                                    <td>{{ $debt->id }}</td>
                                                    <td>{{ $debt->title }}</td>
                                                    <td>NGN {{ number_format($debt->amount, 2) }}</td>
                                                    <td>{{ str_replace('_', ' ', ucwords($debt->mode_of_payment ?? 'N/A')) }}</td>
                                                    <td>{{ $debt->percent_payment ? $debt->percent_payment . '%' : '-' }}</td>
                                                    <td>{{ $debt->payment_months ?? '-' }}</td>
                                                    <td>{{ $debt->monthly_end_date ? \Carbon\Carbon::parse($debt->monthly_end_date)->format('Y-m-d') : '-' }}</td>
                                                    <td>{{ $debt->start_date ? \Carbon\Carbon::parse($debt->start_date)->format('Y-m-d') : '-' }}</td>
                                                    <td>
                                                        @if($debt->activated)
                                                            <span class="badge text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($debt->status == 1)
                                                            <span class="badge text-bg-success">Active</span>
                                                        @elseif($debt->status == 0)
                                                            <span class="badge text-bg-warning">Inactive</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $debt->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">No debt type utilities found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">

                            <div class="card-body">

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-2">Utility Information</h6>

                                    <a href="/export-utilities?user_id={{$user->id}}&estate_id={{$user->estate_id}}" style="width: 100px" class="btn btn-success">Export</a>

                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">ID</th>
                                                <th scope="col" class="cursor-pointer">Estate</th>
                                                <th scope="col" class="cursor-pointer">Duration</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Date/Time</th>
                                                <th scope="col" class="cursor-pointer">Action</th>


                                            </tr>
                                            </thead>
                                            <tbody>


                                            @foreach($upayment as $data)

                                                <tr>
                                                    <td> {{$data->id}} </td>
                                                    <td>{{$estate_name}}</td>
                                                    <td>{{strtoupper($data->duration)}}</td>
                                                    <td>{{number_format($data->amount, 2)}}</td>
                                                    <td>
                                                        @if($data->status == 0)
                                                            <span class="badge text-bg-warning">Not Paid</span>
                                                        @else
                                                            <span class="badge text-bg-success">Paid</span>
                                                        @endif

                                                    </td>
                                                    <td>{{$data->created_at}}</td>

                                                    <td>
                                                        @if($data->status == 0)
                                                            <a href="pay-utility?id={{$data->id}}&customer_id={{$user->id}}&estate_id={{$user->estate_id}}" class="badge text-bg-primary">Pay Utility</a>
                                                        @else
                                                            <a href="unpay-utility?id={{$data->id}}&customer_id={{$user->id}}&estate_id={{$user->estate_id}}" class="badge text-bg-danger">Unpay Utility</a>
                                                        @endif

                                                    </td>

                                                </tr>

                                            @endforeach


                                            </tbody><!-- end tbody -->

                                            <tfoot>

                                            {{ $upayment->links() }}


                                            </tfoot>
                                        </table><!-- end table -->
                                    </div>
                                </div>


                            </div>

                        </div>

                        {{-- Utility Payment Records --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="d-flex justify-content-start my-2">Utility Payment Records</h6>

                                    <form method="GET" action="{{ url()->current() }}">
                                        <input type="hidden" name="id" value="{{ $user->id }}">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label mb-1">Utility</label>
                                                <input type="text" name="utility_title" class="form-control" placeholder="Utility title" value="{{ request('utility_title') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">From</label>
                                                <input type="date" name="payment_date_from" class="form-control" value="{{ request('payment_date_from') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">To</label>
                                                <input type="date" name="payment_date_to" class="form-control" value="{{ request('payment_date_to') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">Transaction ID</label>
                                                <input type="text" name="trx_id" class="form-control" placeholder="Transaction number" value="{{ request('trx_id') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="">All Status</option>
                                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Not Paid</option>
                                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Payment in Progress</option>
                                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Paid</option>
                                                </select>
                                            </div>
                                            <div class="col-auto d-flex align-items-end">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                    <a href="{{ url()->current() }}?id={{ $user->id }}" class="btn btn-secondary">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Utility</th>
                                                <th>Utility Amount</th>
                                                <th>Amount Paid</th>
                                                <th>Transaction ID</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($utility_payment_records as $record)
                                                <tr>
                                                    <td>{{ $record->id }}</td>
                                                    <td>{{ $record->utility?->title ?? 'N/A' }}</td>
                                                    <td>{{ number_format($record->utility_amount, 2) }}</td>
                                                    <td>{{ number_format($record->amount_paid, 2) }}</td>
                                                    <td>{{ $record->trx_id }}</td>
                                                    <td>
                                                        @if($record->status == 0)
                                                            <span class="badge text-bg-warning">Not Paid</span>
                                                        @elseif($record->status == 1)
                                                            <span class="badge text-bg-info">Payment in Progress</span>
                                                        @else
                                                            <span class="badge text-bg-success">Paid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $record->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No payment records found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                            <tfoot>
                                            {{ $utility_payment_records->links() }}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                    @endif


                </div>
            </div>
        </div>


    @else

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


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">{{$user->first_name }} {{$user->last_name }}</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">
                            <div class="row">
                                <form action="update-user" method="post">
                                    @csrf

                                    <div class="row">

                                        <h6 class="d-flex justify-content-start my-4">Customer Information</h6>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">First Name</label>
                                            <input type="text" value="{{$user->first_name}}" name="first_name" class="form-control" required>

                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Last Name</label>
                                            <input type="text" value="{{$user->last_name}}" name="last_name"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Email</label>
                                            <input type="email" value="{{$user->email}}" name="email" class="form-control" required>

                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Phone</label>
                                            <input type="number" value="{{$user->phone}}" name="phone" class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Estate</label>
                                            <input type="text" value="{{$estate_title}}" name="estate_title"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Address</label>
                                            <input type="text" value="{{$user->address}}" name="address"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">City</label>
                                            <input type="text" value="{{$user->city}}" name="city" class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">State</label>
                                            <select type="text" name="state" class="form-control" required>
                                                @if($user->state == null)
                                                    <option disabled selected>--Select State--</option>
                                                @else
                                                    <option value="{{$user->state}}">{{$user->state}}</option>
                                                @endif
                                                <option value="Abia">Abia</option>
                                                <option value="Adamawa">Adamawa</option>
                                                <option value="Akwa Ibom">Akwa Ibom</option>
                                                <option value="Anambra">Anambra</option>
                                                <option value="Bauchi">Bauchi</option>
                                                <option value="Bayelsa">Bayelsa</option>
                                                <option value="Benue">Benue</option>
                                                <option value="Borno">Borno</option>
                                                <option value="Cross River">Cross River</option>
                                                <option value="Delta">Delta</option>
                                                <option value="Ebonyi">Ebonyi</option>
                                                <option value="Edo">Edo</option>
                                                <option value="Ekiti">Ekiti</option>
                                                <option value="Enugu">Enugu</option>
                                                <option value="FCT">Federal Capital Territory</option>
                                                <option value="Gombe">Gombe</option>
                                                <option value="Imo">Imo</option>
                                                <option value="Jigawa">Jigawa</option>
                                                <option value="Kaduna">Kaduna</option>
                                                <option value="Kano">Kano</option>
                                                <option value="Katsina">Katsina</option>
                                                <option value="Kebbi">Kebbi</option>
                                                <option value="Kogi">Kogi</option>
                                                <option value="Kwara">Kwara</option>
                                                <option value="Lagos">Lagos</option>
                                                <option value="Nasarawa">Nasarawa</option>
                                                <option value="Niger">Niger</option>
                                                <option value="Ogun">Ogun</option>
                                                <option value="Ondo">Ondo</option>
                                                <option value="Osun">Osun</option>
                                                <option value="Oyo">Oyo</option>
                                                <option value="Plateau">Plateau</option>
                                                <option value="Rivers">Rivers</option>
                                                <option value="Sokoto">Sokoto</option>
                                                <option value="Taraba">Taraba</option>
                                                <option value="Yobe">Yobe</option>
                                                <option value="Zamfara">Zamfara</option>
                                            </select>

                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">LGA</label>
                                            <input type="text" value="{{$user->lga}}" name="lga" class="form-control"
                                                   required>
                                        </div>

                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Designation</label>
                                            <input type="text" value="{{$user->desgination}}" name="desgination"
                                                   class="form-control"
                                            >
                                        </div>


                                        <div class="col-xl-3 col-sm-12">
                                            <label class="my-2">Status</label>
                                            <select type="text" name="status" class="form-control" required>
                                                @if($user->status == 2)
                                                    <option value="{{$user->status}}">Active</option>
                                                @elseif($user->status == 0)
                                                    <option value="{{$user->status}}">Pending</option>
                                                @elseif($user->status == 3)
                                                    <option value="{{$user->status}}"><span
                                                            style="color: red">Inactive</span></option>
                                                @endif
                                                <option value="3">Deactivate</option>
                                                <option value="1">Activate</option>

                                            </select>
                                        </div>


                                    </div>

                                    <button type="submit" class="col-xl-3 col-sm-12 d-flex btn btn-primary my-3">
                                        Update Customer Information
                                    </button>

                                </form>
                            </div>
                        </div>
                    </div>


                    @if($user->role == 2)
                        <div class="row ">

                            <div class="card col-xl-6 mr-3 col-sm-12 card mr-3">
                                <div class="card-body">
                                    <div class="row">
                                        <form action="update-meter" method="post">
                                            @csrf
                                            <div class="row">

                                                <h6 class="d-flex justify-content-start my-2">Attach Information</h6>


                                                <div class="col-xl-5 col-sm-12" style="position: relative;">


                                                    <label class="my-2">Choose Meter</label>
                                                    @if($meter_count == 0)
                                                        <span
                                                            class="badge text-bg-danger">No meter found</span>
                                                    @else
                                                        <span class="badge text-bg-danger">{{$meterNo}}</span>
                                                    @endif


                                                    <input type="text" name="meterNo"
                                                           value="{{$meterNo }}" id="searchMeter"
                                                           placeholder="Type meter number..." class="form-control" required
                                                           autocomplete="off">
                                                    <div id="meterResult" class="search-result"></div>

                                                    <input type="text" name="user_id" value="{{$user->id}}" hidden>

                                                    <input type="text" name="old_value" value="{{$user->meterNo}}" hidden>


                                                    <script>
                                                        document.getElementById('searchMeter').addEventListener('keyup', function () {
                                                            let query = this.value;
                                                            console.log('User input:', query); // Log user input

                                                            if (query.length > 2) { // Only search if input has more than 2 characters
                                                                let xhr = new XMLHttpRequest();
                                                                xhr.open('GET', '/search-meter?q=' + query, true); // Replace with the correct URL

                                                                xhr.onreadystatechange = function () {
                                                                    if (xhr.readyState == 4 && xhr.status == 200) {
                                                                        console.log('Response received:', xhr.responseText); // Log the response

                                                                        let meters = JSON.parse(xhr.responseText);
                                                                        let meterResultDiv = document.getElementById('meterResult');
                                                                        meterResultDiv.innerHTML = ''; // Clear previous results

                                                                        if (meters.length > 0) {
                                                                            meters.forEach(meter => {
                                                                                let div = document.createElement('div');
                                                                                div.textContent = meter.meterNo;
                                                                                div.setAttribute('data-id', meter.id);

                                                                                // Add click event to populate the input with the selected suggestion
                                                                                div.addEventListener('click', function () {
                                                                                    document.getElementById('searchMeter').value = meter.meterNo;
                                                                                    meterResultDiv.style.display = 'none'; // Hide suggestions
                                                                                });

                                                                                meterResultDiv.appendChild(div);
                                                                            });
                                                                            meterResultDiv.style.display = 'block'; // Show results
                                                                        } else {
                                                                            let noResultDiv = document.createElement('div');
                                                                            noResultDiv.textContent = 'No meters found';
                                                                            noResultDiv.style.color = 'red';
                                                                            meterResultDiv.appendChild(noResultDiv);
                                                                            meterResultDiv.style.display = 'block'; // Show the "No meters found" message
                                                                        }
                                                                    } else if (xhr.readyState == 4) {
                                                                        console.log('Error: Status', xhr.status); // Log error status
                                                                    }
                                                                };

                                                                xhr.onerror = function () {
                                                                    console.error('Request error'); // Log any request errors
                                                                };

                                                                xhr.send();
                                                            } else {
                                                                document.getElementById('meterResult').style.display = 'none'; // Hide if input is too short
                                                            }
                                                        });
                                                    </script>


                                                </div>

                                                <div class="col-xl-6 col-sm-12">

                                                </div>



                                                @if($meterNo == null)

                                                    <div class="col-xl-4 col-sm-12">
                                                        <button type="submit" class="col-12 d-flex w-100 btn btn-primary my-3">
                                                            Attach Meter
                                                        </button>
                                                    </div>

                                                @else
                                                    <a href="detach-meter?meterNo={{$meterNo}}" class="col-4 d-flex  btn btn-danger my-3">
                                                        Detach Meter
                                                    </a>
                                                @endif


                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                            <div class="card col-xl-6 mr-3 col-sm-12 card">
                                <div class="card-body">
                                    <div class="row">
                                        <form action="set-percentage" method="post">
                                            @csrf
                                            <div class="row">

                                                <h6 class="d-flex justify-content-start my-2">Set Utilities percentage</h6>


                                                <div class="col-xl-4 col-sm-12" style="position: relative;">

                                                    <label class="my-2">Set percentage %</label>
                                                    <input type="number" step="0.01" name="percent" class="form-control"
                                                           value="{{$percentage}}" disabled>
                                                    <input type="text" name="user_id" value="{{$user->id}}" hidden>
                                                    <input type="text" name="estate_id" value="{{$user->estate_id}}" hidden>

                                                </div>

                                                <div class="col-xl-6 col-sm-12">

                                                </div>

                                                <div class="col-xl-3 col-sm-12">
                                                    <button type="submit" class="col-12 d-flex w-100 btn btn-primary my-3" disabled>
                                                        Update
                                                    </button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="card my-4">
                            <div class="card-body">
                                <h6 class="d-flex justify-content-start my-2">Create Customer Utility</h6>
                                <form action="customer-store-utility" method="post" class="row">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <input type="hidden" name="estate_id" value="{{$user->estate_id}}">

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Title</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" required>
                                    </div>

                                    <input type="hidden" name="type" value="debt">

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1">
                                        <label class="my-1">Mode of Payment</label>
                                        <select name="mode_of_payment" class="form-control mode-of-payment" onchange="togglePaymentFields(this)" required>
                                            <option value="">Select</option>
                                            <!-- <option value="percentage_payment">Percentage Payment</option> -->
                                            <option value="monthly_payment">Monthly Payment</option>
                                            <option value="one_off">One-Off</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 percent-field" style="display: none;">
                                        <label class="my-1">% Payment</label>
                                        <select name="percent_payment" class="form-control" required>
                                            <option value="">Select</option>
                                            @for($pct = 5; $pct <= 70; $pct += 5)
                                                <option value="{{ $pct }}">{{ $pct }}%</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12 my-1 months-field" style="display: none;">
                                        <label class="my-1">Number of Months</label>
                                        <input type="number" name="payment_months" min="1" max="60" class="form-control" required>
                                    </div>

                                    <div class="col-12 my-3">
                                        <button type="submit" class="btn btn-primary">Save Utility</button>
                                    </div>
                                </form>
                            </div>
                        </div>










                        {{--                    <div class="col-xl-12">--}}

                        {{--                        <div class="row">--}}
                        {{--                            <div class="col-xl-6 mr-3 col-sm-12 card">--}}
                        {{--                                <div class="card-body">--}}
                        {{--                                    <div class="row">--}}
                        {{--                                        <form action="update-nepa" method="post">--}}
                        {{--                                            @csrf--}}
                        {{--                                            <div class="row">--}}

                        {{--                                                <h6 class="d-flex justify-content-start my-2">Tariff--}}
                        {{--                                                    Information</h6>--}}


                        {{--                                                <div class="col-xl-6 col-sm-12"--}}
                        {{--                                                     style="position: relative;">--}}

                        {{--                                                    <h6 class="mb-4">NEPA TARIFF <span--}}
                        {{--                                                            class="badge text-bg-secondary">{{$nepa_tariff_title   ?? "Set Tariff"}} | ID - {{$tariff_index_nepa ?? " "}}</span>--}}
                        {{--                                                    </h6>--}}

                        {{--                                                    <select class="form-control my-3" name="id">--}}

                        {{--                                                        @foreach($tariff as $data)--}}
                        {{--                                                            <option--}}
                        {{--                                                                value="{{$data->id}}">{{$data->title}}--}}
                        {{--                                                                |--}}
                        {{--                                                                ID - {{$data->tariff_index}}</option>--}}
                        {{--                                                        @endforeach--}}

                        {{--                                                        <input name="user_id" hidden--}}
                        {{--                                                               value="{{$user->id}}">--}}


                        {{--                                                    </select>--}}


                        {{--                                                    @if($tariff_count_nepa == 0)--}}

                        {{--                                                        <button type="submit"--}}
                        {{--                                                                class="col-12 d-flex w-100 btn btn-primary my-3">--}}
                        {{--                                                            Attach Tariff--}}
                        {{--                                                        </button>--}}

                        {{--                                                    @else--}}

                        {{--                                                        <a href="detach-nepa-tariff?id={{$tariff_id_nepa}}" class="col-12 d-flex w-100 btn btn-danger my-3">--}}
                        {{--                                                            Detach  Tariff--}}
                        {{--                                                        </a>--}}



                        {{--                                                    @endif--}}




                        {{--                                                </div>--}}


                        {{--                                            </div>--}}

                        {{--                                        </form>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                            <div class="col-xl-6 col-sm-12 card">--}}
                        {{--                                <div class="card-body">--}}
                        {{--                                    <div class="row">--}}
                        {{--                                        <form action="update-gen" method="post">--}}
                        {{--                                            @csrf--}}
                        {{--                                            <div class="row">--}}

                        {{--                                                <h6 class="d-flex justify-content-start my-2">Tariff--}}
                        {{--                                                    Information</h6>--}}


                        {{--                                                <div class="col-xl-6 col-sm-12"--}}
                        {{--                                                     style="position: relative;">--}}

                        {{--                                                    <h6 class="mb-4">GENERATOR TARIFF <span--}}
                        {{--                                                            class="badge text-bg-secondary">{{$gen_tariff_title   ?? "Set Tariff"}} | ID - {{$tariff_index_gen ?? " "}}</span>--}}
                        {{--                                                    </h6>--}}

                        {{--                                                    <select class="form-control my-3" name="tariff">--}}

                        {{--                                                        @foreach($tariff as $data)--}}
                        {{--                                                            <option--}}
                        {{--                                                                value="{{$data->id}}">{{$data->title}}--}}
                        {{--                                                                |--}}
                        {{--                                                                ID - {{$data->tariff_index}}</option>--}}
                        {{--                                                        @endforeach--}}


                        {{--                                                    </select>--}}


                        {{--                                                    <input name="user_id" hidden--}}
                        {{--                                                           value="{{$user->id}}">--}}


                        {{--                                                    @if($tariff_count_gen == 0)--}}

                        {{--                                                        <button type="submit"--}}
                        {{--                                                                class="col-12 d-flex w-100 btn btn-primary my-3">--}}
                        {{--                                                            Attach Tariff--}}
                        {{--                                                        </button>--}}

                        {{--                                                    @else--}}

                        {{--                                                        <a href="detach-gen-tariff?id={{$tariff_id_gen}}" class="col-12 d-flex w-100 btn btn-danger my-3">--}}
                        {{--                                                            Detach  Tariff--}}
                        {{--                                                        </a>--}}



                        {{--                                                    @endif--}}

                        {{--                                                </div>--}}


                        {{--                                            </div>--}}

                        {{--                                        </form>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        {{--                    </div> <!-- end col -->--}}



                        {{-- Service Charges Table --}}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">Service Charges</h5>
                                            <span class="badge text-bg-info">{{ $customer_service_charges->count() }} Total</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">ID</th>
                                                <th scope="col" class="cursor-pointer">Title</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer">Duration</th>
                                                <th scope="col" class="cursor-pointer">Activated</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($customer_service_charges as $sc)
                                                <tr>
                                                    <td>{{ $sc->id }}</td>
                                                    <td>{{ $sc->title }}</td>
                                                    <td>NGN {{ number_format($sc->amount, 2) }}</td>
                                                    <td>{{ strtoupper($sc->duration ?? 'N/A') }}</td>
                                                    <td>
                                                        @if($sc->activated)
                                                            <span class="badge text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sc->status == 1)
                                                            <span class="badge text-bg-success">Active</span>
                                                        @elseif($sc->status == 0)
                                                            <span class="badge text-bg-warning">Inactive</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $sc->created_at }}</td>
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

                        {{-- Debt Type Utilities Table --}}
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title text-black mb-0">Debt Type Utilities</h5>
                                            <span class="badge text-bg-warning">{{ $customer_debt_utilities->count() }} Total</span>
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
                                                <th scope="col" class="cursor-pointer">% Payment</th>
                                                <th scope="col" class="cursor-pointer">Payment Months</th>
                                                <th scope="col" class="cursor-pointer">Monthly End Date</th>
                                                <th scope="col" class="cursor-pointer">Start Date</th>
                                                <th scope="col" class="cursor-pointer">Activated</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($customer_debt_utilities as $debt)
                                                <tr>
                                                    <td>{{ $debt->id }}</td>
                                                    <td>{{ $debt->title }}</td>
                                                    <td>NGN {{ number_format($debt->amount, 2) }}</td>
                                                    <td>{{ str_replace('_', ' ', ucwords($debt->mode_of_payment ?? 'N/A')) }}</td>
                                                    <td>{{ $debt->percent_payment ? $debt->percent_payment . '%' : '-' }}</td>
                                                    <td>{{ $debt->payment_months ?? '-' }}</td>
                                                    <td>{{ $debt->monthly_end_date ? \Carbon\Carbon::parse($debt->monthly_end_date)->format('Y-m-d') : '-' }}</td>
                                                    <td>{{ $debt->start_date ? \Carbon\Carbon::parse($debt->start_date)->format('Y-m-d') : '-' }}</td>
                                                    <td>
                                                        @if($debt->activated)
                                                            <span class="badge text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($debt->status == 1)
                                                            <span class="badge text-bg-success">Active</span>
                                                        @elseif($debt->status == 0)
                                                            <span class="badge text-bg-warning">Inactive</span>
                                                        @else
                                                            <span class="badge text-bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $debt->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center">No debt type utilities found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">

                            <div class="card-body">

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-2">Utility Information</h6>

                                    <a href="/export-utilities?user_id={{$user->id}}&estate_id={{$user->estate_id}}" style="width: 100px" class="btn btn-success">Export</a>

                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th scope="col" class="cursor-pointer">ID</th>
                                                <th scope="col" class="cursor-pointer">Estate</th>
                                                <th scope="col" class="cursor-pointer">Duration</th>
                                                <th scope="col" class="cursor-pointer">Amount</th>
                                                <th scope="col" class="cursor-pointer">Status</th>
                                                <th scope="col" class="cursor-pointer">Date/Time</th>


                                            </tr>
                                            </thead>
                                            <tbody>


                                            @foreach($upayment as $data)

                                                <tr>
                                                    <td> {{$data->id}} </td>
                                                    <td>{{$estate_name}}</td>
                                                    <td>{{strtoupper($data->duration)}}</td>
                                                    <td>{{number_format($data->amount, 2)}}</td>
                                                    <td>
                                                        @if($data->status == 0)
                                                            <span class="badge text-bg-warning">Not Paid</span>
                                                        @else
                                                            <span class="badge text-bg-success">Paid</span>
                                                        @endif

                                                    </td>
                                                    <td>{{$data->created_at}}</td>

                                                </tr>

                                            @endforeach


                                            </tbody><!-- end tbody -->

                                            <tfoot>

                                            {{ $upayment->links() }}


                                            </tfoot>
                                        </table><!-- end table -->
                                    </div>
                                </div>


                            </div>

                        </div>

                        {{-- Utility Payment Records --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <h6 class="d-flex justify-content-start my-2">Utility Payment Records</h6>

                                    <form method="GET" action="{{ url()->current() }}">
                                        <input type="hidden" name="id" value="{{ $user->id }}">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label mb-1">Utility</label>
                                                <input type="text" name="utility_title" class="form-control" placeholder="Utility title" value="{{ request('utility_title') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">From</label>
                                                <input type="date" name="payment_date_from" class="form-control" value="{{ request('payment_date_from') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">To</label>
                                                <input type="date" name="payment_date_to" class="form-control" value="{{ request('payment_date_to') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">Transaction ID</label>
                                                <input type="text" name="trx_id" class="form-control" placeholder="Transaction number" value="{{ request('trx_id') }}">
                                            </div>
                                            <div class="col">
                                                <label class="form-label mb-1">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="">All Status</option>
                                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Not Paid</option>
                                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Payment in Progress</option>
                                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Paid</option>
                                                </select>
                                            </div>
                                            <div class="col-auto d-flex align-items-end">
                                                <div>
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                    <a href="{{ url()->current() }}?id={{ $user->id }}" class="btn btn-secondary">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="card-body">
                                        <table id="datatable-buttons"
                                               class="table table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Utility</th>
                                                <th>Utility Amount</th>
                                                <th>Amount Paid</th>
                                                <th>Transaction ID</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($utility_payment_records as $record)
                                                <tr>
                                                    <td>{{ $record->id }}</td>
                                                    <td>{{ $record->utility?->title ?? 'N/A' }}</td>
                                                    <td>{{ number_format($record->utility_amount, 2) }}</td>
                                                    <td>{{ number_format($record->amount_paid, 2) }}</td>
                                                    <td>{{ $record->trx_id }}</td>
                                                    <td>
                                                        @if($record->status == 0)
                                                            <span class="badge text-bg-warning">Not Paid</span>
                                                        @elseif($record->status == 1)
                                                            <span class="badge text-bg-info">Payment in Progress</span>
                                                        @else
                                                            <span class="badge text-bg-success">Paid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $record->created_at }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No payment records found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                            <tfoot>
                                            {{ $utility_payment_records->links() }}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                    @endif


                </div>
            </div>
        </div>

    @endif

@endsection
