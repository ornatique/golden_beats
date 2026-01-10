@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Add Reel</h3>
    </div>

    <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('admin.reels.store') }}">
        @csrf

        <div class="card-body">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name"
                    class="form-control" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"
                    class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id"
                    class="form-control">
                    <option value="">Select</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Subcategory</label>
                <select name="subcategory_id"
                    id="subcategory"
                    class="form-control">
                    <option value="">Select Subcategory</option>
                </select>
            </div>

            <div class="form-group">
                <label>Thumbnail Image</label>
                <input type="file" name="image"
                    id="imageInput"
                    class="form-control"
                    accept="image/*">

                <img id="imagePreview"
                    src=""
                    style="display:none;margin-top:10px;max-width:200px;border:1px solid #ddd">
            </div>


            <div class="form-group">
                <label>Video File</label>
                <input type="file" name="media_file"
                    id="videoInput"
                    class="form-control"
                    accept="video/mp4,video/mov,video/avi"
                    required>

                <video id="videoPreview"
                    controls
                    style="display:none;margin-top:10px;max-width:300px;border:1px solid #ddd">
                </video>
            </div>


        </div>

        <div class="card-footer text-right">
             <a href="{{ route('admin.reels.index') }}"
                class="btn btn-secondary">Back</a>
            <button class="btn btn-primary">Save</button>
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
</script>
@endpush
@push('scripts')
<script>
$(document).ready(function () {

    // IMAGE PREVIEW
    $('#imageInput').on('change', function (e) {

        const file = e.target.files[0];
        const preview = $('#imagePreview');

        if (!file) {
            preview.hide();
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image');
            $(this).val('');
            preview.hide();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            preview.attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    });


    // VIDEO PREVIEW (ONLY VIDEO)
    $('#videoInput').on('change', function (e) {

        const file = e.target.files[0];
        const preview = $('#videoPreview');

        if (!file) {
            preview.hide();
            return;
        }

        if (!file.type.startsWith('video/')) {
            alert('Only video files are allowed');
            $(this).val('');
            preview.hide();
            return;
        }

        const url = URL.createObjectURL(file);
        preview.attr('src', url).show();
    });

});
</script>
@endpush
<script>
const MAX_DURATION = 30; // seconds
const MAX_VIDEO_SIZE = 50 * 1024 * 1024; // 50MB

$('#videoInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    // ❌ Size check
    if (file.size > MAX_VIDEO_SIZE) {
        alert('Video size must be less than 50MB');
        this.value = '';
        return;
    }

    // ❌ Duration check
    const video = document.createElement('video');
    video.preload = 'metadata';

    video.onloadedmetadata = function () {
        window.URL.revokeObjectURL(video.src);

        if (video.duration > MAX_DURATION) {
            alert('Video duration must be 30 seconds or less');
            $('#videoInput').val('');
            $('#videoPreview').hide();
        }
    };

    video.src = URL.createObjectURL(file);
});
</script>

