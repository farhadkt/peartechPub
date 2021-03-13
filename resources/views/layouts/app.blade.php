<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset("plugins/fontawesome-free/css/all.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset("css/adminlte.min.css") }}">
    <link rel="stylesheet" href="{{ asset("css/skin-midnight.css") }}">
    <!-- Bootstrap Select -->
    <link rel="stylesheet" href="{{ asset("plugins/bootstrap-select/css/bootstrap-select.min.css") }}">
    <!-- Jquery UI -->
    <link rel="stylesheet" href="{{ asset("plugins/jquery-ui/jquery-ui.min.css") }}">
    <!-- Month Picker -->
    <link rel="stylesheet" href="{{ asset("plugins/jquery-month-picker/MonthPicker.css") }}">
    <!-- Custom pages -->
    @yield('css')
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset("css/styles.css") }}">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

</head>
<body class="hold-transition sidebar-mini layout-fixed skin-midnight {!! Render::pushMenu() !!}">
    <div class="wrapper">

        @include('layouts/app/header')

        @include('layouts/app/sidebar')

        <div class="content-wrapper">

            @include('layouts/app/content-header')

            <!-- Main content -->
            <div class="content">

                    @yield('content')

                <!-- /.container-fluid -->
            </div>
            <!-- /.content -->

        </div>

        @include('layouts/app/control-sidebar')

        @include('layouts/app/footer')

    </div>
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js')  }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <!-- bootstra-select -->
    <script src="{{ asset('plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <!-- bootstra-switch -->
    <script src="{{ asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <!-- Jquery UI -->
    <script src="{{ asset("plugins/jquery-ui/jquery-ui.min.js") }}"></script>
    <!-- Date range picker -->
    <script src="{{ asset('plugins/jquery-month-picker/MonthPicker.js') }}"></script>
    <!-- Input mask -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>

    <!-- Sweetalert -->
    @if (!Session::has('alert.config'))
        <script src="{{ asset('vendor/sweetalert/sweetalert.all.js')  }}"></script>
    @endif
    @include('sweetalert::alert')
    <!-- Scripts -->
    <script src="{{ asset('js/scripts.js') }}"></script>

    <!-- Custom Scripts -->
    @yield('js')
</body>
</html>
