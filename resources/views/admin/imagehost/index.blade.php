@extends('layout.admin.admin_layout')

@section('title','Image Host')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/custom/app-file-manager.css') }}">
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#uploadFile" class="btn btn-sm btn-primary">Upload File</a>
            </div>
        </div>

        <div class="row">
            @if ($posts->count() > 0)
                @foreach ($posts as $post)
                    <div class="col-md-2">
                        <div class="card">
                            
                            <div class="card-img-top ">
                                <img src="{{ asset('admin/assets/images/imagehost/'.$post->file_name) }}" class="img-fluid" alt="{{ $post->file_name }}">
                            </div>
                            <div class="px-1 py-1">
                                <a href="{{ asset('admin/assets/images/imagehost/'.$post->file_name) }}" class="text-info" target="_blank" title="Preview File"><i class="ti ti-eye ti-sm"></i></a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteIMG" data-id="{{ $post->id }}" class="text-danger" title="Delete {{ $post->file_name }}"><i class="ti ti-trash ti-sm"></i></a>
                                <a href="#" class="text-success text-url-copy" title="Copy File URL" data-id="{{ $post->file_url }}"><i class="ti ti-copy ti-sm"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach                
            @else
                <div class="col-md-12">
                    <p class="text-center">No Data Found</p>
                </div>
            @endif

        </div>
        
        <!-- Upload File Modal Start -->
        <div class="modal fade" id="uploadFile" aria-hidden="true" aria-labelledby="uploadFileLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="uploadFileLabel">Upload File</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.imagehost.store') }}" method="POST" id="uploadImageValidation" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-upload-file-name" class="form-label">File <span class="text-danger">*</span></label>
                                        <input class="form-control template-file-input" name="file_name" type="file" id="add-upload-file-name" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Upload File</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Upload File Modal End -->
        <!-- Delete File Modal Start -->
        <div class="modal fade" id="deleteIMG" aria-hidden="true" aria-labelledby="deleteIMGLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="deleteIMGLabel">Upload File</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.imagehost.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="imgID">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-danger">Are you sure to delete this file!</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Yes</button>
                            <button class="btn btn-sm btn-danger" type="button" data-bs-dismiss="modal">No</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Delete File Modal End -->
    </div>
    
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>    
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/image-host-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#deleteIMG').on('show.bs.modal',function(e){
                var imgID = $(e.relatedTarget).data('id');
                $('#imgID').val(imgID);
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('.text-url-copy').on('click',function(e){
                var file_url = $(this).data('id');
                // alert(file_url);
                navigator.clipboard.writeText(file_url);
                toastr['success']('Text copied - '+file_url+'', 'Success', { hideDuration: 3000 });
            });
        });
    </script>

@endsection