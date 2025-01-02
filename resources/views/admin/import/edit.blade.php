@extends('admin.layouts.layout')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{route('admin.imports.list')}}" class="btn btn-sm btn-primary">Back to list</a>
            </div>
            <div class="card-body">
                <form id="import-form" action="{{ route('admin.imports.edit', $import->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Supplier</label>
                                    <select name="supplier" class="form-select" id="supplier-name">
                                        @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $import->id_supplier == $supplier->id ?
                                            'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Import Name</label>
                                    <input type="text" id="import-name" name="name" value="{{ $import->name }}"
                                        class="form-control" readonly>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Import Date</label>
                                    <input type="date" class="form-control" id="import-date" name="import_date"
                                        value="{{ $import->import_date }}" readonly>
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Total Amount</label>
                                    <input type="number" id="total-amount" name="total_amount"
                                        value="{{ $import->total_amount }}" class="form-control" step="0.01" readonly>
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Note</label>
                                    <textarea id="note" name="note" class="form-control">{{ $import->note }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div id="import-details-container">
                                <h5>Import Details</h5>
                                <div class="product-rows-container" data-import-id="{{ $import->id }}">
                                    <!-- Existing variants will be loaded here -->
                                </div>
                            </div>

                            <button type="button" id="add-row-btn" class="btn btn-primary">Add Product</button>

                            <div class="mb-3 d-grid">
                                <button type="submit" class="btn btn-lg btn-success">Update Import</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/api/editImport.js') }}"></script>
@endpush