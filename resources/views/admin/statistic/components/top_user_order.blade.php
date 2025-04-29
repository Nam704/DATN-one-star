<div class="col-lg-12 d-flex">
    <div class="card flex-fill">
        <div class="card-body">
            <h5 class="header-title mb-4">Top 10 người mua nhiều nhất (hôm nay)</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khách hàng</th>
                            <th>Liên hệ (Email / SĐT)</th>
                            <th>Sản phẩm đã mua</th>
                            <th>Tổng chi (VNĐ)</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userStats as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user['user_name'] }}</td>
                                <td>
                                    @if ($user['email'] && $user['email'] != '-')
                                        Email: {{ $user['email'] }}<br>
                                    @endif
                                    @if ($user['phone'] && $user['phone'] != '-')
                                        Phone: {{ $user['phone'] }}
                                    @endif
                                    @if (($user['email'] == '-' || !$user['email']) && ($user['phone'] == '-' || !$user['phone']))
                                        -
                                    @endif
                                </td>
                                <td style="max-width:400px; white-space:normal; word-break:break-word;">
                                    {!! str_replace(', ', '<br>', $user['products_bought']) ?: '-' !!}
                                </td>
                                <td>{{ number_format($user['total_purchase'], 0, '.', ',') }}đ</td>
                                <td>
                                    <a href="{{ route('admin.orders.byUser', $user['id_user']) }}"
                                        class="btn btn-sm btn-info">
                                        Đơn hàng
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Chưa có khách hàng nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
