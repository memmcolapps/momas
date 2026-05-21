@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Estates Token List</h4>
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

                    <div class="col-md-12 col-xl-12">
                        <div class="row">

                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                                    <svg width="20" height="17" viewBox="0 0 20 17" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 3.38391e-09C7.2198 -1.80724e-05 7.43348 0.0723807 7.608 0.206L7.708 0.293L10.414 3H17C17.7652 2.99996 18.5015 3.29233 19.0583 3.81728C19.615 4.34224 19.9501 5.06011 19.995 5.824L20 6V14C20 14.7652 19.7077 15.5015 19.1827 16.0583C18.6578 16.615 17.9399 16.9501 17.176 16.995L17 17H3C2.23479 17 1.49849 16.7077 0.941739 16.1827C0.384993 15.6578 0.0498925 14.9399 0.00500012 14.176L4.66045e-09 14V3C-4.26217e-05 2.23479 0.292325 1.49849 0.817284 0.941739C1.34224 0.384993 2.06011 0.0498925 2.824 0.00500012L3 3.38391e-09H7Z"
                                                            fill="#D60574" fill-opacity="0.57"/>
                                                    </svg>

                                                </div>

                                                <p class="mb-0 text-dark fs-15">Total Estate Token</p>
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <h3 class="mb-0 fs-24 text-black me-2">{{$token_count}}</h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="widget-first">

                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    class="bg-secondary-subtle rounded-circle p-2 me-2 border border-dashed border-secondary">
                                                    <svg width="20" height="17" viewBox="0 0 20 17" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7 3.38391e-09C7.2198 -1.80724e-05 7.43348 0.0723807 7.608 0.206L7.708 0.293L10.414 3H17C17.7652 2.99996 18.5015 3.29233 19.0583 3.81728C19.615 4.34224 19.9501 5.06011 19.995 5.824L20 6V14C20 14.7652 19.7077 15.5015 19.1827 16.0583C18.6578 16.615 17.9399 16.9501 17.176 16.995L17 17H3C2.23479 17 1.49849 16.7077 0.941739 16.1827C0.384993 15.6578 0.0498925 14.9399 0.00500012 14.176L4.66045e-09 14V3C-4.26217e-05 2.23479 0.292325 1.49849 0.817284 0.941739C1.34224 0.384993 2.06011 0.0498925 2.824 0.00500012L3 3.38391e-09H7Z"
                                                            fill="#D60574" fill-opacity="0.57"/>
                                                    </svg>

                                                </div>

                                                <p class="mb-0 text-dark fs-15">Total Estate Token</p>
                                            </div>

                                            <div class="d-flex align-items-center">
                                                <h3 class="mb-0 fs-24 text-black me-2">{{$token_count}}</h3>

                                            </div>

                                        </div>



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
                                    <h5 class="card-title text-black mb-0">Estates Token List</h5>
                                </div>


                            </div>

                            <div class="card-body">
                                <table id="datatable-buttons"
                                       class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Customer Name</th>
                                        <th scope="col" class="cursor-pointer">Token No</th>
                                        <th scope="col" class="cursor-pointer">Visitors</th>
                                        <th scope="col" class="cursor-pointer">Email</th>
                                        <th scope="col" class="cursor-pointer">Date</th>
                                        <th scope="col" class="cursor-pointer desc">Status</th>
                                        <th scope="col" class="cursor-pointer desc">Action</th>


                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($token as $data)

                                        <tr>
                                            <td>{{$data->user->first_name ?? "NAME"}}  {{$data->user->last_name ?? "NAME"}}</td>
                                            <td>{{$data->token}}</td>
                                            <td>{{$data->visitor}}</td>
                                            @if($data->email != null)
                                                <td>{{$data->email}}</td>
                                            @else
                                                <td>No email provided</td>
                                            @endif
                                            <td>{{$data->created_at}}</td>
                                            <td>
                                                @if($data->status == 2)
                                                    <span class="badge text-bg-primary">Activated</span>
                                                @elseif($data->status == 0)
                                                    <span class="badge text-bg-warning">Pending</span>
                                                @elseif($data->status == 3)
                                                    <span class="badge text-bg-danger">Rejected</span>
                                                @endif

                                            </td>


                                            @if($data->status == 2)
                                                <td><a href="token-deactivate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-warning">Deactivate
                                                        Token</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to deactivate this token?');
                                                        }
                                                    </script>
                                                </td>
                                            @else

                                                <td><a href="token-activate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-primary">Activate
                                                        Token</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to activate this token?');
                                                        }
                                                    </script>
                                                </td>
                                            @endif

                                        </tr>

                                    @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>

                                    {{ $token->links() }}


                                    </tfoot>
                                </table><!-- end table -->
                            </div>
                        </div>

                    </div>
                </div>


            </div>


        </div>

    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Estates Token List</h4>
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
                                            <svg width="20" height="17" viewBox="0 0 20 17" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7 3.38391e-09C7.2198 -1.80724e-05 7.43348 0.0723807 7.608 0.206L7.708 0.293L10.414 3H17C17.7652 2.99996 18.5015 3.29233 19.0583 3.81728C19.615 4.34224 19.9501 5.06011 19.995 5.824L20 6V14C20 14.7652 19.7077 15.5015 19.1827 16.0583C18.6578 16.615 17.9399 16.9501 17.176 16.995L17 17H3C2.23479 17 1.49849 16.7077 0.941739 16.1827C0.384993 15.6578 0.0498925 14.9399 0.00500012 14.176L4.66045e-09 14V3C-4.26217e-05 2.23479 0.292325 1.49849 0.817284 0.941739C1.34224 0.384993 2.06011 0.0498925 2.824 0.00500012L3 3.38391e-09H7Z"
                                                    fill="#D60574" fill-opacity="0.57"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Estate Token</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$token_count}}</h3>

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
                                    <h5 class="card-title text-black mb-0">Estates Token List</h5>
                                </div>


                            </div>

                            <div class="card-body">
                                <table id="datatable-buttons"
                                       class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Customer Name</th>
                                        <th scope="col" class="cursor-pointer">Token No</th>
                                        <th scope="col" class="cursor-pointer">Visitors</th>
                                        <th scope="col" class="cursor-pointer">Email</th>
                                        <th scope="col" class="cursor-pointer">Date</th>
                                        <th scope="col" class="cursor-pointer desc">Status</th>
                                        <th scope="col" class="cursor-pointer desc">Action</th>


                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($token as $data)

                                        <tr>
                                            <td>{{$data->user->first_name ?? "NAME"}}  {{$data->user->last_name ?? "NAME"}}</td>
                                            <td>{{$data->token}}</td>
                                            <td>{{$data->visitor}}</td>
                                            @if($data->email != null)
                                                <td>{{$data->email}}</td>
                                            @else
                                                <td>No email provided</td>
                                            @endif
                                            <td>{{$data->created_at}}</td>
                                            <td>
                                                @if($data->status == 2)
                                                    <span class="badge text-bg-primary">Activated</span>
                                                @elseif($data->status == 0)
                                                    <span class="badge text-bg-warning">Pending</span>
                                                @elseif($data->status == 3)
                                                    <span class="badge text-bg-danger">Rejected</span>
                                                @endif

                                            </td>


                                            @if($data->status == 2)
                                                <td><a href="token-deactivate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-warning">Deactivate
                                                        Token</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to deactivate this token?');
                                                        }
                                                    </script>
                                                </td>
                                            @else

                                                <td><a href="token-activate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-primary">Activate
                                                        Token</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to activate this token?');
                                                        }
                                                    </script>
                                                </td>
                                            @endif

                                        </tr>

                                    @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>

                                    {{ $token->links() }}


                                    </tfoot>
                                </table><!-- end table -->
                            </div>
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
