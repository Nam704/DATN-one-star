@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.slides.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Back
                        </a>
                    </div>
                    <h4 class="page-title">Slide Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title" style="margin-bottom: 15px">{{ $slide->title }}</h4>
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-calendar-month-outline me-1"></i>{{ $slide->created_at }}
                            <i class="mdi mdi-face-man me-1" style="margin-left: 10px"></i>Admin
                            <i class="mdi mdi-clipboard-multiple me-1"
                                style="margin-left: 10px"></i>{{ $slide->category->name ?? 'Không thuộc danh mục' }}
                        </p>
                        <br>
                        <span>{!! $slide->description !!}</span>
                        <!-- Ảnh chính -->
                        @if ($slide->primaryImage)
                                <img src="{{ asset($slide->primaryImage->image) }}" alt="Primary Image"
                                    class="primary-image">
                        @endif

                        <!-- Ảnh phụ -->
                        @foreach ($slide->secondaryImages as $image)
                            <img src="{{ asset($image->image) }}" alt="Secondary Image" class="secondary-image">
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.data-table-styles />
    <style>
        .primary-image {
            width: 250px;
            height: 200px;
            object-fit: cover;
        }

        .secondary-image {
            width: 250px;
            height: 200px;
            margin: 5px;
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    {{-- <script src="{{ asset('admin/api/attributes.js') }}"></script> --}}
@endpush
