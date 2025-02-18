@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Back to Categories
                        </a>
                    </div>
                    <h4 class="page-title">Trash Categories</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-datatable" class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Parent Category</th>
                                    <th>Status</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trashedCategories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->parent->name ?? 'None' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($category->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $category->deleted_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-success restore-category"
                                                    data-id="{{ $category->id }}">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger force-delete-category"
                                                    data-id="{{ $category->id }}">
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
<script src="{{ asset('admin/api/trashCategories.js') }}"></script>
@endpush
