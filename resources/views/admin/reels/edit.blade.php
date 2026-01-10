@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Reel</h3>
    </div>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('admin.reels.update',$reel->id) }}">
        @csrf
        @method('PUT')

        <div class="card-body">

            {{-- NAME --}}
            <div class="form-group">
                <label>Name</label>
                <input type="text"
                    name="name"
                    value="{{ $reel->name }}"
                    class="form-control"
                    required>
            </div>

            {{-- DESCRIPTION --}}
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"
                    class="form-control">{{ $reel->description }}</textarea>
            </div>

            {{-- CATEGORY --}}
            <div class="form-group">
                <label>Category</label>
                <select name="category_id"
                    id="category"
                    class="form-control">
                    <option value="">Select</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}"
                        {{ $reel->category_id == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- SUBCATEGORY --}}
            <div class="form-group">
                <label>Subcategory</label>
                <select name="subcategory_id"
                    id="subcategory"
                    class="form-control">
                    <option value="">Select Subcategory</option>
                </select>
            </div>
            {{-- CHANGE THUMBNAIL IMAGE --}}
            <div class="form-group">
                <label>Change Thumbnail Image</label>

                <input type="file"
                    name="image"
                    id="imageInput"
                    class="form-control"
                    accept="image/*">

                {{-- Preview new image --}}
                <img id="imagePreview"
                    style="display:none;margin-top:10px;max-width:300px;border:1px solid #ddd">
            </div>

            <div class="row">
                <div class="col-md-6">
                    @if($reel->image)
                    <div class="form-group">
                        <label>Current Thumbnail</label><br>
                        <img src="{{ asset($reel->image) }}"
                            style="width:300px;height:200px;border:1px solid #ddd">
                    </div>
                    @endif

                </div>
                <div class="col-md-6">
                    @if($reel->media_file)
                    <div class="form-group">
                        <label>Current Video</label><br>
                        <video width="300" height="200" controls>
                            <source src="{{ asset($reel->media_file) }}" type="video/mp4">
                        </video>
                    </div>
                    @endif
                </div>

            </div>
            {{-- IMAGE UPLOAD + PREVIEW --}}

            {{-- EXISTING VIDEO --}}


            {{-- VIDEO UPLOAD (DRAG & DROP) --}}
            <div class="form-group">
                <label>Replace Video</label>

                <div id="dropZone"
                    style="border:2px dashed #aaa;padding:25px;text-align:center;cursor:pointer">
                    <p>Drag & drop video here or click</p>
                    <input type="file"
                        id="videoInput"
                        name="media_file"
                        accept="video/mp4,video/mov,video/avi"
                        hidden>
                </div>

                <video id="videoPreview"
                    controls
                    style="display:none;margin-top:10px;max-width:300px">
                </video>
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.reels.index') }}"
                class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {

        $('select[name="category_id"]').on('change', function() {

            let categoryId = $(this).val();
            let subcategory = $('#subcategory');

            subcategory.html('<option value="">Loading...</option>');

            if (!categoryId) {
                subcategory.html('<option value="">Select Subcategory</option>');
                return;
            }

            $.ajax({
                url: "{{ url('admin/get-subcategories') }}/" + categoryId,
                type: "GET",
                success: function(data) {

                    let options = '<option value="">Select Subcategory</option>';

                    data.forEach(function(item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });

                    subcategory.html(options);
                },
                error: function() {
                    subcategory.html('<option value="">Error loading</option>');
                }
            });
        });

    });
    $(document).ready(function() {

        /* IMAGE PREVIEW */
        $('#imageInput').on('change', function(e) {
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) {
                $(this).val('');
                $('#imagePreview').hide();
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        });

        /* VIDEO PREVIEW + DURATION + SIZE */
        const MAX_DURATION = 30;
        const MAX_SIZE = 50 * 1024 * 1024;

        function previewVideo(file) {
            if (!file || !file.type.startsWith('video/')) {
                alert('Only video files allowed');
                $('#videoInput').val('');
                $('#videoPreview').hide();
                return;
            }

            if (file.size > MAX_SIZE) {
                alert('Max video size is 50MB');
                $('#videoInput').val('');
                return;
            }

            const video = document.createElement('video');
            video.preload = 'metadata';
            video.onloadedmetadata = function() {
                if (video.duration > MAX_DURATION) {
                    alert('Video must be 30 seconds or less');
                    $('#videoInput').val('');
                    $('#videoPreview').hide();
                }
            };
            video.src = URL.createObjectURL(file);

            $('#videoPreview').attr('src', video.src).show();
        }

        /* DRAG & DROP */
        const dropZone = document.getElementById('dropZone');
        const videoInput = document.getElementById('videoInput');

        dropZone.addEventListener('click', () => videoInput.click());
        dropZone.addEventListener('dragover', e => e.preventDefault());
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            videoInput.files = e.dataTransfer.files;
            previewVideo(e.dataTransfer.files[0]);
        });

        videoInput.addEventListener('change', e => previewVideo(e.target.files[0]));

    });
</script>

@endpush
@push('scripts')
<script>
    $(document).ready(function() {

        const categorySelect = $('select[name="category_id"]');
        const subcategorySelect = $('#subcategory');

        const selectedCategory = "{{ $reel->category_id ?? '' }}";
        const selectedSubcategory = "{{ $reel->subcategory_id ?? '' }}";

        function loadSubcategories(categoryId, selectedSubcat = null) {

            if (!categoryId) {
                subcategorySelect.html('<option value="">Select Subcategory</option>');
                return;
            }

            $.ajax({
                url: "{{ url('admin/get-subcategories') }}/" + categoryId,
                type: "GET",
                success: function(data) {

                    let options = '<option value="">Select Subcategory</option>';

                    data.forEach(function(item) {
                        options += `<option value="${item.id}"
                        ${selectedSubcat == item.id ? 'selected' : ''}>
                        ${item.name}
                    </option>`;
                    });

                    subcategorySelect.html(options);
                }
            });
        }

        // 🔥 EDIT PAGE: auto-load on page load
        if (selectedCategory) {
            loadSubcategories(selectedCategory, selectedSubcategory);
        }

        // 🔁 CREATE + EDIT: on category change
        categorySelect.on('change', function() {
            loadSubcategories($(this).val());
        });

    });
</script>
@endpush