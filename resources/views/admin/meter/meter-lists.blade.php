@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">

                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Meter List</h4>
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
                                            <svg width="20" height="20" viewBox="0 0 100 105" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M50 0C22.4444 0 0 21.21 0 47.25C0 67.7775 13.8889 85.26 33.3333 91.7175V105H44.4444V94.185C46.2778 94.5 48.1111 94.5 50 94.5C51.8889 94.5 53.7222 94.5 55.5556 94.185V105H66.6667V91.7175C86.1111 85.2075 100 67.725 100 47.25C100 21.21 77.5556 0 50 0ZM62.5 63L45.8333 78.75L37.5 70.875L44.4444 64.3125L37.5 57.75L54.1667 42L62.5 49.875L55.5556 56.4375L62.5 63ZM72.2222 36.75H27.7778V26.25H72.2222V36.75Z"
                                                    fill="#E50086" fill-opacity="0.52"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Meter</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$meters}}</h3>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xl-12">

                        <div class="card">
                            <div class="card-body">

                                <div class="row">

                                    <div class="element-box">

                                        <h6 class="element-header ">Filter</h6>

                                        <form action="filter-meter" method="post">
                                            @csrf

                                            <div class="row">
                                                <div class="col-3">
                                                    <label>Choose Estate</label>
                                                    <select  class="form-control" required name="estate_id">

                                                        <option value=" ">All</option>
                                                        @foreach($estate as $data)
                                                            <option value="{{$data->id}}">{{$data->title}}</option>
                                                        @endforeach

                                                    </select>
                                                </div>


                                                <div class="col-3">
                                                    <label>Enter Meter No</label>
                                                    <input type="number" class="form-control" name="meterNo">
                                                </div>

                                                <div class="col-2 mt-3">
                                                    <button type="submit" class="btn btn-primary w-100">Filter
                                                    </button>
                                                </div>

                                            </div>


                                            <div class="col-4 mt-4 row">



                                            </div>


                                        </form>

                                    </div>


                                </div>

                            </div>
                        </div>

                    </div>


                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">

                            <div class="col-xl-6">
                                <div class="card">
                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop"
                                         data-bs-backdrop="static" data-bs-keyboard="false"
                                         tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5"
                                                        id="staticBackdropLabel">Import Bulk Meter</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>

                                                <form action="{{ route('meters.import') }}" method="POST"
                                                      enctype="multipart/form-data">
                                                    @csrf

                                                    <div class="modal-body">

                                                        <p>Click here to download the sample of file to upload <a
                                                                href="{{url('')}}/public/asset/meter_upload_sample.csv">Download here</a>
                                                        </p>
                                                        <label class="mt-3">Choose File</label>
                                                        <input type="file" class="form-control mb-3" name="file"
                                                               required>

                                                        <label>Choose Estate</label>
                                                        <select name="estate_id" required class="form-control">
                                                            <option value=""> --Select Estate--</option>
                                                            @foreach($estate as $data)
                                                                <option
                                                                    value="{{$data->id}}"> {{$data->title}} </option>
                                                            @endforeach
                                                        </select>


                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">Import bulk
                                                            Meters
                                                        </button>
                                                    </div>

                                                </form>


                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card -->
                            </div> <!-- end col -->


                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title text-black mb-0">Latest Meter</h5>
                                    <a href="/export-meters" style="width: 100px" class="btn btn-success">Export</a>

                                    <div class="justify-content-end">
                                        <div class="justify-content-end">
                                            <a href="#" class="btn btn-primary text-white " data-bs-toggle="modal"
                                               data-bs-target="#staticBackdrop">Import Bulk Meter</a>
                                            <a href="new-meter" class="btn btn-primary text-white">Add new</a>
                                        </div>
                                    </div>

                                </div>


                            </div>


                            <div>


                            </div>

                            <div class="card-body">
                                <table class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Meter No</th>
                                        <th scope="col" class="cursor-pointer">Meter Model</th>
                                        <th scope="col" class="cursor-pointer">Estate</th>
                                        <th scope="col" class="cursor-pointer">Customer</th>
                                        <th scope="col" class="cursor-pointer">Status</th>
                                        <th scope="col" class="cursor-pointer">Date Added</th>
                                        @if(Auth::user()->role == 0)
                                            <th scope="col" class="cursor-pointer desc">Action</th>
                                        @endif

                                        <th scope="col" class="cursor-pointer desc">Action</th>


                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($meter_lists as $data)

                                        <tr>
                                            <td><a href="view-meter?id={{$data->id}}"> {{$data->meterNo}} </a></td>
                                            <td>
                                                {{strtoupper($data->meterModel)}}
                                            </td>
                                            <td>{{$data->estate->title ?? "Name"}}</td>
                                            <td>{{$data->user->first_name ?? "No user"}} {{$data->user->last_name ?? "attached"}}</td>

                                            <td>
                                                @if($data->status == 2)
                                                    <span class="badge text-bg-primary">Active</span>
                                                @elseif($data->status == 0)
                                                    <span class="badge text-bg-warning">Inactive</span>
                                                @elseif($data->status == 3)
                                                    <span class="badge text-bg-danger">Blocked</span>
                                                @endif

                                            </td>
                                            <td>{{$data->created_at}}</td>

                                            @if(Auth::user()->role == 0)
                                                <td><a href="meter-delete?id={{$data->id}}"
                                                       onclick="return confirmDelete();"
                                                       class="btn btn-danger">Delete</a></td>
                                                <script>
                                                    function confirmDelete() {
                                                        return confirm('Are you sure you want to delete this item?');
                                                    }
                                                </script>
                                            @endif

                                            @if($data->status == 2)
                                                <td><a href="meter-deactivate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-warning">Deactivate
                                                        Meter</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to deactivate this meter?');
                                                        }
                                                    </script>


                                                </td>
                                            @else

                                                <td><a href="meter-activate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-primary">Activate
                                                        Meter</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to activate this meter?');
                                                        }
                                                    </script>


                                                </td>
                                            @endif


                                        </tr>

                                    @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>

                                    {{ $meter_lists->links() }}


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
                        <h4 class="fs-18 fw-semibold m-0">Meter List</h4>
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
                                            <svg width="20" height="20" viewBox="0 0 100 105" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M50 0C22.4444 0 0 21.21 0 47.25C0 67.7775 13.8889 85.26 33.3333 91.7175V105H44.4444V94.185C46.2778 94.5 48.1111 94.5 50 94.5C51.8889 94.5 53.7222 94.5 55.5556 94.185V105H66.6667V91.7175C86.1111 85.2075 100 67.725 100 47.25C100 21.21 77.5556 0 50 0ZM62.5 63L45.8333 78.75L37.5 70.875L44.4444 64.3125L37.5 57.75L54.1667 42L62.5 49.875L55.5556 56.4375L62.5 63ZM72.2222 36.75H27.7778V26.25H72.2222V36.75Z"
                                                    fill="#E50086" fill-opacity="0.52"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Meter</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$meters}}</h3>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xl-12">

                        <div class="card">
                            <div class="card-body">

                                <div class="row">

                                    <div class="element-box">

                                        <h6 class="element-header ">Filter</h6>

                                        <form action="filter-meter" method="post">
                                            @csrf

                                            <div class="row">
                                                <div class="col-3">
                                                    <label>Choose Estate</label>
                                                    <select  class="form-control" required name="estate_id">

                                                        <option value=" ">All</option>
                                                        @foreach($estate as $data)
                                                            <option value="{{$data->id}}">{{$data->title}}</option>
                                                        @endforeach

                                                    </select>
                                                </div>


                                                <div class="col-3">
                                                    <label>Enter Meter No</label>
                                                    <input type="number" class="form-control" name="meterNo">
                                                </div>

                                                <div class="col-2 mt-3">
                                                    <button type="submit" class="btn btn-primary w-100">Filter
                                                    </button>
                                                </div>

                                            </div>


                                            <div class="col-4 mt-4 row">



                                            </div>


                                        </form>

                                    </div>


                                </div>

                            </div>
                        </div>

                    </div>



                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">

                            <div class="col-xl-6">
                                <div class="card">
                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop"
                                         data-bs-backdrop="static" data-bs-keyboard="false"
                                         tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5"
                                                        id="staticBackdropLabel">Import Bulk Meter</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>

                                                <form action="{{ route('meters.import') }}" method="POST"
                                                      enctype="multipart/form-data">
                                                    @csrf

                                                    <div class="modal-body">

                                                        <p>Click here to download the sample of file to upload <a
                                                                href="{{url('')}}/public/asset/meter_upload_sample.csv">Download here</a>
                                                        </p>
                                                        <label class="mt-3">Choose File</label>
                                                        <input type="file" class="form-control mb-3" name="file"
                                                               required>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">Import Bulk
                                                            Meters
                                                        </button>
                                                    </div>

                                                </form>


                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card -->
                            </div> <!-- end col -->


                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title text-black mb-0">Latest Meter</h5>
                                    {{--                                    <div class="justify-content-end">--}}
                                    {{--                                        <div class="justify-content-end">--}}
                                    {{--                                            <a href="#" class="btn btn-primary text-white " data-bs-toggle="modal"--}}
                                    {{--                                               data-bs-target="#staticBackdrop">Import Bulk Meter</a>--}}
                                    {{--                                            <a href="new-meter" class="btn btn-primary text-white">Add new</a>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}

                                </div>


                            </div>

                            <div class="card-body">
                                <table class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Meter No</th>
                                        <th scope="col" class="cursor-pointer">Meter Model</th>
                                        <th scope="col" class="cursor-pointer">Estate</th>
                                        <th scope="col" class="cursor-pointer">Customer</th>
                                        <th scope="col" class="cursor-pointer">Status</th>
                                        <th scope="col" class="cursor-pointer">Date Added</th>
                                        @if(Auth::user()->role == 0)
                                            <th scope="col" class="cursor-pointer desc">Action</th>
                                        @endif

                                        <th scope="col" class="cursor-pointer desc">Action</th>


                                    </tr>
                                    </thead>
                                    <tbody>


                                    @foreach($meter_lists as $data)

                                        <tr>
                                            <td><a href="view-meter?id={{$data->id}}"> {{$data->meterNo}} </a></td>
                                            <td>
                                                {{strtoupper($data->meterModel)}}
                                            </td>
                                            <td>{{$data->estate->title ?? "Name"}}</td>
                                            <td>{{$data->user->first_name ?? "No user"}} {{$data->user->last_name ?? "attached"}}</td>

                                            <td>
                                                @if($data->status == 2)
                                                    <span class="badge text-bg-primary">Active</span>
                                                @elseif($data->status == 0)
                                                    <span class="badge text-bg-warning">Inactive</span>
                                                @elseif($data->status == 3)
                                                    <span class="badge text-bg-danger">Blocked</span>
                                                @endif

                                            </td>
                                            <td>{{$data->created_at}}</td>

                                            @if(Auth::user()->role == 0)
                                                <td><a href="meter-delete?id={{$data->id}}"
                                                       onclick="return confirmDelete();"
                                                       class="btn btn-danger">Delete</a></td>
                                                <script>
                                                    function confirmDelete() {
                                                        return confirm('Are you sure you want to delete this item?');
                                                    }
                                                </script>
                                            @endif

                                            @if($data->status == 2)
                                                <td><a href="meter-deactivate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-warning">Deactivate
                                                        Meter</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to deactivate this meter?');
                                                        }
                                                    </script>


                                                </td>
                                            @else

                                                <td><a href="meter-activate?id={{$data->id}}"
                                                       onclick="return confirmupdate();" class="btn btn-primary">Activate
                                                        Meter</a>

                                                    <script>
                                                        function confirmupdate() {
                                                            return confirm('Are you sure you want to activate this meter?');
                                                        }
                                                    </script>
                                                </td>
                                            @endif

                                        </tr>

                                    @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>

                                    {{ $meter_lists->links() }}


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
