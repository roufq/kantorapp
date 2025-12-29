<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | Office App</title>
    <meta content="Login to access the portal" name="description">
    <link rel="shortcut icon" href="{{ asset('NewAsset/assets/images/favicon.ico') }}">
    <link href="{{ asset('NewAsset/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('NewAsset/assets/css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('NewAsset/assets/css/style.css') }}" rel="stylesheet" type="text/css">
</head>
<body class="fixed-left">
    <div class="accountbg"></div>
    <div class="wrapper-page">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mt-0 m-b-15">
                    <a href="{{ url('/') }}" class="logo logo-admin">
                        OFFICE APP
                        <!-- <img src="{{ asset('NewAsset/assets/images/logo.png') }}" height="24" alt="logo"> -->
                    </a>
                </h3>
                <div class="p-3">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="form-horizontal m-t-20" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input class="form-control" type="password" name="password" required placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="remember">Remember me</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center row m-t-20">
                            <div class="col-12">
                                <button class="btn btn-danger btn-block waves-effect waves-light" type="submit">Log In</button>
                            </div>
                        </div>
                        @if(Route::has('password.request'))
                        <div class="form-group m-t-10 mb-0 row">
                            <div class="col-sm-12 text-center">
                                <a href="{{ route('password.request') }}" class="text-muted"><i class="mdi mdi-lock"></i> <small>Lupa password?</small></a>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('NewAsset/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/modernizr.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/detect.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/fastclick.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/waves.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/app.js') }}"></script>
</body>
</html>
