@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Attribute
                        </a>
                        <a href="{{ route('admin.attributes.trash') }}" class="btn btn-warning me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Attributes Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-database"
                         class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attributes as $attribute)
                                    <tr>
                                        <td>{{ $attribute->id }}</td>
                                        <td>{{ $attribute->name }}</td>
                                        <td>{{ $attribute->description }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $attribute->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($attribute->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if (!$attribute->deleted_at)
                                                    <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm {{ $attribute->status === 'active' ? 'btn-success' : 'btn-danger' }} toggle-status"
                                                        data-id="{{ $attribute->id }}"
                                                        data-status="{{ $attribute->status }}">
                                                        <i
                                                            class="ri-lock{{ $attribute->status === 'active' ? '-unlock' : '' }}-line"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-attribute"
                                                        data-id="{{ $attribute->id }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @endif
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
    <script src="{{ asset('admin/api/attributes.js') }}"></script>
@endpush
