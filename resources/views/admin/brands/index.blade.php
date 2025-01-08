@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Brand
                        </a>
                        <a href="{{ route('admin.brands.trash') }}" class="btn btn-warning me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Brands Management</h4>
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
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brands as $brand)
                                    <tr>
                                        <td>{{ $brand->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $brand->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($brand->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if (!$brand->deleted_at)
                                                    <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm {{ $brand->status === 'active' ? 'btn-success' : 'btn-danger' }} toggle-status"
                                                        data-id="{{ $brand->id }}"
                                                        data-status="{{ $brand->status }}">
                                                        <i class="ri-lock{{ $brand->status === 'active' ? '-unlock' : '' }}-line"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-brand"
                                                        data-id="{{ $brand->id }}">
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
    <script src="{{ asset('admin/api/brands.js') }}"></script>
@endpush


