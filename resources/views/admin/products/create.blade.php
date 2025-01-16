@extends('admin.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Create New Product</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" 
                                placeholder="Enter product name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Existing Attribute</label>
                            <div class="row mb-2">
                                <div class="col-md-5">
                                    <select class="form-select attribute-select" name="attributes[]">
                                        <option value="">Select Attribute</option>
                                        @foreach($attributes as $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="attribute_values[]" 
                                        placeholder="Enter value for selected attribute">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mb-3" id="showNewAttributeForm">
                            <i class="mdi mdi-plus"></i> Create New Attribute
                        </button>

                        <div class="mb-3" id="newAttributeForm" style="display: none;">
                            <label class="form-label">New Attribute Details</label>
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="new_attribute" 
                                        placeholder="Enter new attribute name">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="new_attribute_value" 
                                        placeholder="Enter value for new attribute">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
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
<script src="{{ asset('admin/api/products.js') }}"></script>
@endpush
