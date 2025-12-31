@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Create Role</h3>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf

        <div class="mb-3">
            <label>Role Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Permissions</label><br>
            @foreach($permissions as $permission)
                <label>
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                    {{ $permission->name }}
                </label><br>
            @endforeach
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
