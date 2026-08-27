@extends('layouts.main')
@section('content')

    <div class="content">

        <div class="container-fluid">

            <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-18 fw-semibold m-0">Postpaid Accumulation Payment</h4>
                </div>
            </div>

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
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Accumulation Due</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Estate</p>
                                    <h5>{{ $estate->title }}</h5>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Accumulation Period</p>
                                    <h5>{{ $accumulationPeriod }} month(s)</h5>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Total Unpaid Ledger Fees</p>
                                    <h3 class="text-danger">₦{{ number_format($totalDue, 2) }}</h3>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Unpaid Ledger Entries</p>
                                    <h3>{{ $unpaidCount }}</h3>
                                </div>
                            </div>

                            @if($oldestUnpaid)
                                <div class="alert alert-warning">
                                    <strong>Note:</strong> Your oldest unpaid ledger entry dates back to <strong>{{ $oldestUnpaid->created_at->format('d M Y') }}</strong>.
                                    Postpaid vending is blocked until all accumulated fees are settled.
                                </div>
                            @endif

                            @if($totalDue > 0)
                                <div class="mt-4">
                                    <button type="button" class="btn btn-success btn-lg" id="payAccumulationBtn" data-estate="{{ $estate->id }}">
                                        <i class="feather-icon icon-credit-card"></i> Pay ₦{{ number_format($totalDue, 2) }} Now
                                    </button>
                                    <p class="text-muted mt-2 small">Payment via Paystack. Upon successful payment, all unpaid ledger fees will be marked as paid.</p>
                                </div>
                            @else
                                <div class="alert alert-success mt-4">
                                    <strong>All clear!</strong> You have no outstanding accumulation fees. You can now vend postpaid tokens.
                                    <a href="{{ url('admin/postpaid-token') }}" class="btn btn-sm btn-primary ms-3">Go to Postpaid Token</a>
                                </div>
                            @endif

                            <div id="paymentError" class="alert alert-danger mt-3" style="display: none;"></div>
                            <div id="paymentProcessing" class="mt-3" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Processing...</span>
                                </div>
                                <span class="ms-2">Initializing payment...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment History</h5>
                        </div>
                        <div class="card-body">
                            @if($paymentHistory->count() > 0)
                                @foreach($paymentHistory as $payment)
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-semibold">₦{{ number_format($payment->amount, 2) }}</p>
                                            <small class="text-muted">{{ $payment->paid_at->format('d M Y, h:i A') }}</small><br>
                                            <small class="text-muted">Ref: {{ $payment->trx_ref }}</small>
                                        </div>
                                        <div>
                                            <span class="badge text-bg-success">Paid</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No previous accumulation payments found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        $(document).ready(function () {
            $('#payAccumulationBtn').on('click', function () {
                var estateId = $(this).data('estate');
                var btn = $(this);

                btn.prop('disabled', true).text('Processing...');
                $('#paymentError').hide();
                $('#paymentProcessing').show();

                $.ajax({
                    url: '/admin/postpaid-accumulation-pay',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estate_id: estateId
                    },
                    success: function (response) {
                        $('#paymentProcessing').hide();

                        if (response.status && response.authorization_url) {
                            window.location.href = response.authorization_url;
                        } else {
                            $('#paymentError').text(response.message || 'Payment initialization failed.').show();
                            btn.prop('disabled', false).text('Pay ₦{{ number_format($totalDue, 2) }} Now');
                        }
                    },
                    error: function (xhr) {
                        $('#paymentProcessing').hide();
                        var msg = 'An error occurred. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        $('#paymentError').text(msg).show();
                        btn.prop('disabled', false).text('Pay ₦{{ number_format($totalDue, 2) }} Now');
                    }
                });
            });
        });
    </script>

@endsection
