@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>List Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary float-right">Add Product</a>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="categoryFilter" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $id=>$name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select id="subcategoryFilter" class="form-control">
                    <option value="">All Subcategories</option>
                </select>
            </div>

            <div class="col-md-6 text-right">
                <button class="btn btn-success" id="printSelectedwithdetails">
                    Download PDF With Details
                </button>
                <button class="btn btn-success" id="printSelected">
                    Download PDF
                </button>
                <button class="btn btn-success" onclick="printQrPdf()">
                    Print Selected QR
                </button>
            </div>
        </div>
        <table class="table table-bordered" id="productTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>#</th>
                    <th>Name</th>
                    <th>QR</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Gallery</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-body d-flex align-items-center justify-content-center position-relative">

                <!-- Left Button -->
                <button id="prevImg"
                    class="slider-btn slider-left">
                    ❮
                </button>

                <!-- Image Container -->
                <div class="slider-image-wrapper">
                    <img id="sliderImage" src="">
                </div>

                <!-- Right Button -->
                <button id="nextImg"
                    class="slider-btn slider-right">
                    ❯
                </button>

            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    let table = $('#productTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.products.data') }}",
            data: function(d) {
                d.category_id = $('#categoryFilter').val();
                d.subcategory_id = $('#subcategoryFilter').val();
            }
        },
        columns: [{
                data: 'checkbox',
                orderable: false,
                searchable: false
            },
            {
                data: 'DT_RowIndex',
                orderable: false
            },
            {
                data: 'name'
            },
            {
                data: 'qr',
                orderable: false,
                searchable: false
            },
            {
                data: 'category'
            },
            {
                data: 'subcategory'
            },
            {
                data: 'gallery',
                orderable: false
            },
            {
                data: 'quantity'
            },
            {
                data: 'action',
                orderable: false
            },
        ]
    });

    /* FILTER */
    $('#categoryFilter').change(function() {
        let id = $(this).val();
        $('#subcategoryFilter').html('<option>Loading...</option>');

        if (!id) {
            $('#subcategoryFilter').html('<option value="">All Subcategories</option>');
            table.ajax.reload();
            return;
        }

        $.get("{{ route('admin.get.subcategories_data',':id') }}".replace(':id', id), function(res) {
            let opt = '<option value="">All Subcategories</option>';
            $.each(res, function(i, v) {
                opt += `<option value="${i}">${v}</option>`
            });
            $('#subcategoryFilter').html(opt);
            table.ajax.reload();
        });
    });

    $('#subcategoryFilter').change(() => table.ajax.reload());

    /* CHECKBOX */
    $('#checkAll').on('change', function() {
        $('.product-check').prop('checked', this.checked);
    });

    /* BULK PDF */
    $('#printSelected').click(function() {
        let ids = $('.product-check:checked').map(function() {
            return this.value
        }).get();

        if (ids.length === 0) {
            alert('Select at least one product');
            return;
        }

        $('<form>', {
                method: 'POST',
                action: "{{ route('admin.products.bulk.pdf') }}"
            })
            .append('@csrf')
            .append(ids.map(id => `<input type="hidden" name="product_ids[]" value="${id}">`))
            .append(`<input type="hidden" name="category_id" value="${$('#categoryFilter').val()}">`)
            .append(`<input type="hidden" name="subcategory_id" value="${$('#subcategoryFilter').val()}">`)
            .appendTo('body')
            .submit();
    });
// bulk pdf with details
     $('#printSelectedwithdetails').click(function() {
        let ids = $('.product-check:checked').map(function() {
            return this.value
        }).get();

        if (ids.length === 0) {
            alert('Select at least one product');
            return;
        }

        $('<form>', {
                method: 'POST',
                action: "{{ route('admin.products-details.bulk.pdf') }}"
            })
            .append('@csrf')
            .append(ids.map(id => `<input type="hidden" name="product_ids[]" value="${id}">`))
            .append(`<input type="hidden" name="category_id" value="${$('#categoryFilter').val()}">`)
            .append(`<input type="hidden" name="subcategory_id" value="${$('#subcategoryFilter').val()}">`)
            .appendTo('body')
            .submit();
    });

    function deleteProduct(id) {

        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        $.ajax({
            url: "{{ route('admin.products.destroy', ':id') }}".replace(':id', id),
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                toastr.success(res.message);
                $('#productsTable').DataTable().ajax.reload(null, false);
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    }

    function getSelectedProductIds() {
        let ids = [];
        $('.product-check:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    // Select All
    $('#selectAll').on('change', function() {
        $('.product-check').prop('checked', this.checked);
    });

    function printQrPdf() {
        let ids = getSelectedProductIds();

        if (ids.length === 0) {
            alert('Please select at least one product');
            return;
        }

        let url = "{{ route('admin.products.print.qrcode') }}" +
            "?product_ids=" + ids.join(',');

        let win = window.open(url, '_blank');

        win.onload = function() {
            win.focus();
            win.print(); // 🔥 auto print
        };
    }
// list of product view gallary on popup
    let images = [];
    let currentIndex = 0;

    $(document).on('click', '.gallery-thumb', function() {
        images = $(this).data('images');
        currentIndex = 0;
        showImage();

        new bootstrap.Modal(document.getElementById('galleryModal')).show();
    });

    function showImage() {
        $('#sliderImage').attr('src', '/' + images[currentIndex]);
    }

    $('#nextImg').on('click', function() {
        currentIndex = (currentIndex + 1) % images.length;
        showImage();
    });

    $('#prevImg').on('click', function() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showImage();
    });
</script>
@endpush