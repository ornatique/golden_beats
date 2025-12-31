@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Edit Permission</h3>

    <form method="POST" action="{{ route('admin.permissions.update',$permission->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Permission Name</label>
            <input type="text" name="name" value="{{ $permission->name }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
