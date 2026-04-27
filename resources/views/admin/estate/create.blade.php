<?php
    use App\Models\ModFeature;

    $handled_status = array_keys(ModFeature::HANDLED_STATUS);

    function statusValue ($status) {
        return ModFeature::HANDLED_STATUS[$status];
    }
?>

@extends('layouts.main')
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Add New Estate</h4>
                </div>
            </div>


            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <form action="estate-store" method="post">
                            @csrf

                            <div class="row">

                                <h6 class="d-flex justify-content-start my-4">Estate Information</h6>

                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">Estate Name</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>


                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">State</label>
                                    <select type="text" name="state" class="form-control" required>
                                        <option disabled selected>--Select State--</option>
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
                                    <label class="my-2">City</label>
                                    <input type="text" name="city" class="form-control" >
                                </div>

                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">LGA</label>
                                    <input type="text" name="lga" class="form-control" >


                                </div>



                            </div>

                            <hr class="my-4">


                            <div class="row">


                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">Charge Fee %</label>
                                    <input type="number" name="charge_fee_percent"  class="form-control" >
                                </div>

                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">Charge Fee (Flat)</label>
                                    <input type="number" name="charge_fee_flat"  class="form-control" >
                                </div>

                                <div class="col-3">
                                    <label class="my-2">Payment Type</label>
                                    <select type="text" name="ptype" class="form-control" required>
                                            <option value="">--select type---</option>
                                            <option value="1">APP Only</option>
                                            <option value="2">Web Only</option>
                                            <option value="3">App & Web</option>
                                    </select>

                                </div>

                                <div class="col-xl-3 col-sm-12">
                                    <label class="my-2">Estate Admin Fee</label>
                                    <input type="number" name="estate_admin_fee"  class="form-control" >
                                </div>


                            </div>

                            {{-- <hr class="my-4"> --}}

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
                                                @foreach ($handled_status as $my_status)
                                                    <option value="{{ $my_status }}"
                                                        @selected($feature->status == $my_status)
                                                    >
                                                        {{ statusValue($my_status) }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    @endforeach

                                </div>


                            <hr class="my-4">

                            <button type="submit" class="col-2 d-flex btn btn-primary">
                                Create
                            </button>



                        </form>


                    </div>


                </div>


            </div>


        </div>


    </div> <!-- container-fluid -->

@endsection
