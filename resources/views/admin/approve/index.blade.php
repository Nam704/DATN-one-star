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
                                            @foreach ($requests as $request)
                                                <tr>
                                                    <td><input type="checkbox" name="request_ids[]"
                                                            value="{{ $request->id }}" class="form-check-input"></td>
                                                    <td>{{ ucfirst($request->model_type) }}</td>
                                                    {{-- Cột Dữ liệu cũ --}}
                                                    <td>
                                                        @if (empty($request->original))
                                                            <span class="text-muted">—</span>
                                                        @else
                                                            <ul class="mb-0 pl-3">
                                                                @foreach ($request->payload as $key => $newValue)
                                                                    @php
                                                                        $oldValue = data_get($request->original, $key);
                                                                    @endphp

                                                                    <li>
                                                                        <strong>{{ $key }}:</strong>
                                                                        @if ($oldValue !== null && $oldValue != $newValue)
                                                                            <span>{{ $oldValue }}</span>
                                                                        @elseif ($oldValue !== null)
                                                                            <span
                                                                                class="text-muted">{{ $oldValue }}</span>
                                                                        @else
                                                                            <span class="text-muted">N/A</span>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </td>

                                                    {{-- Cột Dữ liệu mới --}}
                                                    <td>
                                                        <ul class="mb-0 pl-3">
                                                            @foreach ($request->payload as $key => $newValue)
                                                                <li>
                                                                    <strong>{{ $key }}:</strong>
                                                                    @if (is_array($newValue))
                                                                        <span>{{ json_encode($newValue) }}</span>
                                                                    @else
                                                                        <span>{{ $newValue }}</span>
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
