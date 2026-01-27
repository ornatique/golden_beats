@extends('layouts.admin')

@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Event</h3>
            </div>

            <form method="POST"
                action="{{ route('admin.events.update',$event->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title *</label>
                                <input type="text"
                                    name="title"
                                    value="{{ old('title',$event->title) }}"
                                    class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- EVENT DATE --}}
                            <div class="form-group">
                                <label>Event Date *</label>
                                <input type="datetime-local"
                                    name="event_date"
                                    value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}"
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
                                <label>Location *</label>
                                <input type="text"
                                    name="location"
                                    value="{{ old('location',$event->location) }}"
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
                                <input type="url"
                                    name="map_link"
                                    value="{{ old('map_link',$event->map_link) }}"
                                    class="form-control">
                            </div>

                        </div>

                        <div class="col-md-6">
                            {{-- DESCRIPTION --}}
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description"
                                    class="form-control"
                                    rows="4">{{ old('description',$event->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                    {{-- CURRENT IMAGES --}}
                    @if(!empty($event->image))
                    @php
                    $eventImages = is_array($event->image) ? $event->image : json_decode($event->image, true);
                    @endphp

                    <label>Current Images</label>
                    <div class="gallery-grid" id="existingGallery" style="display: flex !important;">
                        @foreach($eventImages as $img)
                        <div class="gallery-item" data-name="{{ $img }}">
                            <span class="remove-img" onclick="removeExistingImage('{{ $img }}')">&times;</span>
                            <img src="{{ asset('uploads/events/'.$img) }}">
                        </div>
                        
                        @endforeach
                    </div>
                    @endif

                    {{-- ADD MORE IMAGES --}}
                    <label class="mt-3">Add More Images</label>
                    <input type="file"
                        id="galleryInput"
                        name="image[]"
                        multiple
                        class="form-control"
                        onchange="previewGallery(this)">

                    <div class="gallery-grid mt-2" id="galleryPreview"  style="display: flex !important;"></div>

                    <input type="hidden" name="removed_images" id="removedImages">

                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                    <button class="btn btn-primary">
                        Update
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection
@push('styles')
<style>
.gallery-grid{
    display: flex !important;
}
 .image-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.image-box {
    position: relative;
    width: 110px;
    height: 110px;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background: #f9f9f9;
}

.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/*  Remove icon */
.remove-img {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 22px;
    height: 22px;
    background: #e53935;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 22px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10;
}

</style>

@endpush


@push('scripts')
<script>
    let galleryFiles = new DataTransfer();
    let removedImages = [];

    function previewGallery(input) {
        Array.from(input.files).forEach(file => {

            if (!file.type.startsWith('image/')) return;

            galleryFiles.items.add(file);

            const reader = new FileReader();
            reader.onload = e => {
                $('#galleryPreview').append(`
                <div class="gallery-item" data-name="${file.name}">
                    <span class="remove-img" onclick="removeNewImage('${file.name}')">&times;</span>
                    <img src="${e.target.result}">
                </div>
            `);
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('galleryInput').files = galleryFiles.files;
    }

    function removeNewImage(fileName) {
        let newFiles = new DataTransfer();

        Array.from(galleryFiles.files).forEach(file => {
            if (file.name !== fileName) newFiles.items.add(file);
        });

        galleryFiles = newFiles;
        document.getElementById('galleryInput').files = galleryFiles.files;

        $(`.gallery-item[data-name="${fileName}"]`).remove();
    }

    function removeExistingImage(fileName) {
        removedImages.push(fileName);
        $('#removedImages').val(JSON.stringify(removedImages));
        $(`.gallery-item[data-name="${fileName}"]`).remove();
    }
</script>
@endpush