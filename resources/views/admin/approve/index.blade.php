@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line align-middle me-1"></i>
                            Back to Dashboard
                        </a>
                    </div>
                    <h4 class="page-title">Phê duyệt Yêu cầu</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($requests->isEmpty())
                            <p class="text-muted">Không có yêu cầu nào đang chờ phê duyệt.</p>
                        @else
                            @php
                                // Nhãn hiển thị cho các trường
                                $labels = [
                                    'id' => 'ID',
                                    'name' => 'Tên',
                                    'parent_name' => 'Danh mục cha',
                                    'status' => 'Trạng thái',
                                    'attribute_name' => 'Thuộc tính', // Nhãn cho attribute_name
                                    'value' => 'Giá trị thuộc tính',
                                    'description' => 'Mô tả',
                                    // Blog fields
                                    'category_name' => 'Chuyên mục',
                                    'title' => 'Tiêu đề',
                                    'slug' => 'Slug',
                                    'content' => 'Nội dung',
                                    'thumbnail' => 'Ảnh đại diện',
                                    'published_at' => 'Ngày đăng',
                                ];
                            @endphp
                            <form action="{{ route('admin.requests.approve') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                                <th>Loại</th>
                                                <th>Trạng thái</th>
                                                <th>Chi tiết</th>
                                                <th>Hành động</th>
                                                <th>Người yêu cầu</th>
                                                <th>Thời gian yêu cầu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($requests as $req)
                                                <tr>
                                                    <td><input type="checkbox" name="request_ids[]" value="{{ $req->id }}" class="form-check-input"></td>
                                                    <td>{{ ucfirst($req->model_type) }}</td>
                                                    <td>{{ ucfirst($req->status) }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $req->id }}">
                                                            Xem
                                                        </button>

                                                        <!-- Detail Modal -->
                                                        <div class="modal fade" id="detailModal{{ $req->id }}" tabindex="-1">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Chi tiết yêu cầu #{{ $req->id }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <h6>Dữ liệu cũ</h6>
                                                                                <ul>
                                                                                    @foreach($req->original_data as $key => $value)
                                                                                        <li>
                                                                                            <strong>{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                            @if(is_array($value))
                                                                                                {{ implode(', ', $value) }}
                                                                                            @else
                                                                                                {{ $value ?? '—' }}
                                                                                            @endif
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <h6>Dữ liệu mới</h6>
                                                                                <ul>
                                                                                    @foreach($req->payload_data as $key => $value)
                                                                                        @if($key !== 'id_parent' && $key !== 'id_attribute') <!-- Bỏ qua id_parent và id_attribute -->
                                                                                            <li>
                                                                                                <strong>{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                                @if(is_array($value))
                                                                                                    {{ implode(', ', $value) }}
                                                                                                @else
                                                                                                    {{ $value ?? '—' }}
                                                                                                @endif
                                                                                            </li>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ ucfirst($req->action) }}</td>
                                                    <td>{{ $req->employee->name ?? 'Không xác định' }}</td>
                                                    <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="approve-btn" disabled>Phê duyệt đã chọn</button>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAll = document.getElementById('select-all');
                const checkboxes = document.querySelectorAll('input[name="request_ids[]"]');
                const approveBtn = document.getElementById('approve-btn');

                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    toggleButton();
                });

                checkboxes.forEach(cb => cb.addEventListener('change', toggleButton));

                function toggleButton() {
                    approveBtn.disabled = !Array.from(checkboxes).some(cb => cb.checked);
                }
            });
        </script>
    @endpush
@endsection
