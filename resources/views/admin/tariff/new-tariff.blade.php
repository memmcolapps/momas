<?php
    use App\Models\Tariff;

    $power_source = Tariff::POWER_SOURCE;
?>

@extends('layouts.main')
@section('content')

<div id="tariff-data"
     data-used-indices='@json($used_indices_by_estate ?? [])'
     data-user-role="{{ Auth::user()->role }}"
     data-user-estate-id="{{ Auth::user()->estate_id ?? '' }}"
     style="display: none;">
</div>

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
                        <h4 class="fs-18 fw-semibold m-0">Add New Tariff</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-Tariff" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Tariff Information</h6>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Tariff Title</label>
                                        <input type="text" value="TF" name="title" class="form-control" required>


                                    </div>


                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Select Estate</label>
                                        <select required name="estate_id" class="form-control">
                                            <option value="">---select Estate----</option>
                                        @foreach($estate as $data)
                                                <option value="{{$data->id}}">{{$data->title}}</option>
                                            @endforeach

                                        </select>



                                    </div>


                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Source</label>
                                        <select class="form-control" name="tariff_source" required>
                                            <option value="">--Select Source--</option>
                                            @foreach ($power_source as $source)
                                                <option value="{{ $source }}">{{ $source }}</option>
                                            @endforeach
                                        </select>



                                    </div>



{{--                                    <div class="col-xl-4 col-sm-12">--}}
{{--                                        <label class="my-2">Tariff Index</label>--}}
{{--                                        <select class="form-control" name="tariff_index" required>--}}
{{--                                            <option value="">---Select Index-----</option>--}}
{{--                                            @php--}}
{{--                                                for ($i = 1; $i <= 99; $i++) {--}}
{{--                                                    echo "<option value=\"$i\">$i</option>";--}}
{{--                                                }--}}
{{--                                            @endphp--}}

{{--                                        </select>--}}

{{--                                    </div>--}}





                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Tariff Index</label>
                                        <select class="form-control" name="tariff_index" id="tariff_index_select" required>
                                            <option value="">---Select Index-----</option>
                                            @for ($i = 1; $i <= 99; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <hr class="my-4">


                                    <button type="submit" class="col-xl-2 col-sm-12 d-flex btn btn-primary">
                                        Create
                                    </button>

                                </div>


                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div> <!-- container-fluid -->
    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
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
                        <h4 class="fs-18 fw-semibold m-0">Add New Tariff</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="add-new-Tariff" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Tariff  Information</h6>


                                    <div class="col-3">
                                        <label class="my-2">Tariff Title</label>
                                        <input type="text" value="TF" name="title" class="form-control" required>
                                        <input type="hidden" name="estate_id" value="{{Auth::user()->estate_id}}">

                                    </div>


                                    <div class="col-3">
                                        <label class="my-2">Tariff Index</label>
                                        <select class="form-control" name="tariff_index" id="tariff_index_select" required>
                                            <option value="">---Select Index-----</option>
                                            @for ($i = 1; $i <= 99; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor

                                        </select>

                                    </div>

                                    <div class="col-xl-4 col-sm-12">
                                        <label class="my-2">Source</label>
                                        <select class="form-control" name="tariff_source" required>
                                            <option value="">--Select Source--</option>
                                            @foreach ($power_source as $source)
                                                <option value="{{ $source }}">{{ $source }}</option>
                                            @endforeach
                                        </select>



                                    </div>

                                        <!-- <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox"
                                                   name="apply_vat" id="apply_vat_estate" value="1" checked>
                                            <label class="form-check-label" for="apply_vat_estate">
                                                Apply VAT
                                            </label>
                                        </div> -->



                                    <hr class="my-4">

                                    <button type="submit" class="col-2 d-flex btn btn-primary">
                                        Create Tariff
                                    </button>

                                </div>


                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div> <!-- container-fluid -->
    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
        <div class="content">
            <div class="container-fluid">
                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Unauthorized Access</h4>
                        <p>You don't have permission to access this page.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif


<script>
document.addEventListener('DOMContentLoaded', function() {
    const estateSelect = document.querySelector('[name="estate_id"]');
    const tariffIndexSelect = document.getElementById('tariff_index_select');
    const dataContainer = document.getElementById('tariff-data');

    // Get data from HTML attributes
    const usedIndicesByEstate = JSON.parse(dataContainer.dataset.usedIndices);
    const userRole = parseInt(dataContainer.dataset.userRole);
    const userEstateId = dataContainer.dataset.userEstateId;

    function filterTariffIndices(estateId) {
        if (!tariffIndexSelect) return;

        const usedIndices = usedIndicesByEstate[estateId] || [];
        const options = tariffIndexSelect.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') return; // Skip placeholder

            if (usedIndices.includes(parseInt(option.value))) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });

        // Reset selected value if it's now hidden
        if (tariffIndexSelect.selectedOptions[0] && tariffIndexSelect.selectedOptions[0].style.display === 'none') {
            tariffIndexSelect.value = '';
        }
    }

    // For Super Admin - disable tariff index initially and enable after estate selection
    if (userRole === 0 && estateSelect && tariffIndexSelect) {
        // Initially disable tariff index dropdown
        tariffIndexSelect.disabled = true;
        tariffIndexSelect.innerHTML = '<option value="">Select estate first</option>';

        estateSelect.addEventListener('change', function() {
            const selectedEstateId = this.value;

            if (selectedEstateId && selectedEstateId !== '') {
                // Enable tariff index dropdown and restore all options
                tariffIndexSelect.disabled = false;
                tariffIndexSelect.innerHTML = '<option value="">---Select Index-----</option>';

                // Add all indices back
                for (let i = 1; i <= 99; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    tariffIndexSelect.appendChild(option);
                }

                // Then filter out used ones
                filterTariffIndices(selectedEstateId);
            } else {
                // Disable if no estate selected
                tariffIndexSelect.disabled = true;
                tariffIndexSelect.innerHTML = '<option value="">Select estate first</option>';
            }
        });
    }

    // For Estate Admin - filter immediately on page load
    if (userRole === 3 && userEstateId) {
        filterTariffIndices(userEstateId);
    }
});
</script>

@endsection
