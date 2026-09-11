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
                                Click the button below to complete your payment.
                            </p>

                            <script src="https://demo.remita.net/payment/v1/remita-pay-inline.bundle.js"></script>
                            <input type="hidden" id="rrr" value="{{ $rrr }}">

                            <script>
                                function makePayment() {
                                    var paymentEngine = RmPaymentEngine.init({
                                        key: "QzAwMDAyNzEyNTl8MTEwNjE4NjF8OWZjOWYwNmMyZDk3MDRhYWM3YThiOThlNTNjZTE3ZjYxOTY5NDdmZWE1YzU3NDc0ZjE2ZDZjNTg1YWYxNWY3NWM4ZjMzNzZhNjNhZWZlOWQwNmJhNTFkMjIxYTRiMjYzZDkzNGQ3NTUxNDIxYWNlOGY4ZWEyODY3ZjlhNGUwYTY=",
                                        processRrr: true,
                                        transactionId: Math.floor(Math.random()*1101233),
                                        channel: ["CARD", "BRANCH", "PAYWITHREMITA", "TRANSFER"],
                                        extendedData: {
                                            customFields: [
                                                {
                                                    name: "rrr",
                                                    value: document.getElementById('rrr').value
                                                }
                                            ]
                                        },
                                        onSuccess: function (response) {
                                            window.location.href = '/admin/recepit?trx_id={{ $trx_id }}&type=credit_token';
                                        },
                                        onError: function (response) {
                                            alert('Payment failed. Please try again.');
                                        },
                                        onClose: function () {
                                            console.log("Payment widget closed");
                                        }
                                    });
                                    paymentEngine.showPaymentWidget();
                                }
                            </script>

                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-primary" onclick="makePayment()">
                                    Pay with Remita
                                </button>
                                <a href="/admin/credit-token" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- container-fluid -->
    </div>

@endsection
