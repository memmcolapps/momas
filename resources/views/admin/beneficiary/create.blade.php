@extends('layouts.main')
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Add New Beneficiary</h4>
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

            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <form action="beneficiary-store" method="post">
                            @csrf

                            <div class="row">

                                <h6 class="d-flex justify-content-start my-4">Beneficiary Information</h6>

                                <div class="col-4">
                                    <label class="my-2">Choose Estate</label>
                                    <select type="text" name="estate_id" class="form-control" required>
                                        <option value="">Set Estate</option>
                                        @foreach($estate as $data)
                                            <option value="{{$data->id}}">{{$data->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Line Item ID</label>
                                    <input type="text" name="line_items_id" class="form-control" placeholder="Auto-generated if empty">
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Beneficiary Name</label>
                                    <input type="text" name="beneficiary_name" class="form-control">
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Beneficiary Account</label>
                                    <input type="text" name="beneficiary_account" class="form-control" required>
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Choose Bank</label>
                                    <select type="text" name="bank_code" class="form-control" required>
                                        <option value="">Set Bank</option>
                                        @foreach($bank as $data)
                                            <option value="{{$data->remita_code}}">{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="my-2 d-block">Deduct Fee From Beneficiary</label>
                                    <input type="checkbox" name="deduct_fee_from" value="1" class="form-check-input">
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="2">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

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