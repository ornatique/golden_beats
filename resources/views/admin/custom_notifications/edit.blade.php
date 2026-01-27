@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Custom Notification</h3>
            </div>

            <form method="POST"
                  action="{{ route('admin.custom-notifications.update', $notification->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- TITLE + DESCRIPTION --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title', $notification->title) }}"
                                       class="form-control @error('title') is-invalid @enderror">
                                @error('title') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea name="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description', $notification->description) }}</textarea>
                                @error('description') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- CATEGORY / SUBCATEGORY / PRODUCT --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select id="categorySelect"
                                        name="category_id"
                                        class="form-control @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('category_id', $notification->category_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Subcategory <span class="text-danger">*</span></label>
                                <select id="subcategorySelect"
                                        name="subcategory_id"
                                        class="form-control @error('subcategory_id') is-invalid @enderror">
                                    <option value="{{ $notification->subcategory_id }}">
                                        {{ $notification->subcategory?->name }}
                                    </option>
                                </select>
                                @error('subcategory_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product <span class="text-danger">*</span></label>
                                <select id="productSelect"
                                        name="product_id"
                                        class="form-control @error('product_id') is-invalid @enderror">
                                    <option value="{{ $notification->product_id }}">
                                        {{ $notification->product?->name }}
                                    </option>
                                </select>
                                @error('product_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- STATE / CITY / CUSTOMERS --}}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <select id="stateSelect"
                                        name="state"
                                        class="form-control select2bs4 @error('state') is-invalid @enderror">
                                    <option value="">Select State</option>
                                </select>
                                @error('state') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>City <span class="text-danger">*</span></label>
                                <select id="citySelect"
                                        name="city"
                                        class="form-control select2bs4 @error('city') is-invalid @enderror">
                                    <option value="">Select City</option>
                                </select>
                                @error('city') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Customers <span class="text-danger">*</span></label>
                                <select id="customerSelect"
                                        name="customer_id[]"
                                        class="form-control select2bs4 @error('customer_id') is-invalid @enderror"
                                        multiple>
                                </select>
                                @error('customer_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                    </div>

                    {{-- IMAGE --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Image <span class="text-danger">*</span></label>
                            <input type="file"
                                   name="image"
                                   class="form-control @error('image') is-invalid @enderror">
                            @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror

                            @if($notification->image)
                               <img src="{{ asset('uploads/custom_notifications/'.$notification->image) }}"
                                     width="120"
                                     class="mt-2">
                            @endif
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.custom-notifications.index') }}"
                       class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const existingState = @json(old('state', $notification->state));
const existingCity  = @json(old('city', $notification->city));
const selectedCustomers = @json(old('customer_id', is_array($notification->customer_id)? $notification->customer_id: json_decode($notification->customer_id ?? '[]', true)));
console.log(selectedCustomers)
$(document).ready(function () {

  $('#customerSelect').select2({
    theme: 'bootstrap4',
    width: '100%',
    placeholder: 'Select Customers'
});
    // STATES
    const states = [
        "Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh",
        "Goa","Gujarat","Haryana","Himachal Pradesh","Jharkhand","Karnataka",
        "Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram",
        "Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu",
        "Telangana","Tripura","Uttar Pradesh","Uttarakhand","West Bengal"
    ];

    states.forEach(s => {
        $('#stateSelect').append(
            `<option value="${s}" ${s === existingState ? 'selected' : ''}>${s}</option>`
        );
    });

    $('#stateSelect').trigger('change');
});

/* LOAD CITIES */
$('#stateSelect').on('change', function () {

    let state = $(this).val();
    $('#citySelect').html('<option>Loading...</option>');

    if (!state) return;

    fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ country: "India", state })
    })
    .then(res => res.json())
    .then(data => {
        let options = '<option value="">Select City</option>';
        data.data.forEach(city => {
            options += `<option value="${city}" ${city === existingCity ? 'selected' : ''}>${city}</option>`;
        });
        $('#citySelect').html(options).trigger('change');
    });
});
 $(document).ready(function() {

        /* ================= CATEGORY → SUBCATEGORY ================= */
        $('#categorySelect').on('change', function() {
            let categoryId = $(this).val();

            $('#subcategorySelect').html('<option value="">Loading...</option>');
            $('#productSelect').html('<option value="">Select Product</option>');

            if (!categoryId) {
                $('#subcategorySelect').html('<option value="">Select Subcategory</option>');
                return;
            }

            $.ajax({
                url: "{{ route('admin.get.subcategories_data', ':id') }}".replace(':id', categoryId),
                type: "GET",
                success: function(data) {
                    let options = '<option value="">Select Subcategory</option>';

                    $.each(data, function(id, name) {
                        options += `<option value="${id}">${name}</option>`;
                    });

                    $('#subcategorySelect').html(options);
                },
                error: function() {
                    $('#subcategorySelect').html('<option value="">Error loading</option>');
                }
            });
        });

        /* ================= SUBCATEGORY → PRODUCT ================= */
        $('#subcategorySelect').on('change', function() {
            let subcategoryId = $(this).val();

            $('#productSelect').html('<option value="">Loading...</option>');

            if (!subcategoryId) {
                $('#productSelect').html('<option value="">Select Product</option>');
                return;
            }

            $.ajax({
                url: "{{ route('admin.get-products', ':id') }}"
                    .replace(':id', subcategoryId),
                type: "GET",
                success: function(data) {

                    let options = '<option value="">Select Product</option>';

                    data.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });

                    $('#productSelect').html(options);
                },
                error: function() {
                    $('#productSelect').html('<option value="">Error loading</option>');
                }
            });
        });

    });

/* LOAD CUSTOMERS */
$('#citySelect').on('change', function () {

    let city = $(this).val();

    $('#customerSelect')
        .empty()
        .append('<option value="">Loading...</option>')
        .trigger('change');

    if (!city) return;

    $.ajax({
        url: "{{ route('admin.customers.byCity') }}",
        type: "GET",
        data: { city: city },

        success: function (customers) {

            let options = '';

            customers.forEach(function (customer) {

                const isSelected = selectedCustomers.includes(
                    customer.id.toString()
                );

                options += `
                    <option value="${customer.id}" ${isSelected ? 'selected' : ''}>
                        ${customer.name} (${customer.email})
                    </option>
                `;
            });

            // 🔥 THIS IS THE FIX
            $('#customerSelect')
                .html(options)
                .val(selectedCustomers)
                .trigger('change');
        },

        error: function () {
            alert('Failed to load customers');
        }
    });
});

</script>
@endpush
