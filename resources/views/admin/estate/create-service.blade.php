@extends('layouts.main')
@section('content')

    <div class="content">

        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Add New Estate Service</h4>
                </div>
            </div>

            @if(session()->has('message'))
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

                        <form action="add-new-service-list" method="post">
                            @csrf

                            <div class="row">
                                <h6 class="d-flex justify-content-start my-4">Professional Information</h6>

                                <div class="col-xl-4 col-sm-12 mb-3">
                                    <label class="my-2">Professional Name</label>
                                    <input type="text" name="professional_name" class="form-control" placeholder="Enter name" required>
                                </div>

                                <div class="col-xl-4 col-sm-12 mb-3">
                                    <label class="my-2">Professional Email</label>
                                    <input type="email" name="professional_email" class="form-control" placeholder="Enter email" required>
                                </div>

                                <div class="col-xl-4 col-sm-12 mb-3">
                                    <label class="my-2">Professional Phone</label>
                                    <input type="text" name="professional_phone" class="form-control" placeholder="Enter phone number" required>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <h6 class="d-flex justify-content-start my-4">Service Assignment</h6>

                                <div class="col-xl-6 col-sm-12 mb-3">
                                    <label class="my-2">Select Estate</label>
                                    <select name="estate_id" class="form-control" required>
                                        <option value="" disabled selected>--Select Estate--</option>
                                        @foreach($estates as $estate)
                                            <option value="{{ $estate->id }}">{{ $estate->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-xl-6 col-sm-12 mb-3">
                                    <label class="my-2">Service Category</label>
                                    <select name="service" class="form-control" required>
                                        <option value="" disabled selected>--Select Service--</option>
                                        @foreach($all_services as $service)
                                            <option value="{{ $service->id }}">{{ $service->service_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary px-4" style="background-color: #2D7D6D; border-color: #2D7D6D;">
                                        Create Service
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div> </div>

@endsection
