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
           

            <!-- Select All -->
            <label class="fw-bold">
                <input type="checkbox" id="select-all-permissions">
                Select All
            </label>
            <hr>

            @foreach($permissions as $permission)
                <label>
                    <input type="checkbox"
                           class="permission-checkbox"
                           name="permissions[]"
                           value="{{ $permission->name }}">
                    {{ $permission->name }}
                </label><br>
            @endforeach
        </div>

        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select-all-permissions');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    // Select All click
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    // Individual checkbox click
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) {
                selectAll.checked = false;
            } else {
                selectAll.checked = [...checkboxes].every(c => c.checked);
            }
        });
    });

});
</script>
