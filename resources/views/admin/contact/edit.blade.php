@extends('admin.layouts.layout')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Trả lời liên hệ</h4>
                </div>
            </div>
        </div>
        <div>
            <form action="{{ route('admin.contacts.update', $contact->id) }}" method="post" enctype="multipart/form-data"
                class="form">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="reply" class="font-weight-bold">Phản hồi :</label>
                                    <textarea class="form-control" id="reply" name="reply" rows="8"></textarea>
                                    {{-- <input type="hidden" class="form-control" id="status" name="status" value> --}}
                                    @error('reply')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success mr-2">Gửi phản hồi</button>
                </div>

            </form>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@push('styles')
    <!-- Quill css -->


    <link href="{{ asset('admin/assets/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />


    <link href="{{ asset('admin/assets/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
    <!-- Quill Editor js -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <!-- Quill Demo js -->
    <script src="{{ asset('admin/assets/js/pages/quilljs.init.js') }}"></script>

    <script src="{{ asset('admin/api/blog.js') }}"></script>

    <script src="{{ asset('admin/api/testFunction.js') }}"></script>
@endpush
