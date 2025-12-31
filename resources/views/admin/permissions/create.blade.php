@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>Create Permission</h3>

    <form method="POST" action="{{ route('admin.permissions.store') }}">
        @csrf

        <div class="mb-3">
            <label>Permission Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
