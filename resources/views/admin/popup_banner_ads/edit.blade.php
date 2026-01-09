@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Popup Banner</h3>
    </div>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('admin.popup-banner-ads.update',$popupBannerAd->id) }}">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text"
                            name="title"
                            value="{{ $popupBannerAd->title }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Active" @selected($popupBannerAd->status=='Active')>Active</option>
                            <option value="Inactive" @selected($popupBannerAd->status=='Inactive')>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            @if($popupBannerAd->image)
            <div class="form-group">
                <label>Current Image</label><br>
                <img src="{{ asset($popupBannerAd->image) }}" width="150">
            </div>
            @endif

            <div class="form-group">
                <label>Change Image</label>
                <input type="file" name="image" class="form-control">
            </div>

        </div>

        <div class="card-footer text-right">
           
            <a href="{{ route('admin.popup-banner-ads.index') }}"
                class="btn btn-secondary">Back</a>
              <button class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection