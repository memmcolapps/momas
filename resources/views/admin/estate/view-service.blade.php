@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)

        <div class="content">
            <div class="container-fluid">

                @if (isset($errors) && $errors->any())
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
                        <h4 class="fs-18 fw-semibold m-0">View Estate Service</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ url('admin/estate-service') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="{{ url('admin/service-update') }}" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Estate Service Information</h6>

                                    <div class="col-4">
                                        <label class="my-2">Service Title</label>
                                        <select class="form-control mb-3" name="service" required>
                                            <option value="{{$services->service_id}}" disabled
                                                    selected>{{$services->service_title}}</option>
                                            @foreach($prof_services as $data)
                                                <option value="{{$data->id}}">{{$data->service_title}}</option>
                                            @endforeach

                                        </select>

                                        <input hidden name="id" value="{{$services->id}}" class="form-control" required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Full Name</label>
                                        <input type="text" name="professional_name"
                                               value="{{$services->professional_name}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Email</label>
                                        <input type="text" name="professional_email"
                                               value="{{$services->professional_email}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Phone No</label>
                                        <input type="text" name="professional_phone"
                                               value="{{$services->professional_phone}}" class="form-control"
                                               required>

                                    </div>


                                </div>

                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Info
                                </button>


                            </form>

                        </div>


                    </div>


                    <div class="card">
                        <div class="card-body">

                            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-18 fw-semibold m-0">Service Comment</h4>
                                </div>
                            </div>


                            <table class="table table-striped table-bordered dt-responsive nowrap">

                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Action</th>

                                </tr>
                                </thead>

                                <tbody>

                                @foreach($comment as $data)
                                    <tr>
                                        <td>{{$data->user->first_name ?? ($data->user_name ?? 'User')}} {{$data->user->last_name ?? ''}}</td>
                                        <td>{{$data->comment}}</td>
                                        <td>{{$data->created_at}}</td>
                                        <td><a href="{{ url('admin/delete-comment?id='.$data->id) }}" onclick="return confirmDelete();"
                                               class="btn btn-danger">Delete</a></td>
                                        <script>
                                            function confirmDelete() {
                                                return confirm('Are you sure you want to delete this comment?');
                                            }
                                        </script>
                                    </tr>
                                @endforeach



                                </tbody>


                            </table>


                        </div>
                    </div>


                </div>


            </div>
        </div>

    @elseif(Auth::user()->role == 1)


    @elseif(Auth::user()->role == 2)


    @elseif(Auth::user()->role == 3)

        <div class="content">
            <div class="container-fluid">

                @if (isset($errors) && $errors->any())
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
                        <h4 class="fs-18 fw-semibold m-0">View Estate Service</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ url('admin/estate-service') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>


                <div class="row">

                    <div class="card">

                        <div class="card-body">

                            <form action="{{ url('admin/service-update') }}" method="post">
                                @csrf

                                <div class="row">

                                    <h6 class="d-flex justify-content-start my-4">Estate Service Information</h6>

                                    <div class="col-4">
                                        <label class="my-2">Service Title</label>
                                        <select class="form-control mb-3" name="service" required>
                                            <option value="{{$services->service_id}}" disabled
                                                    selected>{{$services->service_title}}</option>
                                            @foreach($prof_services as $data)
                                                <option value="{{$data->id}}">{{$data->service_title}}</option>
                                            @endforeach

                                        </select>

                                        <input hidden name="id" value="{{$services->id}}" class="form-control" required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Full Name</label>
                                        <input type="text" name="professional_name"
                                               value="{{$services->professional_name}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Email</label>
                                        <input type="text" name="professional_email"
                                               value="{{$services->professional_email}}" class="form-control"
                                               required>

                                    </div>

                                    <div class="col-4">
                                        <label class="my-2">Phone No</label>
                                        <input type="text" name="professional_phone"
                                               value="{{$services->professional_phone}}" class="form-control"
                                               required>

                                    </div>


                                </div>

                                <hr class="my-4">

                                <button type="submit" class="col-2 d-flex btn btn-primary">
                                    Update Info
                                </button>


                            </form>

                        </div>


                    </div>


                    <div class="card">
                        <div class="card-body">

                            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-18 fw-semibold m-0">Service Comment</h4>
                                </div>
                            </div>


                            <table class="table table-striped table-bordered dt-responsive nowrap">

                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                    <th>Action</th>

                                </tr>
                                </thead>

                                <tbody>

                                    @foreach($comment as $data)
                                        <tr>
                                        <td>{{$data->user->first_name ?? ($data->user_name ?? 'User')}} {{$data->user->last_name ?? ''}}</td>
                                        <td>{{$data->comment}}</td>
                                        <td>{{$data->created_at}}</td>
                                        <td><a href="{{ url('admin/delete-comment?id='.$data->id) }}" onclick="return confirmDelete();"
                                               class="btn btn-danger">Delete</a></td>
                                        <script>
                                            function confirmDelete() {
                                                return confirm('Are you sure you want to delete this comment?');
                                            }
                                        </script>
                                        </tr>
                                    @endforeach



                                </tbody>


                            </table>


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
