@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Product</h3>
            </div>

            <form method="POST"
                  action="{{ route('admin.products.update', $product->id) }}"
                  enctype="multipart/form-data"
                  id="productForm">
                @csrf
                @method('PUT')

                <div class="card-body">

                    {{-- Category + Subcategory --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control" id="categorySelect">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id=>$name)
                                        <option value="{{ $id }}"
                                            {{ $product->category_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subcategory <span class="text-danger">*</span></label>
                                <select name="subcategory_id" class="form-control" id="subcategorySelect">
                                    <option value="">Select Subcategory</option>
                                    @foreach($subcategories as $id=>$name)
                                        <option value="{{ $id }}"
                                            {{ $product->subcategory_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Name + Number --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name',$product->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label>Number</label>
                            <input type="text" name="number" class="form-control"
                                   value="{{ old('number',$product->number) }}">
                        </div>
                    </div>

                    {{-- Size + Hole --}}
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Size</label>
                            <input type="text" name="size" class="form-control"
                                   value="{{ old('size',$product->size) }}">
                        </div>
                        <div class="col-md-6">
                            <label>Hole Size</label>
                            <input type="text" name="hole_size" class="form-control"
                                   value="{{ old('hole_size',$product->hole_size) }}">
                        </div>
                    </div>

                    {{-- Weights --}}
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>Gross Weight</label>
                            <input type="number" step="0.001" name="gross_weight" class="form-control"
                                   value="{{ old('gross_weight',$product->gross_weight) }}">
                        </div>
                        <div class="col-md-4">
                            <label>Less Weight</label>
                            <input type="number" step="0.001" name="less_weight" class="form-control"
                                   value="{{ old('less_weight',$product->less_weight) }}">
                        </div>
                        <div class="col-md-4">
                            <label>Net Weight</label>
                            <input type="number" step="0.001" name="weight" class="form-control"
                                   value="{{ old('weight',$product->weight) }}">
                        </div>
                    </div>

                    {{-- Quantity + Charge --}}
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control"
                                   value="{{ old('quantity',$product->quantity) }}">
                        </div>
                        <div class="col-md-6">
                            <label>Other Charges</label>
                            <input type="number" step="0.01" name="charge" class="form-control"
                                   value="{{ old('charge',$product->charge) }}">
                        </div>
                    </div>

                    {{-- Color + BG Color --}}
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label>Color</label>
                            <input type="color" name="color" id="colorPicker"
                                   class="form-control"
                                   value="{{ old('color',$product->color ?? '#000000') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Color Code</label>
                            <input type="text" id="colorCode" class="form-control"
                                   value="{{ old('color',$product->color ?? '#000000') }}">
                        </div>

                        <div class="col-md-3">
                            <label>BG Color</label>
                            <input type="color" name="bg_color" id="bgColorPicker"
                                   class="form-control"
                                   value="{{ old('bg_color',$product->bg_color ?? '#000000') }}">
                        </div>
                        <div class="col-md-3">
                            <label>BG Color Code</label>
                            <input type="text" id="bgColorCode" class="form-control"
                                   value="{{ old('bg_color',$product->bg_color ?? '#000000') }}">
                        </div>
                    </div>

                    {{-- Label --}}
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Label Product</label>
                            <input type="text" name="label_product" class="form-control"
                                   value="{{ old('label_product',$product->label_product) }}">
                        </div>
                    </div>

                    {{-- Existing Gallery --}}
                    <div class="row mt-3">
                    <div class="col-md-12">
                        <label>Existing Images</label>

                        <div class="d-flex flex-wrap" id="existingGallery">
                            @foreach(($product->gallery ?? []) as $img)
                                <div class="gallery-item" data-old="{{ $img }}">
                                    <span class="remove-img"
                                        onclick="removeOldImage('{{ $img }}')">&times;</span>

                                    <img src="{{ asset('uploads/products/'.$img) }}">

                                    {{-- 🔥 THIS IS IMPORTANT --}}
                                    <input type="hidden" name="old_gallery[]" value="{{ $img }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>



                    {{-- New Gallery --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label>Add New Images</label>
                            <input type="file"
                                   id="galleryInput"
                                   name="gallery[]"
                                   class="form-control"
                                   multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp"
                                   onchange="previewGallery(this)">
                            <div id="galleryPreview" class="d-flex flex-wrap mt-2"></div>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-info">Back</a>
                    <button class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection
@push('scripts')
<script>
/* ================= COLOR SYNC ================= */
$('#colorPicker').on('input',()=>$('#colorCode').val($('#colorPicker').val()));
$('#colorCode').on('input',function(){
    if(/^#([0-9A-Fa-f]{6})$/.test(this.value)){
        $('#colorPicker').val(this.value);
    }
});

$('#bgColorPicker').on('input',()=>$('#bgColorCode').val($('#bgColorPicker').val()));
$('#bgColorCode').on('input',function(){
    if(/^#([0-9A-Fa-f]{6})$/.test(this.value)){
        $('#bgColorPicker').val(this.value);
    }
});

/* ================= CATEGORY → SUBCATEGORY ================= */
$('#categorySelect').on('change',function(){
    let id=$(this).val();
    $('#subcategorySelect').html('<option>Loading...</option>');
    if(!id) return;

    $.get("{{ route('admin.get.subcategories',':id') }}".replace(':id',id),
        function(data){
            let opt='<option value="">Select Subcategory</option>';
            $.each(data,(i,v)=>opt+=`<option value="${i}">${v}</option>`);
            $('#subcategorySelect').html(opt);
        });
});

/* ================= OLD IMAGE REMOVE ================= */
function removeOldImage(path){
    $(`.gallery-item[data-old="${path}"]`).remove();
}

/* ================= NEW GALLERY PREVIEW ================= */
let galleryFiles=new DataTransfer();

function previewGallery(input){
    Array.from(input.files).forEach(file=>{
        if(!file.type.startsWith('image/')) return;

        galleryFiles.items.add(file);
        let r=new FileReader();
        r.onload=e=>{
            $('#galleryPreview').append(`
                <div class="gallery-item" data-new="${file.name}">
                    <span class="remove-img"
                          onclick="removeNewImage('${file.name}')">&times;</span>
                    <img src="${e.target.result}">
                </div>
            `);
        };
        r.readAsDataURL(file);
    });
    document.getElementById('galleryInput').files=galleryFiles.files;
}

/* ================= REMOVE NEW IMAGE ================= */
function removeNewImage(name){
    let dt=new DataTransfer();
    Array.from(galleryFiles.files).forEach(f=>{
        if(f.name!==name) dt.items.add(f);
    });
    galleryFiles=dt;
    document.getElementById('galleryInput').files=galleryFiles.files;
    $(`.gallery-item[data-new="${name}"]`).remove();
}
function removeOldImage(imgPath) {

    // Remove preview
    $(`.gallery-item[data-old="${imgPath}"]`).remove();

    // Remove hidden input so it won't be saved
    $(`input[name="old_gallery[]"][value="${imgPath}"]`).remove();
}

</script>

<style>
.gallery-item{position:relative;margin:6px}
.gallery-item img{
    width:90px;height:90px;object-fit:cover;
    border:1px solid #ddd;border-radius:6px
}
.remove-img{
    position:absolute;top:-6px;right:-6px;
    width:22px;height:22px;border-radius:50%;
    background:#dc3545;color:#fff;
    text-align:center;line-height:20px;
    font-weight:bold;cursor:pointer
}
</style>
@endpush
