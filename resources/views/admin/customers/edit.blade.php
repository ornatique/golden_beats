@extends('layouts.admin')

@section('content')
@php
    $only = request('only'); // customer | user | null
@endphp
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit  Customer</h3>
            </div>

            <form id="editUserForm"
                  method="POST"
                  action="{{ route('admin.customers.update', $customer->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <input type="hidden" name="only" value="{{ $only === 'customer' ? 'customer ' : 'user' }}">
                    {{-- Name + Email --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name', $customer->name) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ old('email', $customer->email) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Password + Confirm (Optional) --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           placeholder="Leave blank to keep current">

                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           id="cpassword"
                                           name="cpassword"
                                           class="form-control"
                                           placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile + Role --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="number"
                                       class="form-control"
                                       value="{{ old('number', $customer->number) }}"
                                       maxlength="12"
                                       inputmode="numeric"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                    <label>Categories</label>

                                    <div class="select2-blue">
                                        <select id="categorySelect"
                                                name="category_ids[]"
                                                class="select2 form-control @error('category_ids') is-invalid @enderror"
                                                multiple="multiple"
                                                data-placeholder="Select categories"
                                                data-dropdown-css-class="select2-blue"
                                                style="width:100%;">

                                            <option value="all">Select All</option>

                                            @foreach($categories as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ in_array($id, old('category_ids', $selectedCategories)) ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('category_ids')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                        </div>

                        
                    </div>

                    {{-- State + City --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <select name="state"
                                        id="stateSelect"
                                        class="form-control select2bs4">
                                    <option value="{{ $customer->state }}" selected>
                                        {{ $customer->state }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City</label>
                                <select name="city"
                                        id="citySelect"
                                        class="form-control select2bs4">
                                    <option value="{{ $customer->city }}" selected>
                                        {{ $customer->city }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Image --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Profile Image</label>
                                <input type="file"
                                       name="image"
                                       class="form-control"
                                       accept="image/*"
                                       onchange="previewImage(event)">

                                <div class="mt-2">
                                    <img id="imagePreview"
                                         src="{{ asset('uploads/customer/' .$customer->image) }}"
                                         style="width:120px;height:120px;border-radius:6px;">
                                </div>
                            </div>
                        </div>
                        {{-- Categories --}}
                        <div class="col-md-6">
                                
                            </div>
                       
                    </div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-info">Cancel</a>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection
