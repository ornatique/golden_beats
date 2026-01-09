@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Banner Ads</h3>
        <a href="{{ route('admin.banner-ads.create') }}"
           class="btn btn-success float-right">Add Banner</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="bannerAdsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category Name</th>
                    <th>Subcategory Name</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#bannerAdsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.banner-ads.data') }}",
    columns: [
    {
        data: 'DT_RowIndex',
        name: 'DT_RowIndex',
        orderable: false,
        searchable: false
    },
    { data: 'image', name: 'image', orderable: false, searchable: false },
    { data: 'product', name: 'product.name' },
    { data: 'category', name: 'category.name' },
    { data: 'subcategory', name: 'subcategory.name' },
    { data: 'action', name: 'action', orderable: false, searchable: false }
]


});

function deleteBannerAd(id) {
    if (!confirm('Are you sure you want to delete this banner ad?')) return;

    $.ajax({
        url: '{{ url("admin/banner-ads") }}/' + id,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {
            $('#bannerAdsTable').DataTable().ajax.reload();
        },
        error: function () {
            alert('Something went wrong!');
        }
    });
}
</script>

@endpush
