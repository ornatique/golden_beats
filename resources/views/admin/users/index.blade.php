@extends('layouts.admin')

@section('content')
@php
    $only = request('only'); // customer | user | null
@endphp
<h3> {{ $only === 'customer' ? 'Customer List' : 'User List' }}</h3>

@can('user-create')
<div class="float-right">
    <a href="{{ route('admin.users.create', request()->query()) }}" class="btn btn-primary mb-2 ">
    Create  {{ $only === 'customer' ? 'Customer ' : 'User' }}
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
            <th>Profile Image</th>    
            <th>Email</th>
            <th>Contact</th>
            <th>State</th>
            <th>City</th>
            <th>Reg. Date&time</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
@endsection
@push('scripts')

<script>
$(document).ready(function () {

    let params = new URLSearchParams(window.location.search);
    let usersTable = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
       
         ajax: {
            url: "{{ route('admin.users.data') }}",
            data: function (d) {
                d.only = new URLSearchParams(window.location.search).get('only');
                d.status = new URLSearchParams(window.location.search).get('status');
            }
        },
        order: [[0, 'asc']],
        columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'profile_image', name: 'profile_image' },
        { data: 'email', name: 'email' },
        { data: 'number', name: 'contact' },
        { data: 'state', name: 'state' },
        { data: 'city', name: 'city' },
        { data: 'reg_date', name: 'reg_date' },
        { data: 'role', name: 'role', orderable: true, searchable: true },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
    ],
    
    });

});
</script>
<script>
$(document).on('change', '.toggle-status', function () {

    let userId = $(this).data('id');
    let status = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        url: "{{ route('admin.users.status') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: userId,
            status: status
        },
        success: function (response) {
            toastr.success(response.message ?? 'Status updated successfully');
            usersTable.ajax.reload(null, false); 
        },
        error: function () {
            toastr.error('Something went wrong!');
        }
    });
});
</script>

@endpush



