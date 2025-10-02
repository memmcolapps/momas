@extends('layouts.main')
@section('content')


    @if(Auth::user()->role == 0)
        <div class="content">

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

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Add New Meter</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-meter" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Meter Information</h6>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Meter Number</label>
                                        <input type="text" name="meterNo" class="form-control" required>
                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Meter Model</label>
                                        <select type="text" name="meterModel" class="form-control" required>
                                            <option value=" ">Select</option>
                                            <option value="prepaid">Prepaid</option>
                                            <option value="postpaid">Postpaid</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Account No</label>
                                        <input type="text" name="AccountNo" class="form-control" required>
                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Estate</label>
                                        <select type="text" id="estateSelect" name="estate_id" class="form-control" required>
                                            <option value="">Select an estate</option>
                                            @foreach($estate as $data)
                                                <option value="{{$data->id}}">{{$data->title}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <hr class="my-4">


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Transformer</label>
                                        <select type="text" name="TransformerID" class="form-control" >
                                            <option value=" ">Select</option>
                                            @foreach($transformer as $data)
                                                <option value="{{$data->id}}">{{$data->Title}} | {{$data->Capacity}} KVA </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-xl-3 col-sm-12 mt-4">
                                        <input  type="checkbox" id="isDualTariff" name="isDualTariff" class="form-check-input" style="border: 10px">
                                        <label class="form-check-label">Is Dual Tariff</label>

                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Old SGC</label>
                                        <select name="OldSGC" class="form-control" required>
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>

                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">New SGC</label>
                                        <select name="NewSGC" class="form-control" required>
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>

                                        </select>
                                    </div>



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


                                    <hr class="my-4">



                                    <div class="col-xl-3 col-sm-12" id="oldTariffDualContainer" style="display: none;">
                                        <label class="my-2">Old Generator Tariff </label>

                                        <select id="tariffSelect4" name="OldTariffDual" class="form-control" >
                                            <option value=""></option>
                                        </select>

                                    </div>


                                    <div class="col-xl-3 col-sm-12" id="newtar" style="display: none;">
                                        <label class="my-2">New Generator Tariff</label>
                                        <select id="tariffSelect3" name="NewTariffDual" class="form-control" >
                                            <option value=""></option>
                                        </select>

                                    </div>



                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">New NEPA Tariff</label>
                                            <select id="tariffSelect1" name="NewTariffID" class="form-control" >
                                                <option value=""></option>
                                            </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">

                                        <label class="my-2">Old NEPA Tariff</label>
                                        <select id="tariffSelect2" name="OldTariffID" class="form-control" >
                                            <option value=""></option>
                                        </select>
                                    </div>


                                    <script>


                                        document.addEventListener("DOMContentLoaded", function () {
                                            const estateSelect = document.getElementById("estateSelect");
                                            const tariffSelects = [
                                                document.getElementById("tariffSelect1"),
                                                document.getElementById("tariffSelect2"),
                                                document.getElementById("tariffSelect3"),
                                                document.getElementById("tariffSelect4"),
                                            ];
                                            const endpoint = "{{url('')}}/api/get-estate-tariff";

                                            estateSelect.addEventListener("change", function () {
                                                const estateId = this.value;

                                                tariffSelects.forEach((select) => {
                                                    select.innerHTML = '<option value="">Choose Tariff</option>';
                                                });

                                                if (!estateId) return;

                                                fetch(`${endpoint}?estate_id=${estateId}`)
                                                    .then((response) => response.json())
                                                    .then((data) => {
                                                        if (data?.tariffs?.length) {
                                                            tariffSelects.forEach((select) => {
                                                                data.tariffs.forEach((tariff) => {
                                                                    const displayText = tariff.tariff_index ? `${tariff.title} (Index: ${tariff.tariff_index})` : tariff.title;
                                                                    select.insertAdjacentHTML(
                                                                        "beforeend",
                                                                        `<option value="${tariff.id}">${displayText}</option>`
                                                                    );
                                                                });
                                                            });
                                                        } else {
                                                            tariffSelects.forEach((select) => {
                                                                select.innerHTML = '<option value="">No tariffs available</option>';
                                                            });
                                                        }
                                                    })
                                                    .catch((error) => {
                                                        console.error("Error fetching tariff data:", error);
                                                        tariffSelects.forEach((select) => {
                                                            select.innerHTML = '<option value="">Error fetching data</option>';
                                                        });
                                                    });
                                            });

                                            document.getElementById('isDualTariff').addEventListener('change', function () {
                                                const isChecked = this.checked;
                                                const toggleFields = [
                                                    'newtar',
                                                    'newTariffDualContainer',
                                                    'newSGCDualContainer',
                                                    'oldTariffDualContainer',
                                                    'oldSGCDualContainer',
                                                ];

                                                toggleFields.forEach((id) => {
                                                    const element = document.getElementById(id);
                                                    if (element) {
                                                        element.style.display = isChecked ? 'block' : 'none';
                                                        const input = element.querySelector('input, select');
                                                        if (input) {
                                                            input.required = isChecked;
                                                        }
                                                    }
                                                });
                                            });
                                        });


                                    </script>





























                                    <div class="col-xl-3 col-sm-12"  id="newTariffDualContainer" style="display: none;">
                                        <label class="my-2">New SGC Dual</label>
                                        <select name="NewSGCDual" class="form-control">
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>

                                    </div>


                                    <div class="col-xl-3 col-sm-12" id="newSGCDualContainer" style="display: none;">
                                        <label class="my-2">OLD SGC Dual</label>
                                        <select name="OldSGCDual" class="form-control">
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>

                                    </div>




                                    <hr class="my-4">



                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">KRN1</label>
                                        <!-- <select type="text" name="KRN1" class="form-control" required>
                                            <option value=" ">Select</option>
                                            <option value="STS6">STS6</option>
                                            <option value="STS">STS</option> 
                                        </select> -->
                                        <select type="text" name="KRN1" class="form-control" required>
                                            <option value="STS6" selected>STS6</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">KRN2</label>
                                        <!-- <select type="text" name="KRN2" class="form-control" >
                                            <option value=" ">Select</option>
                                            <option value="STS6">STS6</option>
                                            <option value="STS">STS</option> 
                                        </select> -->
                                        <select type="text" name="KRN2" class="form-control" required>
                                            <option value="STS6" selected>STS6</option>
                                        </select>
                                    </div>


                                    <div class="col-xl-3 col-sm-12 mt-4">
                                        <input  type="checkbox" name="NeedKCT" class="form-check-input" style="border: 10px">
                                        <label class="form-check-label">Need KCT</label>

                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Credit Type</label>
                                        <select type="text" name="CreditTypeID" class="form-control" required>
                                            <option value=" ">Select</option>
                                            <option value="water">Water</option>
                                            <option value="gas">Gas</option>
                                            <option value="electricity">Electricity</option>
                                        </select>
                                    </div>


                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Add Meter
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

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Add New Meter</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-meter" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Meter Information</h6>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Meter Number</label>
                                        <input type="text" name="meterNo" class="form-control" required>
                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Meter Model</label>
                                        <select type="text" name="meterModel" class="form-control" >
                                            <option value=" ">Select</option>
                                            <option value="prepaid">Prepaid</option>
                                            <option value="postpaid">Postpaid</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Account No</label>
                                        <input type="text" name="AccountNo" class="form-control" >
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Estate</label>
                                        <input type="text" value="{{$estate->title}}" class="form-control" required>
                                        <input type="text" value="{{$estate->id}}" name="estate_id"  hidden >

                                    </div>

                                    <hr class="my-4">


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Transformer</label>
                                        <select type="text" name="TransformerID" class="form-control" >
                                            <option value=" ">Select</option>
                                            @foreach($transformer as $data)
                                                <option value="{{$data->id}}">{{$data->Capacity}} KVA </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-xl-3 col-sm-12 mt-4">
                                        <input  type="checkbox" id="isDualTariff" name="isDualTariff" class="form-check-input" style="border: 10px">
                                        <label class="form-check-label">Is Dual Tariff</label>

                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Old SGC</label>
                                        <select name="OldSGC" class="form-control" >
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">New SGC</label>
                                        <select name="NewSGC" class="form-control" >
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>
                                    </div>








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


                                    <hr class="my-4">



                                    <div class="col-xl-3 col-sm-12" id="oldTariffDualContainer" style="display: none;">
                                        <label class="my-2">Old Tariff Dual</label>
                                        <select name="OldTariffDual" class="form-control" required>
                                            <option value=" ">Select</option>
                                            @foreach($tariffdual as $data)
                                                <option value="{{$data->OldTariffDual}}">{{$data->title}}</option>
                                            @endforeach
                                        </select>

                                    </div>


                                    <div class="col-xl-3 col-sm-12" id="newtar" style="display: none;">
                                        <label class="my-2">New Tariff Dual</label>
                                        <select name="NewTariffDual" class="form-control" required>
                                            <option value=" ">Select</option>
                                            @foreach($tariffdual as $data)
                                                <option value="{{$data->NewTariffDual}}">{{$data->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>




                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">New Tariff</label>
                                        <select name="NewTariffID" class="form-control">
                                            <option value="{{$meter->tariff_id ?? " "}}">{{$meter->tariff_id ?? ""}}</option>
                                            @foreach($tariff as $data)
                                                <option value="{{$data->id}}">{{$data->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>





                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Old Tariff</label>
                                        <select type="text" name="OldTariffID" class="form-control" required>
                                            <option value="{{$meter->tariff_id ?? ""}}">{{$meter->tariff_id ?? ""}}</option>
                                            @foreach($tariff as $data)
                                                <option value="{{$data->id}}">{{$data->title}} </option>
                                            @endforeach
                                        </select>
                                    </div>





                                    <div class="col-xl-3 col-sm-12 mt-2"  id="newTariffDualContainer" style="display: none;">
                                        <label class="my-2">New SGC Dual</label>
                                        <select name="NewSGCDual" class="form-control">
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>

                                    </div>


                                    <div class="col-xl-3 col-sm-12 mt-2" id="newSGCDualContainer" style="display: none;">
                                        <label class="my-2">OLD SGC Dual</label>
                                        <select name="OldSGCDual" class="form-control">
                                            <option value="999962">MOMAS Default (9***2)</option>
                                            <option value="600849">MOMAS System Nig Ltd (6***9)</option>
                                        </select>

                                    </div>




                                    <hr class="my-4">
                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">KRN1</label>
                                        <select type="text" name="KRN1" class="form-control" required>
                                            <option value="STS6" selected>STS6</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">KRN2</label>
                                        <select type="text" name="KRN2" class="form-control" required>
                                            <option value="STS6" selected>STS6</option>
                                        </select>
                                    </div>


                                    <div class="col-xl-3 col-sm-12 mt-4">
                                        <input  type="checkbox" name="NeedKCT" class="form-check-input" style="border: 10px">
                                        <label class="form-check-label">Need KCT</label>

                                    </div>


                                    <div class="col-xl-3 col-sm-12">
                                        <label class="my-2">Credit Type</label>
                                        <select type="text" name="CreditTypeID" class="form-control" required>
                                            <option value=" ">Select</option>
                                            <option value="water">Water</option>
                                            <option value="gas">Gas</option>
                                            <option value="electricity">Electricity</option>
                                        </select>
                                    </div>


                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Add Meter
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
