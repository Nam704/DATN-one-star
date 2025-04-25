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
                            <form action="{{ route('admin.requests.approve') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                                <th>Loại</th>
                                                <th>Dữ liệu cũ</th>
                                                <th>Thay đổi</th> {{-- Chỉ 1 cột tổng --}}
                                                <th>Hành động</th>
                                                <th>Người yêu cầu</th>
                                                <th>Thời gian yêu cầu</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                // Nhãn hiển thị cho các trường
                                                $labels = [
                                                    'id' => 'ID',
                                                    'name' => 'Tên',
                                                    'id_parent' => 'Danh mục cha',
                                                    'status' => 'Trạng thái',
                                                    'attribute_name' => 'Thuộc tính',
                                                    'value' => 'Giá trị thuộc tính',
                                                ];
                                            @endphp
                                            @foreach ($requests as $request)
                                                @php
                                                    $orig = $request->original_data ?? [];
                                                    $new = $request->payload_data ?? [];

                                                    if ($request->model_type === 'attribute_value') {
                                                        $fields = ['attribute_name', 'value', 'status'];
                                                    } else {
                                                        $fields = [];
                                                        if (isset($new['id'])) {
                                                            $fields[] = 'id';
                                                        }
                                                        $fields[] = 'name';
                                                        if ($request->model_type === 'category') {
                                                            $fields[] = 'id_parent';
                                                        }
                                                        $fields[] = 'status';
                                                    }
                                                @endphp

                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="request_ids[]"
                                                            value="{{ $request->id }}" class="form-check-input">
                                                    </td>
                                                    <td>{{ ucfirst($request->model_type) }}</td>

                                                    <td>
                                                        <ul class="mb-0 pl-3">
                                                            @foreach ($fields as $field)
                                                                <li>
                                                                    <strong>{{ $labels[$field] ?? $field }}:</strong>
                                                                    @if ($field === 'id_parent')
                                                                        {{ data_get($orig, 'parent_name', '—') }}
                                                                    @elseif ($field === 'attribute_name')
                                                                        {{ data_get($orig, 'attribute_name', '—') }}
                                                                    @else
                                                                        {{ data_get($orig, $field, '—') }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </td>

                                                    <td>
                                                        <ul class="mb-0 pl-3">
                                                            @foreach ($fields as $field)
                                                                <li>
                                                                    <strong>{{ $labels[$field] ?? $field }}:</strong>
                                                                    @if ($field === 'id_parent')
                                                                        {{ data_get($new, 'parent_name', '—') }}
                                                                    @elseif ($field === 'attribute_name')
                                                                        {{ data_get($new, 'attribute_name', '—') }}
                                                                    @else
                                                                        {{ data_get($new, $field, '—') }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </td>

                                                    <td>{{ ucfirst($request->action) }}</td>
                                                    <td>{{ $request->employee->name ?? 'Không xác định' }}</td>
                                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="approve-btn" disabled>Phê duyệt Đã
                                        chọn</button>
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
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateApproveButton();
                });

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateApproveButton);
                });

                function updateApproveButton() {
                    const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                    approveBtn.disabled = !anyChecked;
                }
            });
        </script>
    @endpush
@endsection
