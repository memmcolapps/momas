@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">

            <!-- Start Content-->
            <div class="container-fluid">


                <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                    <div class="flex-grow-1">
                        <h4 class="fs-18 fw-semibold m-0">Emergency Token</h4>
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


                <div id="emergency-result" class="alert d-none">
                    <strong id="emergency-result-message"></strong>
                    <div id="emergency-result-body"></div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <div class="card overflow-hidden">

                            <div class="card">

                                <div class="card-header">
                                    <h5 class="card-title mb-0">Vending Information</h5>
                                </div><!-- end card header -->

                                <div class="card-body">
                                    <div class="row">


                                        <div class="d-flex justify-content-between my-4">
                                            <h5 class="card-title text-black mb-0">Generate Emergency Token</h5>
                                        </div>

                                        <div class="col-xl-8 col-sm-12">
                                            <form id="emergency-token-form" method="POST">
                                                @csrf

                                                <div class="modal-body">

                                                    <div class="row">
                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Enter Meter No</label>
                                                            <input type="number" class="form-control mb-3"
                                                                   name="meterNo" id="meterNo" required>
                                                        </div>

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Power Source</label>
                                                            <select class="form-control" required
                                                                    name="tariff_id" id="tariff_id" disabled>
                                                                <option value="">--Select Tariff--</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-xl-6 my-2 col-sm-12">
                                                            <label class="my-2">Amount</label>
                                                            <input type="number" class="form-control mb-3" name="amount"
                                                                   min="1" max="{{ $max_emergency_token }}"
                                                                   required>
                                                            <small class="text-muted">Maximum: NGN {{ number_format($max_emergency_token) }}</small>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-6 my-2 col-sm-12">
                                                        <button type="submit" class="btn btn-primary">Generate Token</button>
                                                    </div>

                                                </div>

                                            </form>
                                        </div>


                                    </div>
                                    <hr>
                                    <div class="card-body">

                                        <ul class="nav nav-tabs mb-3" id="emergencyTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="credit-tokens-tab" data-bs-toggle="tab"
                                                        data-bs-target="#credit-tokens-pane" type="button" role="tab"
                                                        aria-controls="credit-tokens-pane" aria-selected="true">
                                                    Credit Tokens
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="audit-logs-tab" data-bs-toggle="tab"
                                                        data-bs-target="#audit-logs-pane" type="button" role="tab"
                                                        aria-controls="audit-logs-pane" aria-selected="false">
                                                    Audit Logs
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="emergencyTabsContent">

                                            <div class="tab-pane fade show active" id="credit-tokens-pane" role="tabpanel"
                                                 aria-labelledby="credit-tokens-tab">

                                                <table id="emergency-credit-token-table"
                                                       class="table table-striped table-bordered dt-responsive nowrap">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col" class="cursor-pointer">Customer Name</th>
                                                        <th scope="col" class="cursor-pointer">Meter Number</th>
                                                        <th scope="col" class="cursor-pointer">Estate</th>
                                                        <th scope="col" class="cursor-pointer">Amount</th>
                                                        <th scope="col" class="cursor-pointer">Tariff Index</th>
                                                        <th scope="col" class="cursor-pointer desc">Unit</th>
                                                        <th scope="col" class="cursor-pointer desc">Token</th>
                                                        <th scope="col" class="cursor-pointer desc">Status</th>
                                                        <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($emergency_credit_tokens as $data)

                                                        <tr>
                                                            <td>
                                                                <a href="view-user?id={{$data->user?->id ?? null}}">{{$data->user->first_name ?? "name"}} {{$data->user->last_name ?? "name"}}</a>
                                                            </td>
                                                            <td>{{$data->meterNo}}</td>
                                                            <td>{{$data->estate->title ?? "name"}}</td>
                                                            <td>{{number_format($data->amount_charged ?? $data->amount, 2)}}</td>
                                                            <td>{{$data->tariff_id}}</td>
                                                            <td>{{$data->unitkwh}}kw/N</td>
                                                            <td>{{$data->token}}</td>
                                                            <td>
                                                                @if($data->status == 2)
                                                                    <span class="badge text-bg-primary">Successful</span>
                                                                @elseif($data->status == 0)
                                                                    <span class="badge text-bg-warning">Pending</span>
                                                                @elseif($data->status == 3)
                                                                    <span class="badge text-bg-danger">Declined</span>
                                                                @endif
                                                            </td>
                                                            <td>{{$data->created_at}}</td>
                                                        </tr>

                                                    @endforeach

                                                    </tbody><!-- end tbody -->
                                                </table><!-- end table -->

                                                <div class="d-flex justify-content-end mt-3">
                                                    {{ $emergency_credit_tokens->links() }}
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="audit-logs-pane" role="tabpanel"
                                                 aria-labelledby="audit-logs-tab">

                                                <table id="emergency-audit-table"
                                                       class="table table-striped table-bordered dt-responsive nowrap">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col" class="cursor-pointer">ID</th>
                                                        <th scope="col" class="cursor-pointer">Creator</th>
                                                        <th scope="col" class="cursor-pointer">Meter Number</th>
                                                        <th scope="col" class="cursor-pointer">Emergency Ref</th>
                                                        <th scope="col" class="cursor-pointer desc">Token</th>
                                                        <th scope="col" class="cursor-pointer desc">Amount</th>
                                                        <th scope="col" class="cursor-pointer desc">Unit</th>
                                                        <th scope="col" class="cursor-pointer desc">KCT Tokens</th>
                                                        <th scope="col" class="cursor-pointer desc">Date/Time</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($emergency_logs as $data)

                                                        @php
                                                            $context = $data->context ?? [];
                                                            $creator = $context['creator']['name'] ?? null;
                                                            if (!$creator) {
                                                                $creator = ($context['user']['firstname'] ?? '') . ' ' . ($context['user']['lastname'] ?? '');
                                                            }
                                                            $kct_tokens = $context['kct_tokens'] ?? null;
                                                            if (is_array($kct_tokens)) {
                                                                $kct_tokens = implode(', ', $kct_tokens);
                                                            }
                                                        @endphp

                                                        <tr>
                                                            <td>{{$data->id}}</td>
                                                            <td>{{$creator ?: "name"}}</td>
                                                            <td>{{$context['meter']['meterNo'] ?? ""}}</td>
                                                            <td>{{$context['emergency_ref'] ?? ""}}</td>
                                                            <td>{{$context['token'] ?? ""}}</td>
                                                            <td>{{number_format($context['amount'] ?? 0, 2)}}</td>
                                                            <td>{{$context['unit'] ?? ""}}kw/N</td>
                                                            <td>{{$kct_tokens ?: "N/A"}}</td>
                                                            <td>{{$data->created_at}}</td>
                                                        </tr>

                                                    @endforeach

                                                    </tbody><!-- end tbody -->
                                                </table><!-- end table -->

                                                <div class="d-flex justify-content-end mt-3">
                                                    {{ $emergency_logs->links() }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div> <!-- container-fluid -->

        </div>

        <script>
            $(document).ready(function () {

                $('#meterNo').on('input', function () {
                    var meterNo = $('#meterNo').val();

                    if (meterNo.length === 11 || meterNo.length === 13) {
                        $.ajax({
                            url: '/fetch-tariff',
                            method: 'GET',
                            data: {
                                meterNo: meterNo
                            },
                            success: function (response) {
                                if (response == 1) {
                                    alert("Error: Meter is not assigned to any User yet.");
                                    $('#tariff_id').prop('disabled', true).empty();
                                    return;
                                }
                                if (response == 2) {
                                    alert("Error: Estate meter does not have any tariff");
                                    $('#tariff_id').prop('disabled', true).empty();
                                    return;
                                }
                                if (response && response.tariffs) {
                                    var tariffSelect = $('#tariff_id');
                                    tariffSelect.empty();
                                    tariffSelect.append('<option value="">--Select Tariff--</option>');
                                    response.tariffs.forEach(function (tariff) {
                                        tariffSelect.append('<option value="' + tariff.id + '">' + tariff.title + ' (' + tariff.type + ')' + '</option>');
                                    });
                                    tariffSelect.prop('disabled', false);
                                } else {
                                    $('#tariff_id').prop('disabled', true).empty();
                                }
                            },
                            error: function () {
                                $('#tariff_id').prop('disabled', true).empty();
                                alert("Error fetching tariff data. Please try again.");
                            }
                        });
                    } else {
                        $('#tariff_id').prop('disabled', true).empty();
                    }
                });

                $('#emergency-token-form').on('submit', function (e) {
                    e.preventDefault();

                    var formData = {
                        _token: '{{ csrf_token() }}',
                        meterNo: $('#meterNo').val(),
                        tariff_id: $('#tariff_id').val(),
                        amount: $('input[name="amount"]').val()
                    };

                    $.ajax({
                        url: '/admin/emergency-meter-token',
                        method: 'POST',
                        data: formData,
                        success: function (response) {
                            if (response.status === true) {
                                var data = response.data;
                                var alert = $('#emergency-result');
                                alert.removeClass('alert-danger d-none').addClass('alert-success');

                                if ($('#emergency-token-form button[type="submit"]').length) {
                                    $('#emergency-token-form button[type="submit"]').prop('disabled', true).html('Generating...');
                                }

                                $('#emergency-result-message').text(response.message);
                                $('#emergency-result-body').html(
                                    'Emergency Ref: <strong>' + (data.emergency_ref || '') + '</strong><br>' +
                                    'Meter No: <strong>' + (data.meterNo || '') + '</strong><br>' +
                                    'Token: <strong>' + (data.token || '') + '</strong><br>' +
                                    'Amount: <strong>' + Number(data.amount || 0).toLocaleString() + '</strong><br>' +
                                    'Unit: <strong>' + (data.unit || '') + 'kw/N</strong><br>' +
                                    (data.kct_tokens ? 'KCT Tokens: <strong>' + (Array.isArray(data.kct_tokens) ? data.kct_tokens.join(', ') : data.kct_tokens) + '</strong><br>' : '')
                                );

                                setTimeout(function () {
                                    window.location.reload();
                                }, 120000);
                            } else {
                                $('#emergency-result').removeClass('alert-success d-none').addClass('alert-danger');
                                $('#emergency-result-message').text(response.message || 'An error occurred');
                                $('#emergency-result-body').html('');
                            }
                        },
                        error: function (xhr) {
                            var response = xhr.responseJSON || {};
                            $('#emergency-result').removeClass('alert-success d-none').addClass('alert-danger');
                            $('#emergency-result-message').text(response.message || 'An error occurred');
                            $('#emergency-result-body').html('');
                        }
                    });
                });

            });
        </script>

    @elseif(Auth::user()->role == 1)
    @elseif(Auth::user()->role == 2)
    @elseif(Auth::user()->role == 3)
    @elseif(Auth::user()->role == 4)
    @elseif(Auth::user()->role == 5)

    @else
    @endif

@endsection