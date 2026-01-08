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
                <th>User</th>
                <th>Image</th>
                <th>Description</th>
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
        { data: 'user_name' },
        { data: 'image', orderable:false, searchable:false },
        { data: 'description' },
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
</script>
@endpush