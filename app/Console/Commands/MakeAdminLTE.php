<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeAdminLTE extends Command
{
    protected $signature = 'make:adminlte';
    protected $description = 'Generate AdminLTE layout and partials';

    public function handle()
    {
        // Folders
        $folders = [
            resource_path('views/layouts'),
            resource_path('views/partials'),
            resource_path('views/admin'),
        ];

        foreach ($folders as $folder) {
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
                $this->info("Created folder: $folder");
            }
        }

        // Layout
        File::put(resource_path('views/layouts/admin.blade.php'), <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
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
</body>
</html>
BLADE
        );
        $this->info("Created layouts/admin.blade.php");

        // Header
        File::put(resource_path('views/partials/admin-header.blade.php'), <<<BLADE
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</nav>
BLADE
        );
        $this->info("Created partials/admin-header.blade.php");

        // Sidebar
        File::put(resource_path('views/partials/admin-sidebar.blade.php'), <<<BLADE
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
BLADE
        );
        $this->info("Created partials/admin-sidebar.blade.php");

        // Footer
        File::put(resource_path('views/partials/admin-footer.blade.php'), <<<BLADE
<footer class="main-footer">
    <strong>&copy; {{ date('Y') }} {{ config('app.name') }}.</strong> All rights reserved.
</footer>
BLADE
        );
        $this->info("Created partials/admin-footer.blade.php");

        // Dashboard
        File::put(resource_path('views/admin/dashboard.blade.php'), <<<BLADE
@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>150</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE
        );
        $this->info("Created admin/dashboard.blade.php");

        $this->info("AdminLTE files created successfully!");
    }
}
