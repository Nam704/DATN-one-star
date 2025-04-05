@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.slides.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Add Slides
                        </a>
                        <a href="{{ route('admin.slides.trash') }}" class="btn btn-secondary me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Trash
                        </a>
                    </div>
                    <h4 class="page-title">Slide Management</h4>
                </div>

                <div class="card-body">
                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Danh mục</th>
                                <th>Tiêu đề</th>
                                <th>Ngày đăng tải</th>
                                <th>Vị trí</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($slides as $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $value->category->name ?? 'Không có Danh mục' }}</td>
                                    <td>{{ Str::limit($value->title, 50, '...') }}</td>
                                    <td>{{ $value->created_at ? $value->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        @if (is_array($value->display_locations))
                                            @foreach ($value->display_locations as $location)
                                                <span class="badge bg-primary">{{ ucfirst($location) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-danger">Không xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $value->is_active == '1' ? 'primary' : 'dark' }}">
                                            {{ $value->is_active == '1' ? 'Đang hiển thị' : 'Không hiển thị' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.slides.show', $value->id) }}">
                                                <button type="button" class="btn btn-secondary btn-sm btn-warning me-1"><i
                                                        class="mdi mdi-eye"></i></button>
                                            </a>
                                            <a href="{{ route('admin.slides.edit', $value->id) }}"><button
                                                    class="btn btn-sm btn-success me-1"><i
                                                        class="mdi mdi-comment-edit-outline"></i></button></a>
                                            <button class="btn btn-sm btn-danger delete-slide-btn"
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
                                <th>Danh mục</th>
                                <th>Tiêu đề</th>
                                <th>Ngày đăng tải</th>
                                <th>Vị trí</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </tfoot>
                    </table>
                </div> <!-- end card body-->
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    <script src="{{ asset('admin/api/slides.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@endpush
