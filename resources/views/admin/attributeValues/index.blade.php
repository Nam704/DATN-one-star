@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.attribute-values.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Attribute Value
                        </a>
                        <a href="{{ route('admin.attribute-values.trash') }}" class="btn btn-warning me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Attribute Values Management</h4>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attributeValues as $value)
                                    <tr>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->attribute->name }}</td>
                                        <td>{{ $value->value }}</td>
                                        <td>
                                            <span class="badge bg-{{ $value->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($value->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.attribute-values.edit', $value->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-sm {{ $value->status === 'active' ? 'btn-success' : 'btn-danger' }} toggle-status"
                                                    data-id="{{ $value->id }}"
                                                    data-status="{{ $value->status }}">
                                                    <i class="ri-lock{{ $value->status === 'active' ? '-unlock' : '' }}-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-attribute-value"
                                                    data-id="{{ $value->id }}">
                                                    <i class="ri-delete-bin-line"></i>
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
<script src="{{ asset('admin/api/attributesValue.js') }}"></script>
@endpush
