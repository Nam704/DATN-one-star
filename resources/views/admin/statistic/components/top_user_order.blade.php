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
                                <th>Sản phẩm đã mua</th>
                                <th>Tổng chi (VNĐ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userStats as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user['user_name'] }}</td>
                                    <td style="max-width:400px; white-space:normal; word-break:break-word;">
                                        {!! str_replace(', ', '<br>', $user['products_bought']) ?: '-' !!}
                                    </td>
                                    <td>{{ number_format($user['total_purchase'], 0, '.', ',') }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Chưa có khách hàng nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
