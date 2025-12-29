<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Golden Beats')</title>
    <!-- AdminLTE CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    
<div class="wrapper">
    @include('partials.admin-header')
    @include('partials.admin-sidebar')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">@yield('page-title')</h1>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    @include('partials.admin-footer')
</div>

<!-- AdminLTE JS -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

</body>
</html>