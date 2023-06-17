<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test YOPAZ</title>
    <link rel="icon" sizes="180x180" href="{{ asset('favicon.ico') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('dist/css/adminlte.min.css') }}" rel="stylesheet">
    @stack('myCss')
    <style>
         .select2-container
        {
            width:100% !important;
        }
        .table tbody td {
            vertical-align: middle !important;
            text-align:center !important;
        }
        .table thead th {
            vertical-align: middle !important;
            text-align:center !important;
        }
    </style>
</head>

<body class="sidebar-mini sidebar-collapse" style="height: 100vh">
    <div class="wrapper">
        @include('layouts.nav-top')
        @include('layouts.nav-left')
        <div class="content-wrapper p-3">
            @yield('content')
        </div>
       
    </div>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    <script>
        let lang = $('html').attr('lang');
    </script>
    <script src="{{ asset('dist/js/rowgroup.js') }}"></script>
    <script src="{{ asset('plugins/dhtml/dhtmlgantt.js') }}"></script>
    <script src="{{ asset('plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ asset('js/languages/vi.js') }}"></script>
    <script src="{{ asset('js/languages/en.js') }}"></script>
    <script src="{{ asset('js/languages/ko.js') }}"></script>
    {{-- <script src="{{ asset('plugins/socket/socket.io.min.js') }}"></script> --}}
    <!-- jQuery -->
    <script>
        let url = window.location.href;
        let cut = url.split('?')[0];
        let target = cut.split('/');
        $('aside .nav-link').removeClass('active');
        target.splice(0, 1);
        target.splice(0, 1);
        target.splice(0, 1);
        for (let i = 0; i < target.length; i++) {
            let myClass = target[i].split('#')[0];
            $('.' + myClass).addClass('active');
        }
        $('input').prop('autocomplete', 'off');
        var role_edit = {{ Auth::user()->checkRole('update_master') || Auth::user()->level == 9999 ? 1 : 0 }}
        var role_delete = {{ Auth::user()->checkRole('delete_master') || Auth::user()->level == 9999 ? 1 : 0 }}
        // var socket = io('{{ config('app.server_socket') }}{{ config('app.port_socket') }}');
    </script>
        {{--<script src="{{ config('app.server_socket') }}{{ config('app.port_socket') }}/socket.io/socket.io.js"></script>
        <script>
            var socket = io("{{ config('app.server_socket') }}{{ config('app.port_socket') }}");
        </script>--}}
    @stack('scripts')
</body>

</html>
