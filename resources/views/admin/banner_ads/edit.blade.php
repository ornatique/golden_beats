@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Banner Ad</h3>
    </div>

    <form id="bannerForm"
          method="POST"
          enctype="multipart/form-data"
          action="{{ route('admin.banner-ads.update',$bannerAd->id) }}">
        @csrf
        @method('PUT')

        <div class="card-body">

            {{-- CATEGORY --}}
            <div class="form-group">
                <label>Category</label>
               <select name="category_id"
                        id="category"
                        class="form-control"
                        data-subcategory-url="{{ url('admin/get-subcategories') }}"
                        data-product-url="{{ url('admin/get-products') }}">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @selected($bannerAd->category_id==$cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SUBCATEGORY --}}
            <div class="form-group">
                <label>Subcategory</label>
                <select name="subcategory_id" id="subcategory"
                        class="form-control" required>
                    @foreach($subcategories as $sub)
                        <option value="{{ $sub->id }}"
                            @selected($bannerAd->subcategory_id==$sub->id)>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PRODUCT --}}
            <div class="form-group">
                <label>Product</label>
                <select name="product_id" id="product"
                        class="form-control" required>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}"
                            @selected($bannerAd->product_id==$prod->id)>
                            {{ $prod->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- IMAGE --}}
            @if($bannerAd->image)
                <img src="{{ asset($bannerAd->image) }}" width="150"><br><br>
            @endif

            <input type="file" name="image" class="form-control">

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.banner-ads.index') }}"
               class="btn btn-secondary">Back</a>
            <button class="btn btn-primary">Update</button>
          
        </div>
    </form>
</div>
@endsection
