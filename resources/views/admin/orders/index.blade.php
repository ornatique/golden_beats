@extends('layouts.admin')
@section('content')
<div class="card">
     <div class="card-header">
            <h3>List Orders</h3>
        </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="ordersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estimate ID</th>
                    <th>Customer Name</th>
                    <th>Qty</th>
                    <th>Weight</th>
                    <th>Status</th>
                    <th>Remarks</th>
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
    $(function() {
        $('#ordersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.orders.data') }}",

            columns: [
        { data: 'DT_RowIndex', orderable:false, searchable:false },
        { data: 'order_id', name: 'order_id' },
        { data: 'user_name', name: 'user_name' }, // 🔥 FIX HERE
        { data: 'quantity', name: 'quantity' },
        { data: 'weight', name: 'weight' },
        { data: 'status', orderable:false, searchable:false },
        { data: 'remarks', name: 'remarks' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', orderable:false, searchable:false },
    ]

        });
    });
    // update status 
    $(document).on('change', '.order-status', function() {

        let status = $(this).val();
        let orderId = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.orders.update-status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: orderId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function() {
                toastr.error('Something went wrong');
            }
        });
    });

function deleteOrder(id) {
    if (!confirm('Are you sure you want to delete this order?')) return;

    $.ajax({
        url: "{{ url('admin/orders') }}/" + id,
        type: "DELETE",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if (res.success) {
                toastr.success(res.message);
                $('#ordersTable').DataTable().ajax.reload(null, false);
            } else {
                toastr.error(res.message);
            }
        },
        error: function () {
            toastr.error('Something went wrong');
        }
    });
}
</script>

@endpush