@extends('layouts.main')
@section('content')



    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">



                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Tariff List</h4>
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
                                            <svg width="20" height="20" viewBox="0 0 100 105" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M50 0C22.4444 0 0 21.21 0 47.25C0 67.7775 13.8889 85.26 33.3333 91.7175V105H44.4444V94.185C46.2778 94.5 48.1111 94.5 50 94.5C51.8889 94.5 53.7222 94.5 55.5556 94.185V105H66.6667V91.7175C86.1111 85.2075 100 67.725 100 47.25C100 21.21 77.5556 0 50 0ZM62.5 63L45.8333 78.75L37.5 70.875L44.4444 64.3125L37.5 57.75L54.1667 42L62.5 49.875L55.5556 56.4375L62.5 63ZM72.2222 36.75H27.7778V26.25H72.2222V36.75Z" fill="#E50086" fill-opacity="0.52"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Tariff</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$tarifftariffis}}</h3>

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
                                    <h5 class="card-title text-black mb-0">Latest Tariff</h5>
                                    <a href="new-tariff" class="btn btn-primary text-white justify-content-end">Add new Tariff</a>

                                </div>



                            </div>

                            <div class="card-body">
                                <table id="datatable-buttons"
                                       class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Tariff Source</th>
                                        <th scope="col" class="cursor-pointer">TariffCode</th>
                                        <th scope="col" class="cursor-pointer">Estate</th>
                                        <th scope="col" class="cursor-pointer">Status</th>
                                        <th scope="col" class="cursor-pointer">Date</th>
                                        <th scope="col" class="cursor-pointer desc">Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>


                                  @foreach($tariffis as $data)
                                      @php
                                          $isDisabled = $data->status != 2;
                                          $rowClass = $isDisabled ? 'table-secondary' : '';
                                      @endphp
                                      <tr class="{{ $rowClass }}">
                                          <td>{{$data->type}}</td>
                                          <td><a href="view-tariff?id={{$data->id}}" {{ $isDisabled ? 'style="pointer-events: none; opacity: 0.65;"' : '' }}>{{$data->title ?? "name"}}</a> </td>
                                          <td>{{$data->estate->title ?? "name"}}</td>
                                          <td>
                                              @if($data->status == 2)
                                                  <span class="badge text-bg-primary">Active</span>
                                              @elseif($data->status == 0)
                                                  <span class="badge text-bg-warning">Inactive</span>
                                              @elseif($data->status == 3)
                                                  <span class="badge text-bg-danger">Blocked</span>
                                              @endif

                                          </td>
                                          <td>{{$data->created_at }}</td>
                                          <td>
                                              @if($data->status == 2)
                                                  <a href="tariff-deactivate?id={{$data->id}}" onclick="return confirmDeactivate();" class="btn btn-warning">Deactivate</a>
                                                  <script>
                                                      function confirmDeactivate() {
                                                          return confirm('Are you sure you want to deactivate this item?');
                                                      }
                                                  </script>
                                              @else
                                                  <a href="tariff-activate?id={{$data->id}}" onclick="return confirmActivate();" class="btn btn-primary">Activate</a>
                                                  <script>
                                                      function confirmActivate() {
                                                          return confirm('Are you sure you want to activate this item?');
                                                      }
                                                  </script>
                                              @endif
                                          </td>

                                      </tr>

                                  @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>



                                    </tfoot>
                                </table><!-- end table -->
                            </div>
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



                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Tariff List</h4>
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
                                            <svg width="20" height="20" viewBox="0 0 100 105" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M50 0C22.4444 0 0 21.21 0 47.25C0 67.7775 13.8889 85.26 33.3333 91.7175V105H44.4444V94.185C46.2778 94.5 48.1111 94.5 50 94.5C51.8889 94.5 53.7222 94.5 55.5556 94.185V105H66.6667V91.7175C86.1111 85.2075 100 67.725 100 47.25C100 21.21 77.5556 0 50 0ZM62.5 63L45.8333 78.75L37.5 70.875L44.4444 64.3125L37.5 57.75L54.1667 42L62.5 49.875L55.5556 56.4375L62.5 63ZM72.2222 36.75H27.7778V26.25H72.2222V36.75Z" fill="#E50086" fill-opacity="0.52"/>
                                            </svg>

                                        </div>

                                        <p class="mb-0 text-dark fs-15">Total Tariff</p>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <h3 class="mb-0 fs-24 text-black me-2">{{$tarifftariffis}}</h3>

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
                                    <h5 class="card-title text-black mb-0">Latest Tariff</h5>
                                    <a href="new-tariff" class="btn btn-primary text-white justify-content-end">Add new Tariff</a>

                                </div>



                            </div>

                            <div class="card-body">
                                <table id="datatable-buttons"
                                       class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="cursor-pointer">Tariff Index</th>
                                        <th scope="col" class="cursor-pointer">TariffCode</th>
                                        <th scope="col" class="cursor-pointer">Estate</th>
                                        <th scope="col" class="cursor-pointer">Status</th>
                                        <th scope="col" class="cursor-pointer">Date/Time</th>
                                        <th scope="col" class="cursor-pointer desc">Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>


                                     @foreach($tariffis as $data)
                                         @php
                                             $isDisabled = $data->status != 2;
                                             $rowClass = $isDisabled ? 'table-secondary' : '';
                                         @endphp
                                         <tr class="{{ $rowClass }}">
                                             <td>{{$data->tariff_index}}</td>
                                             <td><a href="view-tariff?id={{$data->id}}" {{ $isDisabled ? 'style="pointer-events: none; opacity: 0.65;"' : '' }}>{{$data->title ?? "name"}}</a> </td>
                                             <td>{{$data->estate->title ?? "name"}}</td>
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
                                             <td><a href="tariff-deactivate?id={{$data->id}}"  onclick="return confirmDelete();" class="btn btn-warning">Deactivate</a>
                                                 <script>

                                                     function confirmDelete() {
                                                         return confirm('Are you sure you want to deactivate this item?');
                                                     }
                                                 </script>
                                             </td>

                                         </tr>

                                     @endforeach


                                    </tbody><!-- end tbody -->

                                    <tfoot>



                                    </tfoot>
                                </table><!-- end table -->
                            </div>
                        </div>

                    </div>
                </div>


            </div>


        </div> <!-- container-fluid -->
    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)
    @else
    @endif






@endsection
