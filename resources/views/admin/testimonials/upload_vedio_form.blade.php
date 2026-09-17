@extends('layout.admin.admin_layout_for_upload_vedio')

@section('title','Upload Video Form')

@section('page-style')
    <style>
        #progressWrapper {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        #progressBar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            line-height: 20px;
            color: white;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3">Share your testimonial as a review </h4>

    <form id="videoUploadForm" enctype="multipart/form-data">
        @csrf
        <!-- Hidden field for the testimonial this video belongs to -->
        <input type="hidden" name="testimonial_id" value="{{ $testimonial_id }}">

        <div class="mb-3">
            <label for="FullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="FullName" name="full_name" value="{{ $full_name }}" placeholder="Enter Full name"  required>
        </div>

        <div class="mb-3">
            <label for="videoFile" class="form-label">Select Video</label>
            <input type="file" class="form-control" id="videoFile" name="uploaded_video" accept="video/*"  required>
        </div>

        <button type="submit" class="btn btn-primary">Transfer Video</button>
    </form>

    <div id="progressWrapper" class="mt-3" style="display:none;">
        <div id="progressBar">0%</div>
    </div>

    <div id="uploadMessage" class="mt-3"></div>

    <!-- Uploaded Files -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Uploaded Files</h5>
        </div>
        <div class="card-body">
            @if (empty($files))
                <p class="text-muted mb-0">No files uploaded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>File Type</th>
                                <th>Uploaded Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $file)
                                <tr>
                                    <td class="text-break">{{ $file['name'] }}</td>
                                    <td>{{ $file['type'] }}</td>
                                    <td>{{ $file['uploaded_at']->format('d M Y, h:i A') }}</td>
                                    <td class="text-nowrap">
                                        @if ($file['previewable'])
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary preview-file-btn"
                                                    data-src="{{ $file['preview_url'] }}"
                                                    data-name="{{ $file['name'] }}">
                                                View
                                            </button>
                                        @endif
                                        <a href="{{ $file['download_url'] }}" class="btn btn-sm btn-outline-success">Download</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewFileModalLabel">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="previewFileVideo" class="w-100" controls style="max-height: 70vh;"></video>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    // Keep in sync with the server-side 'max:1048576' (KB) rule in
    // TestimonialController::uploadVideoProcess().
    var MAX_VIDEO_BYTES = 1024 * 1024 * 1024; // 1 GB

    function formatSize(bytes) {
        return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
    }

    function showError(message) {
        $('#uploadMessage').html('<div class="alert alert-danger">' + message + '</div>');
        $('#progressWrapper').hide();
    }

    $('#videoUploadForm').on('submit', function(e) {
        e.preventDefault();

        var fileInput = document.getElementById('videoFile');
        var file = fileInput.files[0];

        if (!file) {
            showError('Please choose a video to upload.');
            return;
        }

        if (file.size > MAX_VIDEO_BYTES) {
            showError('This video is ' + formatSize(file.size) + ', which is larger than the 1 GB limit. Please choose a smaller file or compress the video and try again.');
            return;
        }

        var formData = new FormData(this); // this will automatically include the hidden field
        $('#progressWrapper').show();
        $('#progressBar').css('width', '0%').text('0%');
        $('#uploadMessage').html('');

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#progressBar').css('width', percentComplete + '%').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            url: "{{ route('testimonials.upload_video_process') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#uploadMessage').html('<div class="alert alert-success">Video uploaded successfully!</div>');
                $('#videoUploadForm')[0].reset();
                $('#progressWrapper').hide();
                // Reload so the newly uploaded file shows up in the table below.
                setTimeout(function() {
                    window.location.reload();
                }, 1200);
            },
            error: function(xhr) {
                var message = 'Upload failed. Please try again.';

                if (xhr.status === 413) {
                    message = 'This video is too large for the server to accept (max 1 GB). Please choose a smaller file.';
                } else if (xhr.status === 422 && xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    } else if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                } else if (xhr.status === 0) {
                    message = 'Upload failed: connection lost or timed out. Please check your internet connection and try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showError(message);
            }
        });
    });

    // Preview modal
    var previewModalEl = document.getElementById('previewFileModal');
    var previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;
    var $previewVideo = $('#previewFileVideo');

    $(document).on('click', '.preview-file-btn', function() {
        var src = $(this).data('src');
        var name = $(this).data('name');

        $('#previewFileModalLabel').text(name);
        $previewVideo.attr('src', src);

        if (previewModal) {
            previewModal.show();
        }
    });

    if (previewModalEl) {
        previewModalEl.addEventListener('hidden.bs.modal', function() {
            $previewVideo[0].pause();
            $previewVideo.attr('src', '');
        });
    }

});
</script>
@endsection
