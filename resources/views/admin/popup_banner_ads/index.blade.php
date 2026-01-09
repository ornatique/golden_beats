@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Popup Banner Ads</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="popupAdsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
$('#popupAdsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.popup-banner-ads.data') }}",
    columns: [
        { data: 'DT_RowIndex', orderable:false, searchable:false },
        { data: 'image', orderable:false, searchable:false },
        { data: 'title', name: 'title' },
        { data: 'status', orderable:false },
        { data: 'action', orderable:false, searchable:false },
    ]
});
</script>
@endpush