@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Custom Orders</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered" id="customOrdersTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Customer Nmae</th>
                <th>Image</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@push('scripts')

<script>
$('#customOrdersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.custom-orders.data') }}",
    columns: [
        { data: 'DT_RowIndex', orderable:false, searchable:false },
        { data: 'user_name', name: 'user_name' },
        { data: 'image', orderable:false, searchable:false },
        { data: 'remarks' },
        { data: 'status', orderable:false },
        { data: 'created_at' },
        { data: 'action', orderable:false, searchable:false },
    ]
});

function deleteOrder(id){
    if(!confirm('Delete this order?')) return;

    $.ajax({
        url: "{{ url('admin/custom-orders') }}/"+id,
        type: "DELETE",
        data: {_token:"{{ csrf_token() }}"},
        success: function(res){
            $('#customOrdersTable').DataTable().ajax.reload();
        }
    });
}

$(document).on('change', '.order-status', function () {

    let status = $(this).val();
    let id = $(this).data('id');

    $.ajax({
        url: "{{ url('admin/custom-orders') }}/" + id + "/status",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            status: status
        },
        success: function (res) {
            toastr.success(res.message);
        },
        error: function () {
            toastr.error('Status update failed');
        }
    });
});
</script>

@endpush