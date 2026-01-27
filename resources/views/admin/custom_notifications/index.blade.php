@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Custom Notifications</h3>

        @can('PushNotification-Create')
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
                    <th>Description</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage"
                     src=""
                     class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openImageModal(imageUrl) {
    $('#previewImage').attr('src', imageUrl);
    $('#imagePreviewModal').modal('show');
}
function resendnotification(id) {
    if (!confirm('Resend this notification?')) return;

    $.post(
        "{{ route('admin.custom-notifications.resend', ':id') }}".replace(':id', id),
        { _token: '{{ csrf_token() }}' },
        function (res) {
            alert(res.message);
        }
    );
}

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
            { data: 'description', name: 'description' },
            { data: 'title', name: 'title' },
            { data: 'category', name: 'category.name' },
            { data: 'subcategory', name: 'subcategory.name' },
            
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
