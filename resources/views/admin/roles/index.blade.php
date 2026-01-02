@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Roles</h3>

    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3">Create Role</a>

    <table class="table table-bordered">
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
                    <?php  if ($role->name != "admin") { ?>
                   <form action="{{ route('admin.roles.destroy', $role->id) }}"
                    method="POST"
                    style="display:inline-block"
                    onsubmit="return confirm('Are you sure you want to delete this role?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                   <?php }?>
                </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
