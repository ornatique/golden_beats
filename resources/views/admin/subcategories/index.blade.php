@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 >List Subcategories</h3>
         @can('subcategory-create')
        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary float-right">
            Add Sub-Category
        </a>
         @endcan
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="subcategoryTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Priority</th>
                    <th>Color</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#subcategoryTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.subcategories.data') }}",
    columns: [
        { data: 'DT_RowIndex', orderable:false, searchable:false },
        { data: 'category' },
        { data: 'name' },
        { data: 'image', orderable:false, searchable:false },
        { data: 'priority' },
        { data: 'color' },
        { data: 'action', orderable:false, searchable:false }
    ]
});
</script>
@endpush
