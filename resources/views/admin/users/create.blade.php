@extends('layouts.admin')

@section('content')
<h3>Create User</h3>

<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<select name="role" class="form-control mb-2">
    @foreach($roles as $role)
        <option value="{{ $role->name }}">{{ $role->name }}</option>
    @endforeach
</select>

<button class="btn btn-success">Save</button>
</form>
@endsection
