@extends('layouts.admin')

@section('content')
<div class="card">

    <div class="card-header">
        <h3>List Products</h3>
        @can('product-create')
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary float-right">Add Product</a>
        @endcan
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
                @can('product-pdf-with-details')
                <button class="btn btn-success" id="printSelectedwithdetails">
                    Download PDF With Details
                </button>
                @endcan
                @can('product-pdf-download')
                <button class="btn btn-success" id="printSelected">
                    Download PDF
                </button>
                @endcan
                @can('product-print-code')
                <button class="btn btn-success" onclick="printQrPdf()">
                    Print Selected QR
                </button>
                @endcan
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Product Images</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                <img id="galleryModalImage"
                    src=""
                    class="img-fluid rounded"
                    style="max-height:500px;">
            </div>

            <div class="modal-footer justify-content-between">
                <button class="btn btn-secondary" id="prevImage">⬅ Prev</button>
                <button class="btn btn-secondary" id="nextImage">Next ➡</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let selectedProducts = [];

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

    /* HEADER CHECK ALL */
    $('#checkAll').on('change', function() {

        if (this.checked) {

            $.get("{{ route('admin.products.allIds') }}", {
                category_id: $('#categoryFilter').val(),
                subcategory_id: $('#subcategoryFilter').val()
            }, function(ids) {

                selectedProducts = ids.map(String);
                $('.product-check').prop('checked', true);
            });

        } else {
            selectedProducts = [];
            $('.product-check').prop('checked', false);
        }
    });

    /* SINGLE CHECKBOX */
    $(document).on('change', '.product-check', function() {

        let id = this.value;

        if (this.checked) {
            if (!selectedProducts.includes(id)) selectedProducts.push(id);
        } else {
            selectedProducts = selectedProducts.filter(x => x !== id);
            $('#checkAll').prop('checked', false);
        }
    });

    function resetSelection() {
        selectedProducts = [];
        $('#checkAll').prop('checked', false);
        $('.product-check').prop('checked', false);
    }
    /* KEEP CHECKBOX STATE */
    table.on('draw', function() {
        $('.product-check').each(function() {
            $(this).prop('checked', selectedProducts.includes(this.value));
        });
    });

    /* BULK PDF */
    $('#printSelected').click(function() {

        if (!selectedProducts.length) {
            alert('Select at least one product');
            return;
        }

        $('<form>', {
                method: 'POST',
                action: "{{ route('admin.products.bulk.pdf') }}"
            })
            .append('@csrf')
            .append(selectedProducts.map(id => `<input type="hidden" name="product_ids[]" value="${id}">`))
            .append(`<input type="hidden" name="category_id" value="${$('#categoryFilter').val()}">`)
            .append(`<input type="hidden" name="subcategory_id" value="${$('#subcategoryFilter').val()}">`)
            .appendTo('body')
            .submit();
        resetSelection();
    });

    /* BULK PDF WITH DETAILS */
    $('#printSelectedwithdetails').click(function() {

        if (!selectedProducts.length) {
            alert('Select at least one product');
            return;
        }

        $('<form>', {
                method: 'POST',
                action: "{{ route('admin.products-details.bulk.pdf') }}"
            })
            .append('@csrf')
            .append(selectedProducts.map(id => `<input type="hidden" name="product_ids[]" value="${id}">`))
            .append(`<input type="hidden" name="category_id" value="${$('#categoryFilter').val()}">`)
            .append(`<input type="hidden" name="subcategory_id" value="${$('#subcategoryFilter').val()}">`)
            .appendTo('body')
            .submit();
        resetSelection();
    });

    /* PRINT QR */
    function printQrPdf() {

        if (!selectedProducts.length) {
            alert('Please select at least one product');
            return;
        }

        let url = "{{ route('admin.products.print.qrcode') }}" +
            "?product_ids=" + selectedProducts.join(',');

        let win = window.open(url, '_blank');
        win.onload = function() {
            win.print();
        };
        resetSelection();
    }
  

    let galleryImages = [];
    let currentIndex = 0;

    $(document).on('click', '.gallery-thumb', function() {

        galleryImages = JSON.parse($(this).attr('data-images'));
        console.log(galleryImages)
        currentIndex = parseInt($(this).attr('data-index'));

        showGalleryImage();
        $('#galleryModal').modal('show');
    });

    function showGalleryImage() {
        if (!galleryImages.length) return;

        let img = galleryImages[currentIndex];
        console.log('Image file:', img);
        $('#galleryModalImage').attr(
            'src',
            '{{ asset("uploads/products") }}/' + img
        );
    }

    $('#nextImage').on('click', function() {
        if (currentIndex < galleryImages.length - 1) {
            currentIndex++;
            showGalleryImage();
        }
    });

    $('#prevImage').on('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            showGalleryImage();
        }
    });
</script>

@endpush