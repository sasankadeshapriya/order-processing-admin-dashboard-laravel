<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoicer | @yield('title', 'Invoicer')</title>

    @include('libraries.style')

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        @include('components.preloader')

        <!-- Navbar -->
        @include('components.navbar')
        <!-- Close Navbar -->

        <!-- Main Sidebar Container -->
        @include('components.sidebar')
        <!-- Close Main Sidebar Container -->

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        @include('components.footer')

        <!-- Control Sidebar -->
        @include('components.control-sidebar')
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('libraries.script')
    @yield('scripts')

</body>

</html>
