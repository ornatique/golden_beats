@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 >Events List</h3>
        @can('event-create')
        <a href="{{ route('admin.events.create') }}"
           class="btn btn-primary float-right">
            <i class="fas fa-plus"></i> Add Event
        </a>
        @endcan
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped" id="eventsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Event Type</th>
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
    $('#eventsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.events.data') }}",

        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image', orderable: false, searchable: false },
            { data: 'title' },
            { data: 'event_date' },
            { data: 'location' },
             { data: 'event_type' },
            { data: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush
