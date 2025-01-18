@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.attribute-values.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Back to Attribute Values
                        </a>
                    </div>
                    <h4 class="page-title">Trash Attribute Values</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-database" class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Attribute</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trashedAttributeValues as $value)
                                    <tr>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->attribute->name }}</td>
                                        <td>{{ $value->value }}</td>
                                        <td>
                                            <span class="badge bg-{{ $value->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($value->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $value->deleted_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-success restore-attribute-value"
                                                    data-id="{{ $value->id }}">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger force-delete-attribute-value"
                                                    data-id="{{ $value->id }}">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
<script src="{{ asset('admin/api/trashAttributesValue.js') }}"></script>
@endpush
