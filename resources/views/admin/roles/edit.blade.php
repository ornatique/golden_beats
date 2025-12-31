@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Role</h3>

    <form method="POST" action="{{ route('admin.roles.update',$role->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Role Name</label>
            <input type="text" name="name" value="{{ $role->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Permissions</label><br>
            @foreach($permissions as $permission)
                <label>
                    <input type="checkbox" name="permissions[]"
                        value="{{ $permission->name }}"
                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                    {{ $permission->name }}
                </label><br>
            @endforeach
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
