@extends('layouts.admin')

@section('content')
<h3>Users List</h3>

@can('user-create')
<div class="float-right">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-2 ">
    Create User
</a>
</div>
@endcan

@can('user-export')
<div class="float-right">
<a href="{{ route('admin.users.export.excel') }}" class="btn btn-success float-right">
    Export Excel
</a>
</div>
@endcan


<table class="table table-bordered" id="users-table">
    <thead>
        <tr>
             <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
@endsection
@push('scripts')

<script>
$(document).ready(function () {


    $('#users-table').DataTable({
        processing: true,
        serverSide: true,
       
        ajax: "{{ route('admin.users.data') }}",
        order: [[0, 'asc']],
        columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'role', name: 'role', orderable: true, searchable: true },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    
    });

});
</script>
@endpush



