@extends('layouts.main')
@section('content')

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Beneficiaries List</h4>
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

                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="widget-first">

                                <div class="d-flex align-items-center mb-2">
                                    <div
                                        class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" fill="#D60574" fill-opacity="0.57"/>
                                            <circle cx="9" cy="7" r="4" fill="#D60574" fill-opacity="0.57"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="#D60574" stroke-width="2"/>
                                        </svg>
                                    </div>

                                    <p class="mb-0 text-dark fs-15">Total Beneficiaries</p>
                                </div>

                                <div class="d-flex align-items-center">
                                    <h3 class="mb-0 fs-24 text-black me-2">{{$beneficiary_count}}</h3>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card overflow-hidden">

                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title text-black mb-0">Beneficiaries List</h5>
                                <a href="new-beneficiary" class="btn btn-primary text-white justify-content-end">Add new</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="datatable-buttons"
                                   class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                <tr>
                                    <th scope="col" class="cursor-pointer">ID</th>
                                    <th scope="col" class="cursor-pointer">Line Item</th>
                                    <th scope="col" class="cursor-pointer">Beneficiary</th>
                                    <th scope="col" class="cursor-pointer">Account</th>
                                    <th scope="col" class="cursor-pointer">Bank</th>
                                    <th scope="col" class="cursor-pointer">Deduct Fee</th>
                                    <th scope="col" class="cursor-pointer">Estate</th>
                                    <th scope="col" class="cursor-pointer">Status</th>
                                    @if(Auth::user()->role == 0)
                                    <th scope="col" class="cursor-pointer desc">Action</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($beneficiary_list as $data)

                                    <tr>
                                        <td>{{$data->id}}</td>
                                        <td>{{$data->line_items_id}}</td>
                                        <td><a href="view-beneficiary?id={{$data->id}}">{{$data->beneficiary_name ?: '-'}}</a></td>
                                        <td>{{$data->beneficiary_account}}</td>
                                        <td>{{$data->bank->name ?? $data->bank_code}}</td>
                                        <td>
                                            @if($data->deduct_fee_from)
                                                <span class="badge text-bg-primary">Yes</span>
                                            @else
                                                <span class="badge text-bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>{{$data->estate->title ?? '-'}}</td>

                                        <td>
                                            @if($data->status == 2)
                                                <span class="badge text-bg-primary">Active</span>
                                            @else
                                                <span class="badge text-bg-warning">Inactive</span>
                                            @endif
                                        </td>

                                        @if(Auth::user()->role == 0)
                                        <td>
                                            @if($data->status == 2)
                                                <a href="beneficiary-deactivate?id={{$data->id}}" class="btn btn-warning btn-sm">Deactivate</a>
                                            @else
                                                <a href="beneficiary-activate?id={{$data->id}}" class="btn btn-success btn-sm">Activate</a>
                                            @endif
                                            <a href="view-beneficiary?id={{$data->id}}" class="btn btn-primary btn-sm">View</a>
                                            <a href="beneficiary-delete?id={{$data->id}}" onclick="return confirmDelete();" class="btn btn-danger btn-sm">Delete</a>
                                        </td>

                                        <script>
                                            function confirmDelete() {
                                                return confirm('Are you sure you want to delete this item?');
                                            }
                                        </script>
                                        @endif
                                    </tr>

                                @endforeach

                                </tbody><!-- end tbody -->

                                <tfoot>

                                {{ $beneficiary_list->links() }}

                                </tfoot>
                            </table><!-- end table -->
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div> <!-- container-fluid -->

@endsection