@extends('layouts.main')
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">View Beneficiary</h4>
                </div>
                <div class="flex-shrink-0">
                    <a href="beneficiary" class="btn btn-secondary">Back</a>
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

            <div class="row">

                <div class="card">

                    <div class="card-body">

                        <form action="beneficiary-update" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{$beneficiary->id}}">

                            <div class="row">

                                <h6 class="d-flex justify-content-start my-4">Beneficiary Information</h6>

                                <div class="col-4">
                                    <label class="my-2">Choose Estate</label>
                                    <select type="text" name="estate_id" class="form-control" required>
                                        <option value="">Set Estate</option>
                                        @foreach($estate as $data)
                                            <option value="{{$data->id}}" @if($data->id == $beneficiary->estate_id) selected @endif>{{$data->title}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Line Item ID</label>
                                    <input type="text" name="line_items_id" class="form-control" value="{{$beneficiary->line_items_id}}">
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Beneficiary Name</label>
                                    <input type="text" name="beneficiary_name" class="form-control" value="{{$beneficiary->beneficiary_name}}">
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Beneficiary Account</label>
                                    <input type="text" name="beneficiary_account" class="form-control" value="{{$beneficiary->beneficiary_account}}" required>
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Choose Bank</label>
                                    <select type="text" name="bank_code" class="form-control" required>
                                        <option value="">Set Bank</option>
                                        @foreach($bank as $data)
                                            <option value="{{$data->remita_code}}" @if($data->remita_code == $beneficiary->bank_code) selected @endif>{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="my-2 d-block">Deduct Fee From Beneficiary</label>
                                    <input type="checkbox" name="deduct_fee_from" value="1" class="form-check-input" @if($beneficiary->deduct_fee_from) checked @endif>
                                </div>

                                <div class="col-4">
                                    <label class="my-2">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="2" @if($beneficiary->status == 2) selected @endif>Active</option>
                                        <option value="0" @if($beneficiary->status == 0) selected @endif>Inactive</option>
                                    </select>
                                </div>

                            </div>

                            <hr class="my-4">

                            <button type="submit" class="col-2 d-flex btn btn-primary">
                                Update
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div> <!-- container-fluid -->

@endsection