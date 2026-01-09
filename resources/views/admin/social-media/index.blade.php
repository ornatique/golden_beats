@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Social Media Links</h3>
    </div>

    <form method="POST" action="{{ route('admin.social-media.update') }}">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="facebook"
                       class="form-control"
                       value="{{ old('facebook', $social->facebook) }}">
            </div>

            <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="twitter"
                       class="form-control"
                       value="{{ old('twitter', $social->twitter) }}">
            </div>

            <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="instagram"
                       class="form-control"
                       value="{{ old('instagram', $social->instagram) }}">
            </div>

            <div class="form-group">
                <label>LinkedIn</label>
                <input type="text" name="linkedin"
                       class="form-control"
                       value="{{ old('linkedin', $social->linkedin) }}">
            </div>

            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp"
                       class="form-control"
                       value="{{ old('whatsapp', $social->whatsapp) }}">
            </div>

            <div class="form-group">
                <label>YouTube</label>
                <input type="text" name="youtube"
                       class="form-control"
                       value="{{ old('youtube', $social->youtube) }}">
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection
