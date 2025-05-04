@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Quay lại
                        </a>
                    </div>
                    <h4 class="page-title">Thêm thuộc tính</h4>
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
                                <label for="name" class="form-label">Tên thuộc tính (Tối đa 100 kí tự)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                       placeholder="Tên thuộc tính..."value="{{ old('name') }}">

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nhập mô tả --}}

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" placeholder="Mô tả thuộc tính">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Chọn trạng thái --}}

                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class=" @error('status') is-invalid @enderror form-select"
                                    aria-label="Chọn trạng thái mặc định" id="" name="status">
                                    <option selected>Chọn trạng thái</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                        </form>
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
@endpush
