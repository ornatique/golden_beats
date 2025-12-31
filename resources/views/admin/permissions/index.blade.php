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
                    <a href="{{ route('admin.permissions.edit',$permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
