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
                                            <option value="nepa">Nepa</option>
                                            <option value="gen">Generator</option>
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
                                        <select class="form-control" name="tariff_index" required>
                                            <option value="">---Select Index-----</option>
                                            @php
                                                for ($i = 1; $i <= 99; $i++) {
                                                    echo "<option value=\"$i\">$i</option>";
                                                }
                                            @endphp

                                        </select>

                                    </div>

                                    <div class="col-xl-6 col-sm-12">
                                        <label class="my-2">Source</label>
                                        <select class="form-control" name="tariff_source" required>
                                            <option value="">--Select Source--</option>
                                            <option value="nepa">Nepa</option>
                                            <option value="gen">Generator</option>
                                        </select>
                                    </div>



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



@endsection
