@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">

    {{-- TOTAL CUSTOMERS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalCustomer }}</h3>
                <p>Total Customers</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    {{-- ACTIVE / INACTIVE CUSTOMERS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activeCustomers }} / {{ $deactiveCustomers }}</h3>
                <p>Active / Inactive Customers</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
    </div>

    {{-- ADMIN USERS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalUsers ?? 0 }}</h3>
                <p>Total Admin Users</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    {{-- PRODUCTS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $Product }}</h3>
                <p>Total Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>

    {{-- CATEGORIES --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $Category }}</h3>
                <p>Total Categories</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>

    {{-- SUBCATEGORIES --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $Subcategory }}</h3>
                <p>Total Subcategories</p>
            </div>
            <div class="icon">
                <i class="fas fa-tag"></i>
            </div>
        </div>
    </div>

    {{-- ESTIMATES / ORDERS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalOrders }}</h3>
                <p>Total Estimates</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    {{-- CUSTOM ORDERS --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-dark">
            <div class="inner">
                <h3>{{ $CustomOrder }}</h3>
                <p>Total Custom Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-pencil-ruler"></i>
            </div>
        </div>
    </div>

</div>
@endsection
