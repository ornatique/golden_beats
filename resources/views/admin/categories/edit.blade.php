@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Category</h3>
            </div>

            <form method="POST"
                  action="{{ route('admin.categories.update', $category->id) }}"
                  enctype="multipart/form-data" id="categoryForm" data-mode="edit">
                @csrf
                @method('PUT')

                <div class="card-body">

                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name',$category->name) }}">
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Priority</label>
                                <input type="number"
                                       name="priority"
                                       class="form-control"
                                       value="{{ old('priority',$category->priority) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Shape -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shape</label>
                                <select name="shape"
                                        id="shapeSelect"
                                        class="form-control">
                                    <option value="">Select Shape</option>
                                    <option value="circle" {{ $category->shape=='circle'?'selected':'' }}>Circle</option>
                                    <option value="rectangle" {{ $category->shape=='rectangle'?'selected':'' }}>Rectangle</option>
                                    <option value="circle_with_border" {{ $category->shape=='circle_with_border'?'selected':'' }}>Circle With Border</option>
                                    <option value="rectangle_with_border" {{ $category->shape=='rectangle_with_border'?'selected':'' }}>Rectangle With Border</option>
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
                                       value="{{  $category->color }}"
                                       class="form-control @error('color') is-invalid @enderror"
                                       style="height:38px;">
                                @error('color')
                                <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Color Code</label>
                                <input type="text"
                                       id="colorCodeValue"
                                       value="{{  $category->color }}"
                                       class="form-control"
                                       >
                                      
                                     
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
                                       onchange="previewCategoryImage(event)">
                                <div class="mt-2">
                                    @if($category->image)
                                        <img src="{{ asset($category->image) }}"
                                             id="imagePreview"
                                             style="width:100px;border-radius:6px;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Home -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Show on Home</label>
                                <div class="custom-control custom-switch">
                                    <!-- This ensures OFF state is sent -->
                                    <input type="hidden" name="home" value="0">

                                    <input type="checkbox"
                                        name="home"
                                        value="1"
                                        class="custom-control-input"
                                        id="homeSwitch"
                                        {{ old('home', $category->home) ? 'checked' : '' }}>

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
                    <button class="btn btn-warning">
                        <i class="fas fa-save"></i> Update
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

    // 🔹 Two-way binding
    $('#colorPicker').on('input', function () {
        $('#colorCodeValue').val($(this).val());
    });

    $('#colorCodeValue').on('input', function () {
        let color = $(this).val();
        if (/^#([0-9A-Fa-f]{6})$/.test(color)) {
            $('#colorPicker').val(color);
        }
    });

});

function previewCategoryImage(event) {
    let img = document.getElementById('imagePreview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = 'block';
}

$(document).ready(function () {
let isEdit = $('#categoryForm').data('mode') === 'edit';
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
                
                  required: function () {
                return !isEdit;   // ✅ ONLY REQUIRED ON CREATE
            },
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
</script>
@endpush
