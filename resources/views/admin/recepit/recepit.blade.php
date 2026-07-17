@extends('layouts.main')
@section('content')

    @if(Auth::user()->role == 0)
        <div class="content">
            <div class="container-fluid">


                <div class="card my-5">

                    <div class="card-body ">

                        <div id="invoice-POS">

                            <style>

                                #invoice-POS {
                                    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
                                    padding: 5mm;
                                    margin: 0 auto;
                                    width: 65mm;
                                    background: #FFF;


                                    ::selection {
                                        background: #f31544;
                                        color: #FFF;
                                    }

                                    ::moz-selection {
                                        background: #f31544;
                                        color: #FFF;
                                    }

                                    h1 {
                                        font-size: 1.5em;
                                        color: #222;
                                    }

                                    h2 {
                                        font-size: .9em;
                                    }

                                    h3 {
                                        font-size: 1.2em;
                                        font-weight: 300;
                                        line-height: 2em;
                                    }

                                    p {
                                        font-size: .7em;
                                        color: #666;
                                        line-height: 1.2em;
                                    }

                                    #top, #mid, #bot { /* Targets all id with 'col-' */
                                        border-bottom: 1px solid #EEE;
                                    }

                                    #top {
                                        min-height: 100px;
                                    }

                                    #mid {
                                        min-height: 80px;
                                    }

                                    #bot {
                                        min-height: 50px;
                                    }

                                    #top .logo {
                                    / / float: left;
                                        height: 60px;
                                        width: 60px;
                                        background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
                                        background-size: 60px 60px;
                                    }

                                    .clientlogo {
                                        float: left;
                                        height: 60px;
                                        width: 60px;
                                        background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
                                        background-size: 60px 60px;
                                        border-radius: 50px;
                                    }

                                    .info {
                                        display: block;
                                    / / float: left;
                                        margin-left: 0;
                                    }

                                    .title {
                                        float: right;
                                    }

                                    .title p {
                                        text-align: right;
                                    }

                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                    }

                                    td {
                                    / / padding: 5 px 0 5 px 15 px;
                                    / / border: 1 px solid #EEE
                                    }

                                    .tabletitle {
                                    / / padding: 5 px;
                                        font-size: .5em;
                                        background: #EEE;
                                    }

                                    .service {
                                        border-bottom: 1px solid #EEE;
                                    }

                                    .item {
                                        width: 24mm;
                                    }

                                    .itemtext {
                                        font-size: .5em;
                                    }

                                    #legalcopy {
                                        margin-top: 5mm;
                                    }

                                    .receipt-row {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 2px;
                                        font-size: .7em;
                                    }

                                }
                            </style>

                            <center id="top">
                                <img class="my-3" src="{{url('')}}/public/asset/ass/images/logo-dark.png" alt="" height="30">
                                <div class="info">
                                    <h2>Momas Pay</h2>
                                </div><!--End Info-->
                            </center><!--End InvoiceTop-->




                            <center>
                                <p class="mt-2 mb-3"><b>{{$title ?? "RECEPIT"}}</b></p>
                            </center><!--End InvoiceTop-->



                            <hr>


                            <div id="mid">
                                <div class="info">
                                    <div class="receipt-row"><span>C/Name</span><b>{{$full_name ?? "Customer Name"}}</b></div>
                                    <div class="receipt-row"><span>C/Address</span><b>{{$address ?? "Customer Address"}}</b></div>
                                    <div class="receipt-row"><span>Date</span><b>{{$date ?? "12345678"}}</b></div>
                                </div>
                            </div><!--End Invoice Mid-->



                            <div id="bot">


                                <div class="info mt-4">



                                    @if($title == "KCT TOKEN")
                                        <div class="receipt-row"><span>TRX ID</span><b>{{$ref}}</b></div>
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>KCT 1</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token1 ?? '12345678') }}</b></div>
                                        <div class="receipt-row"><span>KCT 2</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token2 ?? '12345678') }}</b></div>
                                    @elseif($title == "Clear Tamper Token")
                                        <div class="receipt-row"><span>TRX ID</span><b>{{$ref}}</b></div>
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>
                                    @elseif($title == "Clear Credit Token")
                                        <div class="receipt-row"><span>TRX ID</span><b>{{$ref}}</b></div>
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>

                                    @elseif($title == "Compensation Token")
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>Unit</span><b>{{$vend_amount_kw_per_naira ?? "0.00"}}KWH</b></div>
                                        <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>
                                        <div class="receipt-row"><span>Vat</span><b>{{$vat_amount ?? "0.00"}}</b></div>
                                        <div class="receipt-row"><span>Amount</span><b>₦ {{number_format($amount, 2) }}</b></div>

                                     @else
                                         <div class="receipt-row"><span>TRX ID</span><b>{{$ref}}</b></div>
                                         <div class="receipt-row"><span>Amount</span><b>₦ {{number_format($amount, 2) }}</b></div>
                                         @if(($debt_owed ?? 0) > 0)
                                             <div class="receipt-row"><span>Debt Owed</span><b>₦ {{number_format($debt_owed, 2)}}</b></div>
                                         @endif
                                          @if(($service_charge_owed ?? 0) > 0)
                                              <div class="receipt-row"><span>Service Charge Owed</span><b>₦ {{number_format($service_charge_owed, 2)}}</b></div>
                                          @endif
                                          @if(($cost_of_unit ?? 0) > 0)
                                              <div class="receipt-row"><span>Cost of Unit</span><b>₦ {{number_format($cost_of_unit, 2)}}</b></div>
                                          @endif
                                          <div class="receipt-row"><span>Tariff Amt</span><b>₦ {{number_format($tariff_amount, 2) }}</b></div>
                                         <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                         <div class="receipt-row"><span>Vat</span><b>{{$vat_amount ?? "0.00"}}</b></div>
                                         <div class="receipt-row"><span>Unit</span><b>{{round($unit, 2) ?? "0.00"}}KWH</b></div>
                                         <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>
                                         @if ($kct_token1)
                                             <div class="receipt-row"><span>KCToken1</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $kct_token1 ?? '12345678') }}</b></div>
                                             <div class="receipt-row"><span>KCToken2</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $kct_token2 ?? '12345678') }}</b></div>
                                         @endif
                                     @endif


                                <center>
                                    <div id="legalcopy" >
                                        <p class="legal"><i>Thanks for choosing momas!</i>

                                        </p>
                                    </div>

                                </center>


                            </div><!--End InvoiceBot-->
                        </div><!--End Invoice-->


                        <div class="d-print-none">
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-dark border-0"><i
                                        class="mdi mdi-printer me-1"></i>Print</a>
                            </div>
                            <div class="clearfix"></div>
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


                <div class="card my-5">

                    <div class="card-body ">

                        <div id="invoice-POS">

                            <style>

                                #invoice-POS {
                                    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
                                    padding: 5mm;
                                    margin: 0 auto;
                                    width: 65mm;
                                    background: #FFF;


                                    ::selection {
                                        background: #f31544;
                                        color: #FFF;
                                    }

                                    ::moz-selection {
                                        background: #f31544;
                                        color: #FFF;
                                    }

                                    h1 {
                                        font-size: 1.5em;
                                        color: #222;
                                    }

                                    h2 {
                                        font-size: .9em;
                                    }

                                    h3 {
                                        font-size: 1.2em;
                                        font-weight: 300;
                                        line-height: 2em;
                                    }

                                    p {
                                        font-size: .7em;
                                        color: #666;
                                        line-height: 1.2em;
                                    }

                                    #top, #mid, #bot { /* Targets all id with 'col-' */
                                        border-bottom: 1px solid #EEE;
                                    }

                                    #top {
                                        min-height: 100px;
                                    }

                                    #mid {
                                        min-height: 80px;
                                    }

                                    #bot {
                                        min-height: 50px;
                                    }

                                    #top .logo {
                                    / / float: left;
                                        height: 60px;
                                        width: 60px;
                                        background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
                                        background-size: 60px 60px;
                                    }

                                    .clientlogo {
                                        float: left;
                                        height: 60px;
                                        width: 60px;
                                        background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
                                        background-size: 60px 60px;
                                        border-radius: 50px;
                                    }

                                    .info {
                                        display: block;
                                    / / float: left;
                                        margin-left: 0;
                                    }

                                    .title {
                                        float: right;
                                    }

                                    .title p {
                                        text-align: right;
                                    }

                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                    }

                                    td {
                                    / / padding: 5 px 0 5 px 15 px;
                                    / / border: 1 px solid #EEE
                                    }

                                    .tabletitle {
                                    / / padding: 5 px;
                                        font-size: .5em;
                                        background: #EEE;
                                    }

                                    .service {
                                        border-bottom: 1px solid #EEE;
                                    }

                                    .item {
                                        width: 24mm;
                                    }

                                    .itemtext {
                                        font-size: .5em;
                                    }

                                    #legalcopy {
                                        margin-top: 5mm;
                                    }

                                    .receipt-row {
                                        display: flex;
                                        justify-content: space-between;
                                        margin-bottom: 2px;
                                        font-size: .7em;
                                    }

                                }
                            </style>

                            <center id="top">
                                <img class="my-3" src="{{url('')}}/public/asset/ass/images/logo-dark.png" alt="" height="30">
                                <div class="info">
                                    <h2>Momas Pay</h2>
                                </div><!--End Info-->
                            </center><!--End InvoiceTop-->




                            <center>
                                <p class="mt-2 mb-3"><b>{{$title ?? "RECEPIT"}}</b></p>
                            </center><!--End InvoiceTop-->


                            <hr>


                            <div id="mid">
                                <div class="info">
                                    <div class="receipt-row"><span>C/Name</span><b>{{$full_name ?? "Customer Name"}}</b></div>
                                    <div class="receipt-row"><span>C/Address</span><b>{{$address ?? "Customer Address"}}</b></div>
                                    <div class="receipt-row"><span>Date</span><b>{{$date ?? "12345678"}}</b></div>
                                </div>
                            </div><!--End Invoice Mid-->



                            <div id="bot">


                                <div class="info mt-4">



                                    @if($title == "kct_token")
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>KCT 1</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token1 ?? '12345678') }}</b></div>
                                        <div class="receipt-row"><span>KCT 2</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token2 ?? '12345678') }}</b></div>
                                    @elseif($title == "Clear Tamper Token")
                                        <div class="receipt-row"><span>TRX ID</span><b>{{$ref}}</b></div>
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>
                                    @elseif($title == "Clear Credit Token")
                                        <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                        <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>

                                     @else
                                         <div class="receipt-row"><span>Amount</span><b>₦ {{number_format($amount, 2) }}</b></div>
                                         @if(($debt_owed ?? 0) > 0)
                                             <div class="receipt-row"><span>Debt Owed</span><b>₦ {{number_format($debt_owed, 2)}}</b></div>
                                         @endif
                                          @if(($service_charge_owed ?? 0) > 0)
                                              <div class="receipt-row"><span>Service Charge Owed</span><b>₦ {{number_format($service_charge_owed, 2)}}</b></div>
                                          @endif
                                          @if(($cost_of_unit ?? 0) > 0)
                                              <div class="receipt-row"><span>Cost of Unit</span><b>₦ {{number_format($cost_of_unit, 2)}}</b></div>
                                          @endif
                                          <div class="receipt-row"><span>Tariff Amt</span><b>₦ {{number_format($tariff_amount, 2) }}</b></div>
                                         <div class="receipt-row"><span>Meter NO</span><b>{{$meter_no}}</b></div>
                                         <div class="receipt-row"><span>Vat</span><b>{{$vat_amount ?? "0.00"}}</b></div>
                                         <div class="receipt-row"><span>Unit</span><b>{{round($unit) ?? "0.00"}}KWH</b></div>
                                         <div class="receipt-row"><span>Token</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $token ?? '12345678') }}</b></div>
                                         @if ($kct_token1)
                                             <div class="receipt-row"><span>KCToken1</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $kct_token1 ?? '12345678') }}</b></div>
                                             <div class="receipt-row"><span>KCToken2</span><b>{{ preg_replace('/(.{4})(?!$)/', '$1-', $kct_token2 ?? '12345678') }}</b></div>
                                         @endif
                                     @endif


                                <center>
                                    <div id="legalcopy" >
                                        <p class="legal"><i>Thanks for choosing momas!</i>

                                        </p>
                                    </div>

                                </center>


                            </div><!--End InvoiceBot-->
                        </div><!--End Invoice-->


                        <div class="d-print-none">
                            <div class="float-end">
                                <a href="javascript:window.print()" class="btn btn-dark border-0"><i
                                        class="mdi mdi-printer me-1"></i>Print</a>
                            </div>
                            <div class="clearfix"></div>
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
