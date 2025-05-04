@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-middle me-1"></i>
                            Thêm mới
                        </a>
                        <a href="{{ route('admin.blogs.trash') }}" class="btn btn-secondary me-2">
                            <i class="ri-delete-bin-line align-middle me-1"></i>
                            Danh sách xóa
                        </a>
                    </div>
                    <h4 class="page-title">Danh sách tin tức</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-2 mb-2 mb-sm-0">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active show" id="v-pills-all-tab" data-bs-toggle="pill"
                                        href="#v-pills-all" role="tab" aria-controls="v-pills-all" aria-selected="true">
                                        Tất cả
                                    </a>
                                    @foreach ($categories as $key => $category)
                                        <a class="nav-link" id="v-pills-{{ $category->id }}-tab" data-bs-toggle="pill"
                                            href="#v-pills-{{ $category->id }}" role="tab"
                                            aria-controls="v-pills-{{ $category->id }}" aria-selected="false">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div> <!-- end col-->

                            <div class="col-sm-10">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade active show" id="v-pills-all" role="tabpanel"
                                        aria-labelledby="v-pills-all-tab">
                                        @if ($blogs->count())
                                            <div class="card">
                                                <div class="card-body">
                                                    <table id="basic-datatable"
                                                        class="table table-striped dt-responsive nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th>STT</th>
                                                                <th>Tiêu đề</th>
                                                                <th>Hình ảnh</th>
                                                                <th>Thẻ tag</th>
                                                                <th>Trạng thái</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($blogs as $index => $value)
                                                                <tr>
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>{{ \Illuminate\Support\Str::limit($value->title, 40) }}
                                                                    </td>
                                                                    <td><img src="{{ asset($value->thumbnail) }}"
                                                                            alt="img" height="60px" width="60px"
                                                                            style="object-fit: cover"></td>
                                                                    <td
                                                                        style="max-width: 30px; word-wrap: break-word; white-space: normal;">
                                                                        @foreach ($value->tags as $tag)
                                                                            <span
                                                                                class="badge bg-primary-subtle text-primary mt-1">{{ $tag->name }}</span>
                                                                        @endforeach
                                                                    </td>
                                                                    <td><span
                                                                            class="badge bg-{{ $value->status === 'published' ? 'success' : ($value->status === 'draft' ? 'warning' : 'danger') }}">
                                                                            {{ ucfirst($value->status) }}
                                                                        </span></td>
                                                                    <td>
                                                                        <div class="btn-group">
                                                                            <a
                                                                                href="{{ route('admin.blogs.show', $value->id) }}">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary btn-sm btn-warning me-1"><i
                                                                                        class="mdi mdi-eye"></i></button>
                                                                            </a>
                                                                            <a
                                                                                href="{{ route('admin.blogs.edit', $value->id) }}"><button
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
                                                                <th>Tiêu đề</th>
                                                                <th>Hình ảnh</th>
                                                                <th>Thẻ tag</th>
                                                                <th>Trạng thái</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <p>Không có bài viết nào.</p>
                                        @endif
                                    </div>
                                    @foreach ($categories as $key => $category)
                                        <div class="tab-pane fade {{ $key == 0 ? 'active show' : '' }}"
                                            id="v-pills-{{ $category->id }}" role="tabpanel"
                                            aria-labelledby="v-pills-{{ $category->id }}-tab">
                                            @if ($category->blogs->count())
                                                <div class="card">
                                                    <div class="card-body">
                                                        <table id="basic-datatable"
                                                            class="table table-striped dt-responsive nowrap w-100">
                                                            <thead>
                                                                <tr>
                                                                    <th>STT</th>
                                                                    <th>Tiêu đề</th>
                                                                    <th>Hình ảnh</th>
                                                                    <th>Thẻ tag</th>
                                                                    <th>Trạng thái</th>
                                                                    <th>Hành động</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($category->blogs as $key => $value)
                                                                    <tr>
                                                                        <td>{{ $index + 1 }}</td>
                                                                        <td>{{ \Illuminate\Support\Str::limit($value->title, 40) }}
                                                                        </td>
                                                                        <td><img src="{{ asset($value->thumbnail) }}"
                                                                                alt="img" height="60px" width="60px"
                                                                                style="object-fit: cover"></td>
                                                                        <td
                                                                            style="max-width: 30px; word-wrap: break-word; white-space: normal;">
                                                                            @foreach ($value->tags as $tag)
                                                                                <span
                                                                                    class="badge bg-primary-subtle text-primary mt-1">{{ $tag->name }}</span>
                                                                            @endforeach
                                                                        </td>
                                                                        <td><span
                                                                                class="badge bg-{{ $value->status === 'published' ? 'success' : ($value->status === 'draft' ? 'warning' : 'danger') }}">
                                                                                {{ ucfirst($value->status) }}
                                                                            </span></td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <a
                                                                                    href="{{ route('admin.blogs.show', $value->id) }}">
                                                                                    <button type="button"
                                                                                        class="btn btn-secondary btn-sm btn-warning me-1"><i
                                                                                            class="mdi mdi-eye"></i></button>
                                                                                </a>
                                                                                <a
                                                                                    href="{{ route('admin.blogs.edit', $value->id) }}"><button
                                                                                        class="btn btn-sm btn-success me-1"><i
                                                                                            class="mdi mdi-comment-edit-outline"></i></button></a>
                                                                                <button
                                                                                    class="btn btn-sm btn-danger delete-btn"
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
                                                                    <th>Tiêu đề</th>
                                                                    <th>Hình ảnh</th>
                                                                    <th>Thẻ tag</th>
                                                                    <th>Trạng thái</th>
                                                                    <th>Hành động</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div> <!-- end card body-->
                                                </div>
                                                {{-- @else
                                                <p>Không có bài viết nào trong danh mục nàyyyy.</p> --}}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div> <!-- end col-->
                        </div>
                        <!-- end row-->
                    </div> <!-- end card-body -->
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
    <script src="{{ asset('admin/api/blog.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
@endpush
