@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Edit Subcategory</h3>
            </div>

            <form method="POST"
                  id="subcategoryForm"
                  action="{{ route('admin.subcategories.update', $subcategory->id) }}"
                  enctype="multipart/form-data"
                  data-mode="edit">
                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- Category + Name --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category_id"
                                        class="form-control @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('category_id', $subcategory->category_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $subcategory->name) }}"
                                       placeholder="Enter subcategory name">
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Priority + Color --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Priority <span class="text-danger">*</span></label>
                                <input type="number"
                                       name="priority"
                                       class="form-control @error('priority') is-invalid @enderror"
                                       value="{{ old('priority', $subcategory->priority) }}">
                                @error('priority')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Select Color</label>
                                <input type="color"
                                       name="color"
                                       id="colorPicker"
                                       class="form-control"
                                       style="height:38px;"
                                       value="{{ old('color', $subcategory->color ?? '#000000') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Color Code</label>
                                <input type="text"
                                       id="colorCodeValue"
                                       class="form-control"
                                       value="{{ old('color', $subcategory->color ?? '#000000') }}"
                                       placeholder="#000000">
                            </div>
                        </div>
                    </div>

                    {{-- Image --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategory Image</label>
                                <input type="file"
                                       name="image"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*"
                                       onchange="previewSubcategoryImage(this)">
                                @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <div class="mt-2">
                                    @if($subcategory->image)
                                        <img src="{{ asset($subcategory->image) }}"
                                             id="imagePreview"
                                             style="width:100px;border-radius:6px;border:1px solid #ddd;">
                                    @else
                                        <img id="imagePreview"
                                             style="display:none;width:100px;border-radius:6px;border:1px solid #ddd;">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="card-footer text-right">
                    <a href="{{ route('admin.subcategories.index') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-warning">
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
/* ================= COLOR PICKER SYNC ================= */
$(document).ready(function () {

    let initialColor = $('#colorPicker').val() || '#000000';
    $('#colorPicker').val(initialColor);
    $('#colorCodeValue').val(initialColor);

    $('#colorPicker').on('input change', function () {
        $('#colorCodeValue').val($(this).val());
    });

    $('#colorCodeValue').on('input', function () {
        let color = $(this).val().trim();
        if (/^#([0-9A-Fa-f]{6})$/.test(color)) {
            $('#colorPicker').val(color);
        }
    });

});

/* ================= IMAGE PREVIEW ================= */
function previewSubcategoryImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#imagePreview')
                .attr('src', e.target.result)
                .show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ================= CLIENT VALIDATION (EDIT SAFE) ================= */
$(document).ready(function () {

    let isEdit = $('#subcategoryForm').data('mode') === 'edit';

    $('#subcategoryForm').validate({
        rules: {
            category_id: {
                required: true
            },
            name: {
                required: true,
                minlength: 2
            },
            priority: {
                required: true,
                digits: true,
                min: 0
            },
            image: {
                extension: "jpg|jpeg|png|webp|gif"
            }
        },

        messages: {
            category_id: "Please select category",
            name: "Please enter subcategory name",
            priority: "Please enter valid priority",
            image: "Only image files allowed"
        },

        errorElement: 'span',
        errorClass: 'invalid-feedback',

        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },

        submitHandler: function (form) {
            form.submit();
        }
    });

});
</script>
@endpush
