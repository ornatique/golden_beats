@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Product</h3>
            </div>

            <form method="POST"
                action="{{ route('admin.products.store') }}"
                enctype="multipart/form-data"
                id="productForm">
                @csrf

                <div class="card-body">

                    {{-- Category + Subcategory --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" id="categorySelect">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id=>$name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategory <span class="text-danger">*</span></label>
                                <select name="subcategory_id" class="form-control" id="subcategorySelect">
                                    <option value="">Select Subcategory</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Name + Number --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Number</label>
                                <input type="text" name="number" class="form-control">
                            </div>
                        </div>
                    </div>

                    {{-- Size + Hole Size --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Size</label>
                            <input type="text" name="size" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Hole Size</label>
                            <input type="text" name="hole_size" class="form-control">
                        </div>
                    </div>

                    {{-- Weights --}}
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>Gross Weight</label>
                            <input type="number" step="0.001" name="gross_weight" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Less Weight</label>
                            <input type="number" step="0.001" name="less_weight" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Net Weight</label>
                            <input type="number" step="0.001" name="weight" class="form-control">
                        </div>
                    </div>

                    {{-- Quantity + Charge --}}
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Other Charges </label>
                            <input type="number" step="0.01" name="charge" class="form-control">
                        </div>
                    </div>

                    {{-- Color + BG Color --}}
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label>Color</label>
                            <input type="color" name="color" id="colorPicker" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Color Code</label>
                            <input type="text" id="colorCode" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label>BG Color</label>
                            <input type="color" name="bg_color" id="bgColorPicker" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>BG Color Code</label>
                            <input type="text" id="bgColorCode" class="form-control">
                        </div>
                    </div>
                        {{-- Label Product --}}
                    <div class="row mt-2">
                        <div class="col-md-6">

                            <label>Label Product</label>
                            <input type="text"  name="label_product" class="form-control">
                        </div>
                    </div> 
                    {{-- Gallery --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gallery Images</label>
                                <input type="file"
                                id="galleryInput"
                                name="gallery[]"
                                class="form-control"
                                multiple
                               
                                onchange="previewGallery(this)">     
                               <div id="galleryPreview" class="d-flex flex-row flex-wrap mt-2"></div>
                               
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-info">Back</a>
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
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

        $('#categorySelect').on('change', function() {
            let categoryId = $(this).val();

            $('#subcategorySelect').html('<option value="">Loading...</option>');

            if (!categoryId) {
                $('#subcategorySelect').html('<option value="">Select Subcategory</option>');
                return;
            }

            $.ajax({
                url: "{{ route('admin.get.subcategories', ':id') }}".replace(':id', categoryId),
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

    });


let galleryFiles = new DataTransfer();

/* PREVIEW */
function previewGallery(input) {

    if (!input || !input.files) return;

    Array.from(input.files).forEach(file => {

        if (!file.type.startsWith('image/')) return;

        galleryFiles.items.add(file);

        let reader = new FileReader();
        reader.onload = function (e) {
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
</script>


@endpush