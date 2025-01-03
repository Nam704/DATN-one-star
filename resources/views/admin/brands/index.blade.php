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
                        <table class="table table-centered w-100 dt-responsive nowrap" id="brands-table">
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
    <x-admin.dashboard-styles />
@endpush

@push('scripts')
    <x-admin.dashboard-scripts />
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('#brands-table').DataTable({
                responsive: true,
                pageLength: 10,
                ordering: true
            });

            $('.delete-brand').click(function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/brands/${id}`,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    }
                });
            });

            $('.toggle-status').click(function() {
                const id = $(this).data('id');
                const button = $(this);
                const statusBadge = button.closest('tr').find('.badge');

                $.ajax({
                    url: `/admin/brands/${id}/toggle-status`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            const newStatus = response.newStatus;
                            button.data('status', newStatus);

                            if (newStatus === 'active') {
                                button.html('<i class="ri-lock-unlock-line"></i>');
                                button.removeClass('btn-danger').addClass('btn-success');
                                statusBadge.removeClass('bg-danger').addClass('bg-success')
                                    .text('Active');
                            } else {
                                button.html('<i class="ri-lock-line"></i>');
                                button.removeClass('btn-success').addClass('btn-danger');
                                statusBadge.removeClass('bg-success').addClass('bg-danger')
                                    .text('Inactive');
                            }
                            toastr.success(response.message);
                        }
                    }
                });
            });
        });
    </script>
@endpush
