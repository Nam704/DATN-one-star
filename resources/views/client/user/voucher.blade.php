<div class="tab-pane fade show active" id="voucher">
    <h3 class="mb-4">Mã Giảm Giá</h3>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($vouchers as $voucher)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded">
                    <div class="card-body d-flex flex-column">
                        <!-- Tên và mô tả ngắn -->
                        <h5 class="text-primary mb-2">{{ $voucher['name'] }}</h5>

                        <!-- Dòng mô tả rút gọn -->
                        <p class="mb-2 text-dark">
                            @if ($voucher['type'] === 'percent')
                                Giảm {{ $voucher['discount_amount'] }}%
                                @if (!empty($voucher['max_discount_amount']))
                                    tối đa {{ number_format($voucher['max_discount_amount']) }}đ
                                @endif
                            @else
                                Giảm {{ number_format($voucher['discount_amount']) }}%
                                @if (!empty($voucher['max_discount_amount']))
                                    (tối đa {{ number_format($voucher['max_discount_amount']) }}đ)
                                @endif
                            @endif

                            @if (!empty($voucher['min_amount']))
                                cho đơn hàng từ {{ number_format($voucher['min_amount']) }}đ
                            @endif
                        </p>

                        <!-- Hạn sử dụng -->
                        <small class="text-muted mb-2">
                            Hạn dùng: {{ \Carbon\Carbon::parse($voucher['end_date'])->format('H:i d/m/Y') }}
                        </small>

                        <!-- Lượt còn lại và giới hạn -->
                        @if ((!empty($voucher['user_limit']) && $voucher['user_limit'] > 0) || (!empty($voucher['quantity']) && $voucher['quantity'] > 0))
                            <small class="text-success mb-3">
                                @if (!empty($voucher['user_limit']) && $voucher['user_limit'] > 0)
                                    Áp dụng {{ $voucher['user_limit'] }} lần/người
                                @endif
                                @if (!empty($voucher['user_limit']) && $voucher['user_limit'] > 0 && !empty($voucher['quantity']) && $voucher['quantity'] > 0)
                                    –
                                @endif
                                @if (!empty($voucher['quantity']) && $voucher['quantity'] > 0)
                                    Còn {{ $voucher['quantity'] }} lượt
                                @endif
                            </small>
                        @endif

                        <!-- Chi tiết ẩn -->
                        <div class="collapse mb-3" id="details-{{ $voucher['code'] }}">
                            <div class="bg-light p-2 rounded border">
                                @if (!empty($voucher['products']))
                                    <p class="mb-1 fw-bold">Áp dụng cho sản phẩm:</p>
                                    <ul class="ps-3 mb-2">
                                        @foreach ($voucher['products'] as $product)
                                            <li>{{ $product }}</li>
                                        @endforeach
                                    </ul>
                                @elseif (!empty($voucher['categories']))
                                    <p class="mb-1 fw-bold">Áp dụng cho danh mục:</p>
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
                                <button class="btn btn-sm btn-secondary w-100" disabled>Hết mã</button>
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
            alert('Đã sao chép mã: ' + code);
        }).catch(err => {
            alert('Không thể sao chép mã: ' + err);
        });
    }
</script>
