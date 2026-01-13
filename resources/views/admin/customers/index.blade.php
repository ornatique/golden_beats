@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        @php
        $only = request('only'); // customer | user | null
        @endphp
        <h3> Customers List</h3>

        @can('customers-create')
        <div class="float-right">
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-2 ">
                Create Customer
            </a>
        </div>
        @endcan


    </div>
    <div class="card-body">
        <div class="row mb-2 ">
            <div class="col-md-3 text-right">
                <select id="statusFilter" class="form-control">
                    <option value="">Select Customers</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>
                        Active Customers
                    </option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>
                        New Customers
                    </option>
                </select>
            </div>
        </div>
        <table class="table table-bordered" id="customers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Profile Image</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Reg. Date&time</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@push('scripts')
@php
$buttons = [];

if(auth()->user()->can('customer-export')) {
    $buttons[] = [
        'extend' => 'excelHtml5',
        'text' => 'Export Excel',
        'className' => 'btn btn-primary',
        'title' => 'Customer List',
        'exportOptions' => [
            'columns' => [0,1,3,4,5,6,7,8]
        ],
    ];
}
@endphp
<script>
    $(document).ready(function() {

        let params = new URLSearchParams(window.location.search);
       let customersTable = $('#customers-table').DataTable({

    processing: true,
    serverSide: true,

    dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"B>>frtip',

    buttons: {!! json_encode($buttons) !!},

    ajax: {
        url: "{{ route('admin.customers.data') }}",
        data: function (d) {
            d.only   = new URLSearchParams(window.location.search).get('only');
            d.status = new URLSearchParams(window.location.search).get('status');
        }
    },

    order: [[0, 'asc']],

    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'profile_image', name: 'profile_image', orderable: false, searchable: false },
        { data: 'email', name: 'email' },
        { data: 'number', name: 'contact' },
        { data: 'state', name: 'state' },
        { data: 'city', name: 'city' },
        { data: 'reg_date', name: 'reg_date' },
        { data: 'role', name: 'role' },
        { data: 'status', name: 'status', orderable: false, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ]
});


    });
</script>
<script>
    $(document).on('change', '.toggle-status', function() {

        let userId = $(this).data('id');
        let status = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.customers.status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: userId,
                status: status
            },
            success: function(response) {
                toastr.success(response.message ?? 'Status updated successfully');
                customersTable.ajax.reload(null, false);
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    $('#statusFilter').on('change', function() {

        let status = $(this).val();
        let url = new URL(window.location.href);

        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }

        window.history.pushState({}, '', url);
        $('#customers-table').DataTable().ajax.reload();
    });
</script>


@endpush