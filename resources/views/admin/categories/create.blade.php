@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Category</h3>
            </div>

            <form method="POST" id="categoryForm"
                  action="{{ route('admin.categories.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="card-body">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       placeholder="Enter category name"
                                       value="{{ old('name') }}">
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Priority</label>
                                <input type="number"
                                       name="priority"
                                       class="form-control"
                                       placeholder="Priority"
                                       value="{{ old('priority', 0) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Shape -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shape <span class="text-danger">*</span></label>
                                <select name="shape"
                                        id="shapeSelect"
                                        class="form-control">
                                    <option value="">Select Shape</option>
                                    <option value="circle">Circle</option>
                                    <option value="rectangle">Rectangle</option>
                                    <option value="circle_with_border">Circle With Border</option>
                                    <option value="rectangle_with_border">Rectangle With Border</option>
                                </select>
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Select Color</label>
                                <input type="color"
                                    name="color"
                                    id="colorPicker"
                                    class="form-control"
                                    style="height:38px;"
                                    value="#000000">
                            </div>
                        </div>

                        <!-- Color Code -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Color Code</label>
                                <input type="text"
                                    id="colorCodeValue"
                                    class="form-control"
                                    value="#000000"
                                    placeholder="#000000">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Image -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category Image</label>
                                <input type="file"
                                    name="image"
                                    class="form-control"
                                    accept="image/*"
                                    onchange="previewCategoryImage(this)">
                                <div class="mt-2">
                                    <img id="imagePreview"
                                        style="display:none;width:100px;border-radius:6px;border:1px solid #ddd;">
                                </div>
                            </div>
                        </div>


                        <!-- Home -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Show on Home</label>
                                <div class="custom-control custom-switch">
                                  
                                    <input type="checkbox"
                                           name="home"
                                           value="1"
                                           class="custom-control-input"
                                           id="homeSwitch">
                                        <label class="custom-control-label" for="homeSwitch">
                                        Yes
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>

$(document).ready(function () {

    // 🔹 DEFAULT COLOR ON PAGE LOAD
    let defaultColor = '#000000';
    $('#colorPicker').val(defaultColor);
    $('#colorCodeValue').val(defaultColor);

    // 🔹 Shape change (OPTIONAL – if you want color always visible, remove this)
    $('#shapeSelect').on('change', function () {
        let shape = $(this).val();

        if (shape) {
            $('#colorWrapper').slideDown();
        } else {
            $('#colorWrapper').slideUp();
            resetColor();
        }
    });

    function resetColor() {
        $('#colorPicker').val(defaultColor);
        $('#colorCodeValue').val(defaultColor);
    }

    // 🔹 Color Picker → Textbox
    $('#colorPicker').on('input', function () {
        $('#colorCodeValue').val($(this).val());
    });

    // 🔹 Textbox → Color Picker
    $('#colorCodeValue').on('input', function () {
        let color = $(this).val();

        // Validate HEX color
        if (/^#([0-9A-Fa-f]{6})$/.test(color)) {
            $('#colorPicker').val(color);
        }
    });

});



$(document).ready(function () {

    $('#categoryForm').validate({
        ignore: [],

        rules: {
            name: {
                required: true,
                minlength: 2
            },
            priority: {
                 required: true,
                digits: true,
                min: 0
            },
            shape: {
                required: true
            },
            color: {
                 required: true,
                required: function () {
                    return $('#shapeSelect').val() !== '';
                }
            },
            image: {
                 required: true,
                extension: "jpg|jpeg|png|webp|gif"
            }
        },

        messages: {
            name: {
                required: "Please enter category name",
                minlength: "Name must be at least 2 characters"
            },
            priority: {
                digits: "Only numbers allowed",
                min: "Priority must be 0 or greater"
            },
            shape: {
                required: "Please select a shape"
            },
            color: {
                required: "Please select a color"
            },
            image: {
                extension: "Only JPG, JPEG, PNG, WEBP images allowed"
            }
        },

        errorElement: 'span',
        errorClass: 'invalid-feedback',

        errorPlacement: function (error, element) {
            if (element.attr("type") === "color") {
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },

        highlight: function (element) {
            $(element).addClass('is-invalid');
        },

        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },

        submitHandler: function (form) {
            form.submit(); // ✅ allow submit
        }
    });

    // 🔁 Re-validate color when shape changes
    $('#shapeSelect').on('change', function () {
        $('#colorPicker').valid();
    });

});

function previewCategoryImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('imagePreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>




@endpush
