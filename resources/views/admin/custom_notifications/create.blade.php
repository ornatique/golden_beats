@extends('layouts.admin')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Custom Notification</h3>
            </div>

            <form method="POST"
                action="{{ route('admin.custom-notifications.store') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    {{-- TITLE + DESCRIPTION --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description <span class="text-danger">*</span></label>
                                <textarea name="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- CATEGORY / SUBCATEGORY / PRODUCT --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category_id"
                                    id="categorySelect"
                                    class="form-control @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Subcategory <span class="text-danger">*</span></label>
                                <select name="subcategory_id"
                                    id="subcategorySelect"
                                    class="form-control @error('subcategory_id') is-invalid @enderror">
                                    <option value="">Select Subcategory</option>
                                </select>
                                @error('subcategory_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product <span class="text-danger">*</span></label>
                                <select name="product_id"
                                    id="productSelect"
                                    class="form-control @error('product_id') is-invalid @enderror">
                                    <option value="">Select Product</option>
                                </select>
                                @error('product_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- STATE / CITY / CUSTOMERS --}}
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>State <span class="text-danger">*</span></label>
                                <select name="state"
                                    id="stateSelect"
                                    class="form-control select2bs4 @error('state') is-invalid @enderror">
                                    <option value="">Select State</option>
                                </select>
                                @error('state')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>City <span class="text-danger">*</span></label>
                                <select name="city"
                                    id="citySelect"
                                    class="form-control select2bs4 @error('city') is-invalid @enderror">
                                    <option value="">Select City</option>
                                </select>
                                @error('city')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Customers <span class="text-danger">*</span></label>
                                <select name="customer_id[]"
                                    id="customerSelect"
                                    class="form-control select2bs4 @error('customer_id') is-invalid @enderror"
                                    multiple>
                                </select>
                                @error('customer_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- IMAGE --}}

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group"> <label>Gallery Images</label>
                                <input type="file" id="galleryInput" name="image" class="form-control @error('image') is-invalid @enderror" multiple onchange="previewGallery(this)">
                                @error('image')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <div id="galleryPreview" class="d-flex flex-row flex-wrap mt-2"></div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.custom-notifications.index') }}"
                        class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {

        $('#customerSelect').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Customers'
        });

    });

    /* ================= COLOR SYNC ================= */
    $('#colorPicker').on('input', () => $('#colorCode').val($('#colorPicker').val()));
    $('#colorCode').on('input', function() {
        if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) {
            $('#colorPicker').val(this.value);
        }
    });

    $('#bgColorPicker').on('input', () => $('#bgColorCode').val($('#bgColorPicker').val()));
    $('#bgColorCode').on('input', function() {
        if (/^#([0-9A-Fa-f]{6})$/.test(this.value)) {
            $('#bgColorPicker').val(this.value);
        }
    });

    /* ================= GALLERY PREVIEW ================= */
    function previewGallery(input) {
        $('#galleryPreview').html('');
        Array.from(input.files).forEach(file => {
            let reader = new FileReader();
            reader.onload = e => {
                $('#galleryPreview').append(
                    `<img src="${e.target.result}" width="80" class="mr-2 mb-2">`
                );
            };
            reader.readAsDataURL(file);
        });
    }

    /* ================= CLIENT SIDE VALIDATION ================= */
    $.validator.addMethod('filesize', function(value, element, param) {
        if (element.files.length === 0) {
            return true; // handled by required rule
        }

        for (let i = 0; i < element.files.length; i++) {
            if (element.files[i].size > param * 1024 * 1024) {
                return false;
            }
        }
        return true;
    }, 'Each file must be less than {0} MB');
    $('#productForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            category_id: {
                required: true
            },
            subcategory_id: {
                required: true
            },
            quantity: {
                digits: true,
                min: 0
            },
            gross_weight: {
                number: true
            },
            less_weight: {
                number: true
            },
            weight: {
                number: true
            },
            charge: {
                number: true
            },
            'gallery': {
                required: true,
                extension: "jpg|jpeg|png|webp|gif",
                filesize: 2
            }
        },

        messages: {
            name: "Please enter product name",
            category_id: "Please select category",
            subcategory_id: "Please select subcategory",
            'gallery': "Only image files allowed"
        },

        errorElement: 'span',
        errorClass: 'invalid-feedback',

        /**
         * ✅ FIX ERROR POSITION
         */
        errorPlacement: function(error, element) {

            error.addClass('invalid-feedback');

            // 🔥 ALWAYS append error inside form-group (at bottom)
            element.closest('.form-group').append(error);
        },
        submitHandler: function(form) {
            form.submit();
        }
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



    let galleryFiles = new DataTransfer();

    /* PREVIEW */
    function previewGallery(input) {

        if (!input || !input.files) return;

        Array.from(input.files).forEach(file => {

            if (!file.type.startsWith('image/')) return;

            galleryFiles.items.add(file);

            let reader = new FileReader();
            reader.onload = function(e) {
                $('#galleryPreview').append(`
                <div class="gallery-item" data-name="${file.name}">
                    <span class="remove-img" onclick="removeGalleryImage('${file.name}')">&times;</span>
                    <img src="${e.target.result}">
                </div>
            `);
            };
            reader.readAsDataURL(file);
        });

        const inputEl = document.getElementById('galleryInput');
        if (inputEl) {
            inputEl.files = galleryFiles.files;
        }

        // ✅ FORCE VALIDATION AGAIN
        $('#productForm').validate().element('#galleryInput');
    }


    /* REMOVE IMAGE */
    function removeGalleryImage(fileName) {

        let newFiles = new DataTransfer();

        Array.from(galleryFiles.files).forEach(file => {
            if (file.name !== fileName) {
                newFiles.items.add(file);
            }
        });

        galleryFiles = newFiles;
        document.getElementById('galleryInput').files = galleryFiles.files;

        // Remove preview block
        $(`.gallery-item[data-name="${fileName}"]`).remove();
    }

    $('#citySelect').on('change', function() {

        let city = $(this).val();

        // Clear existing options
        $('#customerSelect')
            .empty()
            .append('<option value="">Loading...</option>')
            .trigger('change');

        if (!city) {
            $('#customerSelect').empty().trigger('change');
            return;
        }

        $.ajax({
            url: "{{ route('admin.customers.byCity') }}",
            type: "GET",
            data: {
                city: city
            },
            success: function(customers) {

                let options = '';

                customers.forEach(function(customer) {
                    options += `
                    <option value="${customer.id}">
                        ${customer.name} (${customer.email})
                    </option>
                `;
                });

                // 🔥 IMPORTANT PART
                $('#customerSelect')
                    .html(options)
                    .trigger('change'); // force Select2 refresh
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Failed to load customers');
            }
        });
    });
</script>


@endpush