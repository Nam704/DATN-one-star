<div class="tab-pane fade" id="voucher">
    <h3 class="mb-4 text-center text-2xl font-bold text-gray-900">Mã Giảm Giá</h3>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($vouchers as $voucher)
        <div class="col">
            <div class="card h-100 border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="card-body d-flex flex-column">
                    <h5 class="text-primary mb-3 text-xl font-semibold">{{ $voucher['name'] }}</h5>

                    <!-- Mô tả giảm giá -->
                    <p class="mb-3 text-gray-700">
                        @php
                        $type = $voucher['type'];
                        $discount = $voucher['discount_amount'];
                        $min = $voucher['min_amount'] ?? null;
                        $max = $voucher['max_discount_amount'] ?? null;
                        $discountDisplay = number_format($discount, 0);
                        @endphp

                        @if ($type === 'percentage')
                        <span class="font-semibold">Giảm {{ $discountDisplay }}%</span>
                        @if ($min && $max)
                        cho đơn hàng từ {{ number_format($min) }}đ ( tối đa <span class="font-semibold text-red-600">{{ number_format($max) }}đ)</span>
                        @elseif ($min)
                        cho đơn hàng từ <span class="font-semibold text-red-600">{{ number_format($min) }}đ</span>
                        @elseif ($max)
                        tối đa <span class="font-semibold text-red-600">{{ number_format($max) }}đ</span>
                        @endif
                        @elseif ($type === 'fixed')
                        <span class="font-semibold">Giảm {{ number_format($discount) }}đ</span>
                        @if ($min && $max)
                        cho đơn hàng từ <span class="font-semibold text-red-600">{{ number_format($min) }}đ</span> (tối đa <span class="font-semibold text-red-600">{{ number_format($max) }}đ</span>)
                        @elseif ($min)
                        cho đơn hàng từ <span class="font-semibold text-red-600">{{ number_format($min) }}đ</span>
                        @elseif ($max)
                        (tối đa <span class="font-semibold text-red-600">{{ number_format($max) }}đ</span>)
                        @endif
                        @endif
                    </p>

                    <!-- Mã giảm giá -->
                    <p class="mb-3 text-gray-800">
                        <strong class="text-lg">Mã giảm giá:</strong> <span class="text-primary font-semibold">{{ $voucher['code'] }}</span>
                    </p>

                    <!-- Hạn dùng -->
                    <small class="text-muted mb-2 block">
                        <i class="bi bi-calendar-event"></i> Hạn dùng: {{ \Carbon\Carbon::parse($voucher['end_date'])->format('H:i d/m/Y') }}
                    </small>

                    <!-- Thời gian còn lại -->
                    <small class="text-danger mb-3">
                        Còn {{ \Carbon\Carbon::parse($voucher['end_date'])->diffForHumans() }}
                    </small>

                    <!-- Thông tin giới hạn -->
                    @if ((!empty($voucher['user_limit']) && $voucher['user_limit'] > 0) || (!empty($voucher['quantity']) && $voucher['quantity'] > 0))
                    <div class="text-sm text-green-600 mb-3">
                        @if (!empty($voucher['user_limit']) && $voucher['user_limit'] > 0)
                        <p>Áp dụng {{ $voucher['user_limit'] }} lần/người</p>
                        @endif
                        @if (!empty($voucher['quantity']) && $voucher['quantity'] > 0)
                        <p>Còn {{ $voucher['quantity'] }} lượt</p>
                        @endif
                    </div>
                    @endif

                    <!-- Chi tiết ẩn -->
                    <div class="collapse mb-3" id="details-{{ $voucher['code'] }}">
                        <div class="bg-light p-3 rounded-md border">
                            @if (!empty($voucher['applies_to']) && count($voucher['applies_to']) > 0)
                            <p class="mb-1 font-semibold">Áp dụng cho sản phẩm:</p>
                            <ul class="ps-3 mb-2">
                                @foreach ($voucher['applies_to'] as $product)
                                <li>{{ $product }}</li>
                                @endforeach
                            </ul>
                            @elseif (!empty($voucher['categories']) && count($voucher['categories']) > 0)
                            <p class="mb-1 font-semibold">Áp dụng cho danh mục:</p>
                            <ul class="ps-3 mb-2">
                                @foreach ($voucher['categories'] as $category)
                                <li>{{ $category }}</li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-muted mb-0">Áp dụng cho tất cả sản phẩm</p>
                            @endif
                        </div>
                    </div>


                    <!-- Nút hành động -->
                    <div class="mt-auto d-flex gap-2">
                        @if ($voucher['quantity'] > 0)
                        <button class="btn btn-sm btn-primary w-50" onclick="copyCouponCode('{{ $voucher['code'] }}')">
                            Lấy mã
                        </button>
                        <button class="btn btn-sm btn-outline-secondary w-50" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#details-{{ $voucher['code'] }}">
                            Xem chi tiết
                        </button>
                        @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="bi bi-x-circle-fill"></i> Hết mã
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    function copyCouponCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Lấy mã thành công');
        }).catch(err => {
            alert('Không thể sao chép mã: ' + err);
        });
    }
</script>