@extends('layouts.admin')

@section('content')
@php
    $only = request('only'); // customer | user | null
@endphp
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"> Create  {{ $only === 'customer' ? 'Customer ' : 'User' }}</h3>
            </div>

            <form id="createUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" data-mode="{{ isset($user) ? 'edit' : 'create' }}">
                @csrf

                <div class="card-body">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control"
                                    placeholder="Enter name"
                                    >
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control email"
                                    placeholder="Enter email"
                                    >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control"
                                    placeholder="Enter password"
                                    >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="cpassword"
                                    class="form-control"
                                    placeholder="Enter Confrim password"
                                    >
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="number"
                                    class="form-control"
                                    placeholder="Mobile Number" maxlength="12"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <!-- Role -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-control" >
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State <span class="text-danger">*</span></label>
                                 <div class="select2-blue">
                                <select  name="state" id="stateSelect" class="form-control select2bs4" data-dropdown-css-class="select2-blue" style="width: 100%;">
                                    <option> Select State</option>
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City<span class="text-danger">*</span></label>
                                <div class="select2-blue">
                                   
                                        <select id="citySelect" name="city" class="form-control select2bs4" data-dropdown-css-class="select2-blue" style="width: 100%;">
                                            <option value="">Select City</option>
                                        </select>
                                   
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Profile Image <span class="text-danger">*</span></label>

                                <input type="file"
                                    name="image"
                                    class="form-control"
                                    accept="image/*"
                                    onchange="previewImage(event)"
                                    >

                                <!-- Image Preview -->
                                <div class="mt-2">
                                    <img id="imagePreview"
                                        src=""
                                        alt="Preview"
                                        style="display:none; width:120px; height:120px; object-fit:cover; border-radius:6px; border:1px solid #ddd;">
                                </div>
                            </div>
                        </div>
                        @if( $only  == "customer")

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Categories</label>
                                  <div class="select2-blue">
                                    <div class="select2-blue">
                                       <select id="categorySelect"
                                            class="select2 form-control"
                                            multiple="multiple"
                                            data-placeholder="Select a category"
                                           data-dropdown-css-class="select2-blue" 
                                            style="width: 100%;" name="category_ids[]"
                                        class="form-control @error('category_id') is-invalid @enderror">
                                    <option value="all">Select All</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ old('category_id')==$id?'selected':'' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                    </div>
                                </div>

                            </div>

                        </div>
                        @endif
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('admin.users.index',request()->query()) }}" class="btn btn-info">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Submit
                        </button>

                    </div>
            </form>
        </div>

    </div>
</section>
@endsection
<script>
    window.CHECK_EMAIL_URL = "{{ route('admin.check.email') }}";
</script>
