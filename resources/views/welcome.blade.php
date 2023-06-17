<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>STI-MES</title>
        <link rel="icon" type="image/png" sizes="180x180" href="{{asset('dist/img/sti.png')}}">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .hide {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        @if (Auth::user()->level == 9999)
                            <a href="{{ url('/home') }}">{{__('Home')}}</a>
                        @elseif(Auth::user()->level == 1)
                            <a href="{{ url('/lecturers') }}">{{__('Home')}}</a>
                        @else 
                        <a href="{{ url('/student') }}">{{__('Home')}}</a>

                        @endif
                    @else
                        <a href="{{ route('login') }}">{{__('Login')}}</a>

                        @if (Route::has('register'))
                            <!-- <a href="{{ route('register') }}">{{__('Register')}}</a> -->
                        @endif
                    @endauth
                    <a href="{{ route('home.changeLanguage', ['vi']) }}" class="dropdown-item">
                        {{__('Vietnamese')}}
                    </a>
                    <a href="{{ route('home.changeLanguage', ['en']) }}" class="dropdown-item">
                        English
                    </a>
                    <a href="{{ route('home.changeLanguage', ['ko']) }}" class="dropdown-item hide">
                        {{__('Korean')}}
                    </a>
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    {{__('Welcome To MES')}}
                    <br>
                    <div class="time" id="time"></div>
                </div>
            </div>
        </div>
    </body>
    <script>
        let id = document.getElementById('time');
        id.innerText = new Date().toLocaleString();

        setInterval(function()
        {
            id.innerText = new Date().toLocaleString();
        }, 1000)
    </script>
</html>
