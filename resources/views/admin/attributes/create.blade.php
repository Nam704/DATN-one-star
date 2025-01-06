@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Back to List
                        </a>
                    </div>
                    <h4 class="page-title">Create Attribute</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.attributes.store') }}" method="POST">
                            @csrf
                            {{-- Nhập tên --}}

                            <div class="mb-3">
                                <label for="name" class="form-label">Name (Max 100 characters)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                       placeholder="Enter attribute name (e.g., Color, Size)"value="{{ old('name') }}">
                                        
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror     
                            </div>
                    
                            {{-- Nhập mô tả --}}

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Enter attribute description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Chọn trạng thái --}}

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class=" @error('status') is-invalid @enderror form-select"
                                    aria-label="Default select example" id="" name="status">
                                    <option selected>Open this select menu</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Create Attribute</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <x-admin.dashboard-styles />
@endpush

@push('scripts')
    <x-admin.dashboard-scripts />
@endpush
