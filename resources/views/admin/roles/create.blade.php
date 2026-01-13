@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Role</h3>
            </div>

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="card-body">

                    {{-- ROLE NAME --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       required>
                            </div>
                        </div>
                    </div>

                    {{-- GLOBAL SELECT ALL --}}
                    <div class="mb-3">
                        <label class="fw-bold">
                            <input type="checkbox" id="select-all-permissions">
                            Select All Permissions
                        </label>
                    </div>

                    {{-- PERMISSION GROUPS --}}
                    <div class="row">
                        @foreach($permissions as $module => $perms)
                        <div class="col-md-4 col-sm-6">
                            <div class="card card-outline card-primary mb-3">

                                {{-- GROUP HEADER --}}
                                <div class="card-header py-2">
                                    <label class="mb-0">
                                        <input type="checkbox"
                                               class="group-select"
                                               data-group="{{ $module }}">
                                        <strong class="text-uppercase">
                                            {{ ucfirst($module) }}
                                        </strong>
                                    </label>
                                </div>

                                {{-- GROUP BODY --}}
                                <div class="card-body p-2">
                                    @foreach($perms as $permission)
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input permission-checkbox"
                                               data-group="{{ $module }}"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               id="{{ $permission->name }}">

                                        <label class="form-check-label"
                                               for="{{ $permission->name }}">
                                            {{ str_replace($module.'-', '', $permission->name) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="card-footer text-right">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select-all-permissions');
    const permissions = document.querySelectorAll('.permission-checkbox');
    const groupSelects = document.querySelectorAll('.group-select');

    // 🔹 GLOBAL SELECT ALL
    selectAll.addEventListener('change', function () {
        permissions.forEach(cb => cb.checked = this.checked);
        groupSelects.forEach(gs => gs.checked = this.checked);
    });

    // 🔹 GROUP SELECT ALL
    groupSelects.forEach(group => {
        group.addEventListener('change', function () {
            const groupName = this.dataset.group;
            document
                .querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`)
                .forEach(cb => cb.checked = this.checked);
            syncAll();
        });
    });

    // 🔹 AUTO SYNC
    permissions.forEach(cb => cb.addEventListener('change', syncAll));

    function syncAll() {
        selectAll.checked = [...permissions].every(cb => cb.checked);

        groupSelects.forEach(group => {
            const groupName = group.dataset.group;
            const groupPerms = document.querySelectorAll(
                `.permission-checkbox[data-group="${groupName}"]`
            );
            group.checked = [...groupPerms].every(cb => cb.checked);
        });
    }
});
</script>
@endpush
