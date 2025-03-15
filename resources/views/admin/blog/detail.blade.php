@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Blog Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">{{ $blog->title }}</h4>
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-calendar-month-outline me-1"></i>{{ $blog->published_at }}
                            <i class="mdi mdi-face-man me-1" style="margin-left: 10px"></i>Admin
                            <i class="mdi mdi-clipboard-multiple me-1"
                                style="margin-left: 10px"></i>{{ $blog->category->name }}
                        </p>
                        <br>
                        <img src="{{ asset($blog->thumbnail) }}" alt="img" height="500px" width="500px">
                        <br>
                        <span>{!! $blog->content !!}</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    {{-- <script src="{{ asset('admin/api/attributes.js') }}"></script> --}}
@endpush
