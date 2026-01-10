@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header text-right">
        <h3 class="card-title">Reels</h3>
        <a href="{{ route('admin.reels.create') }}"
           class="btn btn-success">Add Reel</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="reelsTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Media</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Likes & Comments</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="commentsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Reel Comments</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Comment</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="commentsBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function formatDateTime(dateStr) {
    if (!dateStr) return '-';

    const d = new Date(dateStr);

    return d.toLocaleString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}

$(function () {
    $('#reelsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.reels.data') }}",
        columns: [
            {data:'DT_RowIndex', orderable:false, searchable:false},
            {data:'name'},
            {data:'media', orderable:false, searchable:false},
            {data:'category'},
            {data:'subcategory'},
            {data:'stats', orderable:false, searchable:false},
            {data:'action', orderable:false, searchable:false}
        ]
    });
});
</script>
<script>
function openComments(reelId) {

    $('#commentsBody').html('<tr><td colspan="3">Loading...</td></tr>');
    $('#commentsModal').modal('show');

    $.get("{{ url('admin/reels') }}/" + reelId + "/comments", function (comments) {

        let html = '';

        comments.forEach(c => {
            html += `
                <tr data-id="${c.id}">
                    <td>${c.user?.name ?? 'User'}</td>
                    <td>
                        <input type="text"
                               class="form-control comment-input"
                               value="${c.comment}">
                    </td>
                     <td>${formatDateTime(c.created_at)}</td>
                    <td>
                        <button class="btn btn-success btn-sm"
                            onclick="updateComment(${c.id}, this)">
                            Update
                        </button>

                        <button class="btn btn-danger btn-sm"
                            onclick="deleteComment(${c.id}, this)">
                            Delete
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#commentsBody').html(html);
    });
}

function updateComment(commentId, btn) {

    const row = $(btn).closest('tr');
    const comment = row.find('.comment-input').val();

    if (!comment.trim()) {
        alert('Comment cannot be empty');
        return;
    }

    $.ajax({
        url: "{{ url('admin/reel-comments') }}/" + commentId,
        type: "PUT",
        data: {
            _token: "{{ csrf_token() }}",
            comment: comment
        },
        success: function (response) {
            alert('Comment updated successfully');
            $('#commentsModal').modal('hide'); // ✅ now correctly placed
        },
        error: function () {
            alert('Failed to update comment');
        }
    });
}


function deleteComment(commentId, btn) {

    if (!confirm('Delete comment?')) return;

    $.ajax({
        url: "{{ url('admin/reel-comments') }}/" + commentId,
        method: "DELETE",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: () => {
            $(btn).closest('tr').remove();
        }
    });
}
</script>

@endpush
