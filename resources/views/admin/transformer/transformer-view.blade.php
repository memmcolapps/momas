@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)

        <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">{{$trans->Title}}</h4>
                </div>
            </div>


            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <form action="update-transformer" method="post">
                            @csrf

                            <div class="row">

                                <h6 class="d-flex justify-content-start my-4">Transformer Information</h6>


                                <div class="col-3">
                                    <label class="my-2">Title</label>
                                    <input type="text" name="Title" value="{{$trans->Title}}" class="form-control" required>
                                    <input type="text" name="id" value="{{$trans->id}}" hidden>

                                </div>

                                <div class="col-3">
                                    <label class="my-2">Capacity</label>
                                    <input type="number" name="Capacity" value="{{$trans->Capacity}}" class="form-control" required>
                                </div>


                                <div class="col-3">
                                    <label class="my-2">MD Meter SN</label>
                                    <input type="text" name="MDMeterSN" value="{{$trans->MDMeterSN}}" class="form-control" required>
                                </div>


                                <div class="col-3 mt-4">
                                    @if($trans->Multiplier == "on")
                                        <input type="checkbox" name="Multiplier" checked class="form-check-input"
                                               style="border: 10px">
                                        <label class="form-check-label">Multiplier (Active)</label>
                                    @else
                                        <input type="checkbox" name="Multiplier" class="form-check-input"
                                               style="border: 10px">
                                        <label class="form-check-label">Multiplier (Inactive)</label>
                                    @endif


                                </div>


                                <hr class="my-4">


                                <div class="col-3">
                                    <label class="my-2">Estate</label>
                                    <select type="text" name="Estate_id" class="form-control" required>
                                        <option value="{{$trans->Estate_id}}">{{$trans->ownEstate->title}}</option>
                                     @foreach($estate as $data)
                                            <option value="{{$data->id}}">{{$data->title}} </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-3">
                                    <label class="my-2">Address</label>
                                    <input type="text" name="Location"  value="{{$trans->Location}}" class="form-control" required>
                                </div>


                                <div class="col-3">
                                    <label class="my-2">City</label>
                                    <input type="text" name="City"  value="{{$trans->City}}"  class="form-control" required>
                                </div>


                                <div class="col-3">
                                    <label class="my-2">State</label>
                                    <input type="text" name="State"  value="{{$trans->State}}"  class="form-control" required>
                                </div>


                                <hr class="my-4">


                                <div class="col-3">
                                    <label class="my-2">CT Ratio</label>
                                    <input type="text" name="CTRatio"  value="{{$trans->CTRatio}}"  class="form-control" required>
                                </div>


                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Transformer
                                </button>

                            </div>


                        </form>


                    </div>


                </div>


            </div>


        </div>


    </div> <!-- container-fluid -->

    @else

        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">{{$trans->Title}}</h4>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="update-transformer" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Transformer Information</h6>


                                    <div class="col-3">
                                        <label class="my-2">Title</label>
                                        <input type="text" name="Title" value="{{$trans->Title}}" class="form-control" required>
                                        <input type="text" name="id" value="{{$trans->id}}" hidden>

                                    </div>

                                    <div class="col-3">
                                        <label class="my-2">Capacity</label>
                                        <input type="number" name="Capacity" value="{{$trans->Capacity}}" class="form-control" required>
                                    </div>


                                    <div class="col-3">
                                        <label class="my-2">MD Meter SN</label>
                                        <input type="text" name="MDMeterSN" value="{{$trans->MDMeterSN}}" class="form-control" required>
                                    </div>


                                    <div class="col-3 mt-4">
                                        @if($trans->Multiplier == "on")
                                            <input type="checkbox" name="Multiplier" checked class="form-check-input"
                                                   style="border: 10px">
                                            <label class="form-check-label">Multiplier (Active)</label>
                                        @else
                                            <input type="checkbox" name="Multiplier" class="form-check-input"
                                                   style="border: 10px">
                                            <label class="form-check-label">Multiplier (Inactive)</label>
                                        @endif


                                    </div>


                                    <hr class="my-4">


                                    <div class="col-3">
                                        <label class="my-2">Estate</label>
                                        <input name="estate" value="{{$trans->estate}}" class="form-control" readonly>
                                        <input name="Estate_id" value="{{$trans->Estate_id}}" class="form-control" hidden>

                                    </div>


                                    <div class="col-3">
                                        <label class="my-2">Address</label>
                                        <input type="text" name="Location"  value="{{$trans->Location}}" class="form-control" required>
                                    </div>


                                    <div class="col-3">
                                        <label class="my-2">City</label>
                                        <input type="text" name="City"  value="{{$trans->City}}"  class="form-control" required>
                                    </div>


                                    <div class="col-3">
                                        <label class="my-2">State</label>
                                        <input type="text" name="State"  value="{{$trans->State}}"  class="form-control" required>
                                    </div>


                                    <hr class="my-4">


                                    <div class="col-3">
                                        <label class="my-2">CT Ratio</label>
                                        <input type="text" name="CTRatio"  value="{{$trans->CTRatio}}"  class="form-control" required>
                                    </div>


                                    <hr class="my-4">

                                    <button type="submit" class="col-2 d-flex btn btn-primary">
                                        Update Transformer
                                    </button>

                                </div>


                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </div> <!-- container-fluid -->


    @endif

@endsection
