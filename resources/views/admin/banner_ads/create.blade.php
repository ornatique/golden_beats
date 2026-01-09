@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add Banner Ad</h3>
    </div>

    <form id="bannerForm"
        method="POST"
        enctype="multipart/form-data"
        novalidate
        action="{{ route('admin.banner-ads.store') }}">
        @csrf

        <div class="card-body">

            {{-- CATEGORY --}}
            <div class="form-group">
                <label for="category">Category</label>
                <select name="category_id"
                        id="category"
                        class="form-control"
                        data-subcategory-url="{{ url('admin/get-subcategories') }}"
                        data-product-url="{{ url('admin/get-products') }}">


                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- SUBCATEGORY --}}
            <div class="form-group">
                <label for="product">Select SubCategory</label>
            <select name="subcategory_id" id="subcategory" class="form-control">
                <option value="">Select Subcategory</option>
            </select>
             </div>

            {{-- PRODUCT --}}
            <div class="form-group">
                <label for="product">Product</label>
                <select name="product_id" id="product" class="form-control">
                    <option value="">Select Product</option>
                </select>
            </div>

            {{-- IMAGE --}}
            <div class="form-group">
                <label for="image">Banner Image</label>
                <input type="file"
                    name="image"
                    id="image"
                    class="form-control">
            </div>

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.banner-ads.index') }}"
                class="btn btn-secondary">
                Back
            </a>

            <button type="submit"
                class="btn btn-primary">
                Submit
            </button>
        </div>
    </form>
</div>

@endsection
@section('js')
<script>
    $(function() {

        $('#bannerForm').validate({

            ignore: [],

            rules: {
                category_id: {
                    required: true
                },
                subcategory_id: {
                    required: true
                },
                product_id: {
                    required: true
                },
                image: {
                    required: true,
                    extension: "jpg|jpeg|png|webp|gif"
                }
            },

            messages: {
                category_id: "Please select category",
                subcategory_id: "Please select subcategory",
                product_id: "Please select product",
                image: {
                    required: "Please upload banner image",
                    extension: "Only JPG, JPEG, PNG, WEBP, GIF allowed"
                }
            },

            errorElement: 'span',

            // 🔥 THIS IS THE FIX
            errorClass: 'invalid-feedback d-block',

            errorPlacement: function(error, element) {
                error.appendTo(element.closest('.form-group'));
            },

            highlight: function(element) {
                $(element).addClass('is-invalid');
            },

            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },

            submitHandler: function(form) {
                form.submit();
            }
        });

    });
</script>

@endsection