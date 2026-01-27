@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Event</h3>
            </div>

            <form method="POST"
                action="{{ route('admin.events.store') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- TITLE --}}
                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6">
                            {{-- EVENT DATE --}}
                            <div class="form-group">
                                <label>Event Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    name="event_date"
                                    value="{{ old('event_date') }}"
                                    class="form-control @error('event_date') is-invalid @enderror">
                                @error('event_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">

                            {{-- LOCATION --}}
                            <div class="form-group">
                                <label>Location <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="location"
                                    value="{{ old('location') }}"
                                    class="form-control @error('location') is-invalid @enderror">
                                @error('location')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6">
                            {{-- EVENT TYPE --}}
                            <div class="form-group">
                                <label>Event Type <span class="text-danger">*</span></label>

                                <select name="event_type"
                                    class="form-control @error('event_type') is-invalid @enderror">

                                    <option value="">Select Event Type</option>

                                    <option value="upcoming"
                                        {{ old('event_type', $event->event_type ?? '') == 'upcoming' ? 'selected' : '' }}>
                                        Upcoming Event
                                    </option>

                                    <option value="live"
                                        {{ old('event_type', $event->event_type ?? '') == 'live' ? 'selected' : '' }}>
                                        Live Event
                                    </option>

                                    <option value="completed"
                                        {{ old('event_type', $event->event_type ?? '') == 'completed' ? 'selected' : '' }}>
                                        Completed Event
                                    </option>
                                </select>

                                @error('event_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            {{-- MAP LINK --}}
                            <div class="form-group">
                                <label>Map Link</label>
                                <input type="text"
                                    name="map_link"
                                    value="{{ old('map_link') }}"
                                    class="form-control @error('map_link') is-invalid @enderror">
                                @error('map_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6">
                            {{-- DESCRIPTION --}}
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>
                    </div>
                    {{-- IMAGE --}}
                    <div class="form-group">
                        <label>Event Image</label>
                        <input type="file"
                            id="galleryInput"
                            name="image[]"
                            class="form-control @error('image') is-invalid @enderror"
                            accept="image/*"
                            multiple
                            onchange="previewGallery(this)">
                        @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror

                        <img id="imagePreview"
                            style="display:none;margin-top:10px;width:120px;border:1px solid #ddd">
                    </div>
                    <div id="galleryPreview" class="d-flex flex-wrap mt-2"></div>

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection
@push('styles')
<style>
    .gallery-item {
        position: relative;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .gallery-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .remove-img {
        position: absolute;
        top: -6px;
        right: -6px;
        background: red;
        color: #fff;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        text-align: center;
        cursor: pointer;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }


    let galleryFiles = new DataTransfer();

    function previewGallery(input) {

        if (!input.files) return;

        Array.from(input.files).forEach(file => {

            if (!file.type.startsWith('image/')) return;

            galleryFiles.items.add(file);

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#galleryPreview').append(`
                <div class="gallery-item" data-name="${file.name}">
                    <span class="remove-img" onclick="removeGalleryImage('${file.name}')">&times;</span>
                    <img src="${e.target.result}">
                </div>
            `);
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('galleryInput').files = galleryFiles.files;
    }

    function removeGalleryImage(fileName) {

        let newFiles = new DataTransfer();

        Array.from(galleryFiles.files).forEach(file => {
            if (file.name !== fileName) {
                newFiles.items.add(file);
            }
        });

        galleryFiles = newFiles;
        document.getElementById('galleryInput').files = galleryFiles.files;

        $(`.gallery-item[data-name="${fileName}"]`).remove();
    }
</script>


@endpush