<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8" />
    <title>Log In | Momas Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
    <meta name="author" content="momasadmin"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{url('')}}/public/asset/ass/images/favicon.ico">

    <!-- App css -->
    <link href="{{url('')}}/public/asset/ass/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="{{url('')}}/public/asset/ass/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        .btn {
            position: relative;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        .loader {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin-top: -10px;
            margin-left: -10px;
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3498db;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn.disabled {
            cursor: not-allowed;
            opacity: 0.6;
            padding: 20px;
        }
    </style>


</head>

<body class="bg-primary-subtle">
<!-- Begin page -->
<div class="account-page">
    <div class="container-fluid p-0">
        <div class="row align-items-center g-0">

            <div class="col-xl-5">
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card p-3 mb-0">
                            <div class="card-body">

                                <div class="mb-0 border-0 p-md-5 p-lg-0 p-4">
                                    <div class="mb-4 p-0 text-center">
                                        <a class='auth-logo' href='index.html'>
                                            <img src="{{url('')}}/public/asset/ass/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="70" />
                                        </a>
                                    </div>

                                    <div class="auth-title-section mb-3 text-center">
                                        <h3 class="text-dark fs-20 fw-medium mb-2">Welcome back</h3>
                                        <p class="text-dark text-capitalize fs-14 mb-0">Enter OTP Code</p>
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



                                    <div class="pt-0">
                                        <form id="loginForm" action="verify-code"  method="post"  class="my-4">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <input class="form-control" name="email" type="email" value="{{$email}}" hidden>
                                            </div>

                                            <div class="alert alert-info py-1 text-center fs-13 mb-3">
                                                OTP Code: <strong>{{$code ?? ''}}</strong>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="code" class="form-label">Code</label>
                                                <input class="form-control mb-3" name="code" type="text" required="" id="code" value="{{$code ?? ''}}" placeholder="Enter OTP Code">
                                                <span class="mt-4">Didn't Receive code ? <a href="resend_email_code">Resend</a> </span>
                                            </div>


                                            <div class="form-group mb-0 row">
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button id="loginBtn"  type="submit" class="btn btn-primary" type="submit">
                                                            <span class="btn-text"> Log In</span>
                                                            <div class="loader" id="loader"></div>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>



                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="account-page-bg p-md-5 p-4">
                    <div class="text-center">
                        <div class="auth-image">
                            <img src="{{url('')}}/public/asset/ass/images/auth-images.png" class="mx-auto img-fluid" alt="images">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- END wrapper -->

<!-- Vendor -->
<script src="{{url('')}}/public/asset/ass/libs/jquery/jquery.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/simplebar/simplebar.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/node-waves/waves.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/jquery.counterup/jquery.counterup.min.js"></script>
<script src="{{url('')}}/public/asset/ass/libs/feather-icons/feather.min.js"></script>

<!-- App js-->
<script src="{{url('')}}/public/asset/ass/js/app.js"></script>


<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const button = document.getElementById('loginBtn');
        const loader = document.getElementById('loader');
        const btnText = document.querySelector('.btn-text');
        button.classList.add('disabled');
        loader.style.display = 'block';
        btnText.style.display = 'none';
        button.disabled = true;
    });
</script>


</body>
</html>
