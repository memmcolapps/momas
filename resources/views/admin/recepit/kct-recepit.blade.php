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
                                    <p>
                                        C/Name : <b>{{$full_name ?? "Customer Name"}}</b></br><br>
                                        C/Address : <b>{{$address ?? "Customer Address"}}</b></br><br>
                                        Date :  <b>{{$date ?? "12345678"}}</b></br>
                                    </p>
                                </div>
                            </div><!--End Invoice Mid-->



                            <div id="bot">


                                <div class="info mt-4">
                                    <p>



                                        @if($title == "kct_token")
                                            Meter NO : <b>{{$meter_no}}</b></br><br>
                                            KCT 1 : <b>{{$token1 ?? "12345678"}}</b></br><br>
                                            KCT 2 : <b>{{$token2 ?? "12345678"}}</b></br><br>
                                        @elseif($title == "tamper")
                                            Meter NO : <b>{{$meter_no}}</b></br><br>
                                            Unit :  <b>{{$unitkwh ?? "0.00"}}KWH</b></br><br>
                                            Token : <b>{{$token ?? "12345678"}}</b></br><br>
                                        @elseif($title == "clear_credit_token")
                                            Meter NO : <b>{{$meter_no}}</b></br><br>
                                            Unit :  <b>{{$unitkwh ?? "0.00"}}KWH</b></br><br>
                                            Token : <b>{{$token ?? "12345678"}}</b></br><br>

                                        @else

                                            Amount : <b>₦ {{number_format($amount, 2) }}</b></br><br>
                                            Tariff Amt : <b>₦ {{number_format($tariff_amount, 2) }}</b></br><br>
                                            Meter NO : <b>{{$meter_no}}</b></br><br>
                                            Vat :  <b>{{$vat_amount ?? "0.00"}}</b></br><br>
                                            Unit :  <b>{{$unitkwh ?? "0.00"}}KWH</b></br><br>
                                            Token : <b>{{$token ?? "12345678"}}</b></br><br>
                                        @endif

                                    </p>
                                </div>


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


                                }
                            </style>

                            <center id="top">
                                <img class="my-3" src="{{url('')}}/public/asset/ass/images/logo-dark.png" alt="" height="30">
                                <div class="info">
                                    <h2>Momas Pay</h2>
                                </div><!--End Info-->
                            </center><!--End InvoiceTop-->




                            <center>
                                <p class="mt-2 mb-3"><b>-- Customer Copy --</b></p>
                            </center><!--End InvoiceTop-->



                            <hr>


                            <div id="mid">
                                <div class="info">
                                    <p>
                                        C/Name : <b>{{$full_name ?? "Customer Name"}}</b></br><br>
                                        C/Address : <b>{{$address ?? "Customer Address"}}</b></br><br>
                                        C/phone :  <b>{{$phone ?? "12345678"}}</b></br>
                                    </p>
                                </div>
                            </div><!--End Invoice Mid-->



                            <div id="bot">


                                <div class="info mt-4">
                                    <p>
                                        Meter NO : <b>{{$meter_no}}</b></br><br>
                                        KCT 1 : <b>{{$token1 ?? "12345678"}}</b></br><br>
                                        KCT 2 : <b>{{$token2 ?? "12345678"}}</b></br><br>


                                    </p>
                                </div>


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
