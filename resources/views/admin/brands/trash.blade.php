@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Back to Brands
                        </a>
                    </div>
                    <h4 class="page-title">Trash Brands</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-centered w-100 dt-responsive nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trashedBrands as $brand)
                                    <tr>
                                        <td>{{ $brand->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $brand->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($brand->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $brand->deleted_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-success restore-brand"
                                                    data-id="{{ $brand->id }}">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger force-delete-brand"
                                                    data-id="{{ $brand->id }}">
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

@push('scripts')
<x-admin.dashboard-scripts />
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('.table').DataTable({
            responsive: true,
            pageLength: 10,
            ordering: true
        });

        // Restore functionality
        $('.restore-brand').click(function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Restore Brand?',
                text: "This will restore the brand!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/brands/${id}/restore`,
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function() {
                            Swal.fire(
                                'Restored!',
                                'Brand has been restored.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // Force Delete functionality
        $('.force-delete-brand').click(function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Delete Permanently?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete permanently!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/brands/${id}/force-delete`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function() {
                            Swal.fire(
                                'Deleted!',
                                'Brand has been permanently deleted.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
