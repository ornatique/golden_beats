@extends('layouts.admin')

@section('content')
<div class="card">
     <div class="card-header">
            <h3>Permission</h3>
        </div>
    <div class="card-body">

    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary mb-3 float-right">Create Permission</a>

    <table class="table table-bordered" id="permissionsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Permission Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $permission->name }}</td>
                <td>
                    <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                    class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                        method="POST"
                        style="display:inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this permission?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-danger">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    $('#permissionsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        lengthChange: true,
        pageLength: 10,
        order: [[0, 'asc']], // sort by permission name
    });
});
</script>
@endpush

