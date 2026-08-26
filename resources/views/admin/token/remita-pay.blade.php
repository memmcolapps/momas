@extends('layouts.main')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Remita Payment</h4>
                </div>
            </div>

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-xl-6 col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Complete Your Payment</h5>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-6 my-2">
                                    <label class="my-2">Transaction Reference</label>
                                    <h6>{{ $trx_id }}</h6>
                                </div>

                                <div class="col-sm-6 my-2">
                                    <label class="my-2">RRR</label>
                                    <h6>{{ $rrr }}</h6>
                                </div>

                                <div class="col-sm-6 my-2">
                                    <label class="my-2">Amount</label>
                                    <h6>&#8358;{{ number_format((float) $amount, 2) }}</h6>
                                </div>
                            </div>

                            <hr>

                            <p class="text-muted">
                                Click the button below to proceed to Remita's secure payment page.
                            </p>

                            <form id="remita-payment-form"
                                  action="{{ $action }}"
                                  method="POST">
                                @foreach ($payload as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach
                                <input type="hidden" name="response_url" value="{{ $response_url }}">

                                <button type="submit" class="btn btn-primary">Pay with Remita</button>
                            </form>

                            <a href="/admin/credit-token" class="btn btn-secondary mt-3">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>

@endsection
