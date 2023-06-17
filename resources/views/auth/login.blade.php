<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STI-MES</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ asset('dist/img/sti.png') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/multi-select.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="top-right links">
            <a href="{{ url('/') }}">{{ __('Back') }}</a>
            <a href="{{ route('home.changeLanguage', ['vi']) }}" class="dropdown-item">
                {{ __('Vietnamese') }}
            </a>
            <a href="{{ route('home.changeLanguage', ['en']) }}" class="dropdown-item">
                English
            </a>
            <a href="{{ route('home.changeLanguage', ['ko']) }}" class="dropdown-item hide">
                {{ __('Korean') }}
            </a>
        </div>
        <div class="container">
            <div class="card" style="width: 100%;">
                <div class="card-body">
                    <div class="row content_body">
                        <div class="col-7 logo">
                            <img src="logo.png" alt="">
                        </div>
                        <div class="col-5 " style="background-color: white;">
                            <div style="background-color: white;">
                                <div class="title_form">
                                    <p class="control title2">{{ __('Login') }}</p>
                                    <label> 
                                        
                                    </label>
                                </div>
                            </div>
                            <div style="background-color: white;">
                                <div>
                                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                        @foreach ($errors->all() as $error)
                                            <p style="color: red">{{ $error }}</p>
                                        @endforeach
                                    </ul>
                                </div>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-input">
                                        <div class="col-12 form-group">
                                            <label>{{ __('User Name') }}</label>
                                            <input type="text" class="form-control main-form" maxlength="20"
                                                name="username" placeholder="{{ __('User Name') }}" required
                                                autofocus>
                                        </div>

                                        <div class="col-12 form-group pass">
                                            <label>{{ __('Password') }}</label>
                                            <input type="password" class="form-control main-form" maxlength="20"
                                                name="password" placeholder="{{ __('Password') }}" required>
                                        </div>
                                        <div class="col-12 remember">
                                            <input type="checkbox" value="lsRememberMe" name="remember" id="rememberMe">
                                            <label for="rememberMe">{{ __('Remember Me') }}</label>
                                        </div>
                                        <div class="col-12 btn-div">
                                            <button class="btn btn-primary">{{ __('Login') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div style="background-color: white;">
                                <div class="title_form">
                                    <!-- <p style="color: gray;">khong co tai khoan ? <a href="register.html">register</a> -->
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        {{-- </div> --}}
    </div>
</body>
<!-- ./wrapper -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('dist/js/p5.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<script>
    let lang = $('html').attr('lang');
</script>
<script src="{{ asset('dist/js/rowgroup.js') }}"></script>
<script src="{{ asset('plugins/dhtml/dhtmlgantt.js') }}"></script>
<!-- AngularJS 1.6.9 -->
<script src="{{ asset('js/languages/vi.js') }}"></script>
<script src="{{ asset('js/languages/en.js') }}"></script>
<script src="{{ asset('js/languages/ko.js') }}"></script>
<!-- jQuery -->
<script></script>

</html>
