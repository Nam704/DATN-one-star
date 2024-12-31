@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        {{-- Thêm thuộc tính mới --}}
                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Attribute
                        </a>
                        {{-- Thùng rác --}}
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
                        <table class="table table-centered w-100 dt-responsive nowrap" id="attributes-table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Deleted At</th>
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
                                            <span class="badge bg-{{ $attribute->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($attribute->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $attribute->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $attribute->updated_at && $attribute->updated_at != $attribute->created_at
                                            ? $attribute->updated_at->format('d/m/Y H:i:s')
                                            : 'Not updated yet' }}
                                        </td>
                                        <td>{{ $attribute->deleted_at }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if(!$attribute->deleted_at)
                                                    <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm {{ $attribute->status === 'active' ? 'btn-success' : 'btn-danger' }} toggle-status"
                                                            data-id="{{ $attribute->id }}">
                                                        <i class="ri-lock-{{ $attribute->status === 'active' ? 'unlock' : '' }}-line"></i>
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

            $('#attributes-table').DataTable({
                responsive: true,
                pageLength: 10,
                ordering: true
            });

            $('.delete-attribute').click(function() {
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
                            url: `/admin/attributes/${id}`,
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
                $.ajax({
                    url: `/admin/attributes/${id}/toggle-status`,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    }
                });
            });
        });
    </script>
@endpush
