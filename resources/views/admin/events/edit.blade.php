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
                {{-- CURRENT IMAGE --}}
                @if($event->image)
                <div class="form-group">
                    <label>Current Image</label><br>
                    <img src="{{ asset($event->image) }}"
                        width="120"
                        style="border:1px solid #ddd">
                </div>
                @endif

                {{-- CHANGE IMAGE --}}
                <div class="form-group">
                    <label>Change Image</label>
                    <input type="file"
                        name="image"
                        class="form-control"
                        onchange="previewImage(this)">
                    <img id="imagePreview"
                        style="display:none;margin-top:10px;width:120px;border:1px solid #ddd">
                </div>

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