@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Roles</h3>
    </div>
    <div class="card-body">

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3 float-right">Create Role</a>

        <table class="table table-bordered" id="rolesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $role->name }}</td>
                    <td>
                        @foreach($role->permissions as $permission)
                        <span class="badge bg-info">{{ $permission->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.roles.edit',$role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <?php if ($role->name != "admin") { ?>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                method="POST"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            <?php } ?>
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
    $(document).ready(function() {
        $('#rolesTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            lengthChange: true,
            pageLength: 10,
            order: [
                [0, 'asc']
            ], // sort by permission name
        });
    });
</script>
@endpush