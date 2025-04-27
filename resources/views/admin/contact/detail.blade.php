@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-dark">
                            <i class="mdi mdi-arrow-left-thin"></i>
                            Quay lại
                        </a>
                    </div>
                    <h4 class="page-title">Chi tiết liên hệ</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <p class="text-muted mb-0">
                            <i class="mdi mdi-calendar-month-outline me-1"></i>{{ $contact->created_at->format('d/m/Y') }}
                            <i class="mdi mdi-face-man me-1" style="margin-left: 10px"></i>{{ $contact->name }}
                        </p>
                        <p>Nội dung : </p>
                        <p>{{ $contact->message }}</p>
                        <p>Phản hồi : </p>
                        <p>{{ $contact->reply }}</p>

                        <Span>Trạng thái :
                            <span
                                class="badge bg-{{ $contact->status === 'resolved' ? 'success' : ($contact->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($contact->status) }}
                            </span>
                        </Span>
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
