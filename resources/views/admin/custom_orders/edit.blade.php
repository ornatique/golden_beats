@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Custom Order</h3>
    </div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ route('admin.custom-orders.update',$customOrder->id) }}">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    @foreach(['Pending','Approval','Done'] as $s)
                        <option value="{{ $s }}"
                            @selected($customOrder->status==$s)>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"
                    class="form-control">{{ $customOrder->description }}</textarea>
            </div>

            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks"
                    class="form-control">{{ $customOrder->remarks }}</textarea>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

        </div>

        <div class="card-footer">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
