@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>List Categories</h3>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-2 float-right">
            Add Category
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="category-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Priority</th>
                    <th>Home</th>
                    <th>Color</th>
                    <th>Shape</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#category-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.categories.data') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'name'
            },
            {
                data: 'image',
                orderable: false,
                searchable: false
            },
            {
                data: 'priority'
            },
            {
                data: 'home',
                orderable: false
            },
            {
                data: 'color'
            },
            {
                data: 'shape'
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });
</script>
@endpush