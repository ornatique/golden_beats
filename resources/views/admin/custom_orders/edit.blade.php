@extends('layouts.admin')

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

            {{-- Remarks --}}
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks"
                          class="form-control">{{ $customOrder->remarks }}</textarea>
            </div>

            {{-- Existing Image --}}
            @if($customOrder->image)
            <div class="form-group">
                <label>Current Image</label><br>

                <img src="{{ asset($customOrder->image) }}"
                     class="img-thumbnail"
                     style="width:120px;height:auto;cursor:pointer"
                     data-toggle="modal"
                     data-target="#imagePreviewModal">
            </div>
            @endif

            {{-- Upload New Image --}}
            <div class="form-group">
                <label>Change Image</label>
                <input type="file" name="image" class="form-control">
            </div>

        </div>

        <div class="card-footer text-right">
            <a href="{{ route('admin.custom-orders.index') }}"
               class="btn btn-secondary">Back</a>
            <button class="btn btn-primary">submit</button>
            
        </div>
    </form>
</div>

{{-- IMAGE PREVIEW MODAL --}}
@if($customOrder->image)
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <img src="{{ asset($customOrder->image) }}"
                     class="img-fluid"
                     style="max-height:600px">
            </div>

        </div>
    </div>
</div>
@endif
@endsection
<script>
document.querySelector('input[name="image"]').addEventListener('change', function(e){
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(){
        document.querySelector('#imagePreviewModal img').src = reader.result;
        $('#imagePreviewModal').modal('show');
    };
    reader.readAsDataURL(file);
});
</script>
