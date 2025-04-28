@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('admin.contacts.index') }}" type="button" class="btn btn-sm btn-success">
                        <i class="fas fa-trash-alt"></i>Quay lại
                    </a>
                </div>
                <h4 class="page-title">Danh sách đã phản hồi</h4>
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
                            @foreach ($contacts as $key => $value)
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
                                                class="btn btn-secondary btn-sm btn-warning me-1">Chi tiết</button>
                                        </a>
                                        <form action="{{route('admin.contacts.delete',$value->id)}}"
                                            class="d-inline" method="POST"
                                            onclick="return confirm('Bạn có muốn xóa không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm btn-danger ">Xóa</button>
                                        </form>

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
@endpush