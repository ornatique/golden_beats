@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>List Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary float-right">Add Product</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="productTable">
            <thead>
            <tr>
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
@endsection

@push('scripts')
<script>
$('#productTable').DataTable({
    processing:true,
    serverSide:true,
    ajax:"{{ route('admin.products.data') }}",
    columns:[
        {data:'DT_RowIndex',orderable:false,searchable:false},
        {data:'name'},
        {data:'qr'},
        {data:'category'},
        {data:'subcategory'},
        {data:'gallery',orderable:false,searchable:false},
        {data:'quantity'},
        {data:'action',orderable:false,searchable:false},
    ]
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
        success: function (res) {
            toastr.success(res.message);
            $('#productsTable').DataTable().ajax.reload(null, false);
        },
        error: function () {
            toastr.error('Something went wrong!');
        }
    });
}
</script>

@endpush
