@extends('layouts.admin')
@section('content')
<section class="content">
    <div class="container-fluid">

       

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                             Edit Role
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            {{-- ROLE NAME --}}
                            <div class="form-group col-md-6 p-0">
                                <label>
                                    Role Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $role->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required>

                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <hr>

                            {{-- GLOBAL SELECT ALL --}}
                            <div class="mb-3">
                                <label class="font-weight-bold">
                                    <input type="checkbox" id="select-all">
                                    Select All Permissions
                                </label>
                            </div>

                            <hr>

                            {{-- PERMISSIONS GROUPED --}}
                            <div class="row">
                                @foreach($permissions->groupBy(fn($p) => explode('-', $p->name)[0]) as $group => $groupPermissions)

                                    <div class="col-md-4 mb-3">
                                        <div class="card card-outline card-secondary h-100">
                                            <div class="card-header py-2">
                                                <label class="mb-0">
                                                    <input type="checkbox"
                                                           class="group-select"
                                                           data-group="{{ $group }}">
                                                    {{ strtoupper($group) }}
                                                </label>
                                            </div>

                                            <div class="card-body p-2">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                               class="form-check-input permission-checkbox"
                                                               data-group="{{ $group }}"
                                                               name="permissions[]"
                                                               value="{{ $permission->name }}"
                                                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>

                                                        <label class="form-check-label">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>

                        </div>

                        <div class="card-footer text-right">
                            <a href="{{ route('admin.roles.index') }}"
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

                            <button class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Role
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

    const selectAll = document.getElementById('select-all');
    const permissions = document.querySelectorAll('.permission-checkbox');
    const groupSelects = document.querySelectorAll('.group-select');

    // GLOBAL SELECT ALL
    selectAll.addEventListener('change', function () {
        permissions.forEach(cb => cb.checked = this.checked);
        groupSelects.forEach(gs => gs.checked = this.checked);
    });

    // GROUP SELECT
    groupSelects.forEach(group => {
        group.addEventListener('change', function () {
            const groupName = this.dataset.group;
            document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`)
                .forEach(cb => cb.checked = this.checked);
        });
    });

    // AUTO CHECK SELECTS ON LOAD
    function syncCheckboxes() {
        selectAll.checked = [...permissions].every(cb => cb.checked);

        groupSelects.forEach(group => {
            const groupName = group.dataset.group;
            const groupPerms = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
            group.checked = [...groupPerms].every(cb => cb.checked);
        });
    }

    permissions.forEach(cb => cb.addEventListener('change', syncCheckboxes));
    syncCheckboxes();
});
</script>
@endpush
