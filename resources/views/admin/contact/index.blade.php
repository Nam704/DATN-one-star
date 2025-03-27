@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.blogs.trash') }}" class="btn btn-secondary me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Contact Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên người gửi</th>
                                    <th>Email</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($contact as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>{{ $value->email }}</td>
                                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $value->status === 'resolved' ? 'success' : ($value->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($value->status) }}
                                            </span>

                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.contacts.show', $value->id) }}">
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm btn-warning me-1"><i
                                                            class="mdi mdi-eye"></i></button>
                                                </a>
                                                <a href="{{ route('admin.contacts.edit', $value->id) }}"><button
                                                        class="btn btn-sm btn-success me-1"><i
                                                            class="mdi mdi-comment-edit-outline"></i></button></a>
                                                <button class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $value->id }}">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên người gửi</th>
                                    <th>Email</th>
                                    <th>Ngày gửi</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </tfoot>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('admin/api/blog.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@endpush
