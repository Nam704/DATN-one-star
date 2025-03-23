@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <a href="{{ route('admin.attribute_values.create') }}" class="btn btn-primary">
                                            <i class="ri-add-line align-middle me-1"></i>
                                            Thêm giá trị
                                        </a>
                                        <a href="{{ route('admin.attribute_values.trash') }}" class="btn btn-dark">
                                            <i class="ri-delete-bin-line me-1"></i>
                                            Thùng rác
                                        </a>
                                    </div>
                                    <h4 class="page-title">Danh sách giá trị thuộc tính</h4>
                                </div>
                            </div>
                        </div>
                        @if (session('message'))
                            <div class="alert alert-primary" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session('message_error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('message_error') }}
                            </div>
                        @endif
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Thuộc tính</th>
                                    <th>Giá trị thuộc tính</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($attributes_value as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->attribute->name }}</td>
                                        <td>{{ $value->value }}</td>
                                        <td>
                                            <div class="text d-flex">
                                                @if ($value->status == 'active')
                                                    <p style="color: rgb(88, 160, 88)">Đang hoạt động</p>
                                                @else
                                                    <p style="color: rgb(86, 86, 86)">Ngừng hoạt động</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($value->updated_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="group-btn d-flex" style="align-items: center">
                                                <div>
                                                    <a class="btn btn-success waves-effect width-sm waves-light"
                                                        href="{{ route('admin.attribute_values.edit', $value->id) }}"><i
                                                            class="ri-pencil-line"></i></a>
                                                </div>
                                                <div class="formform">
                                                    <form action="{{ route('admin.attribute_values.destroy', $value->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit"
                                                            onclick="return confirm('Bạn có muốn xóa không?')"
                                                            class="btn btn-danger m-1"><i
                                                                class="ri-delete-bin-line"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>STT</th>
                                    <th>Giá trị thuộc tính</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày cập nhật</th>
                                    <th>Hành động</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    <script src="{{ asset('admin/api/attributes_value.js') }}"></script>
@endpush
