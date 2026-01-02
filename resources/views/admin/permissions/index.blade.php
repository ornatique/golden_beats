@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Permissions</h3>

    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary mb-3">Create Permission</a>

    <table class="table table-bordered">
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
@endsection
