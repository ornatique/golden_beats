@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Custom Notifications</h3>

        @can('Push-Notification-create')
        <a href="{{ route('admin.custom-notifications.create') }}"
           class="btn btn-primary mb-2 float-right">
            + Add Custom Notification
        </a>
        @endcan
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped" id="notificationTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>User</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {

    $('#notificationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.custom-notifications.data') }}",

        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'title', name: 'title' },
            { data: 'category', name: 'category.name' },
            { data: 'subcategory', name: 'subcategory.name' },
            { data: 'user', name: 'user.name' },
            {
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

});

/* DELETE HANDLER */
function deleteNotification(id) {
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }

    $.ajax({
        url: "{{ route('admin.custom-notifications.destroy', ':id') }}".replace(':id', id),
        type: 'DELETE',
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            $('#notificationTable').DataTable().ajax.reload();
        },
        error: function () {
            alert('Something went wrong!');
        }
    });
}
</script>
@endpush
