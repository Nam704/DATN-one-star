<!-- resources/views/client/user/vouchers.blade.php -->

<div class="tab-pane fade" id="voucher">
    <h3 class="text-xl font-semibold mb-4">Mã Giảm Giá</h3>

    <!-- Danh sách mã giảm giá -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($vouchers as $voucher)
            <div class="col">
                <div class="card shadow-sm border-light rounded">
                    <div class="card-body">
                        <h5 class="card-title">{{ $voucher['name'] }}</h5>
                        <p class="card-text text-muted">{{ $voucher['description'] }}</p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success font-weight-bold">{{ $voucher['discount_amount'] }} ₫</span>
                            <span class="text-muted">{{ $voucher['end_date'] }}</span>
                        </div>

                        <div class="mt-3">
                            @if($voucher['quantity'] > 0)
                                <button class="btn btn-primary w-100" onclick="copyCouponCode('{{ $voucher['code'] }}')">Sao chép mã</button>
                            @else
                                <button class="btn btn-secondary w-100" disabled>Hết hàng</button>
                            @endif
                        </div>

                        <div class="mt-3">
                            @if($voucher['quantity'] > 0)
                                <button class="btn btn-info w-100" onclick="toggleDetails('{{ $voucher['code'] }}')">Xem chi tiết</button>
                            @else
                                <button class="btn btn-secondary w-100" disabled>Hết hạn</button>
                            @endif
                        </div>
                    </div>

                    <!-- Modal chi tiết voucher -->
                    <div id="voucher-details-{{ $voucher['code'] }}" class="card-footer d-none">
                        <div class="text-muted mt-2">
                            <p><strong>Miêu tả:</strong> {{ $voucher['description'] }}</p>
                            <p><strong>Số lượng còn lại:</strong> {{ $voucher['quantity'] }}</p>
                            <p><strong>Giảm giá:</strong> {{ $voucher['discount_amount'] }} ₫</p>
                            <p><strong>Ngày hết hạn:</strong> {{ $voucher['end_date'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // Copy coupon code to clipboard
    function copyCouponCode(code) {
        navigator.clipboard.writeText(code).then(function() {
            alert('Mã giảm giá đã được sao chép!');
        }).catch(function(error) {
            alert('Không thể sao chép mã giảm giá: ' + error);
        });
    }

    // Toggle show/hide voucher details
    function toggleDetails(code) {
        const detailsDiv = document.getElementById('voucher-details-' + code);
        if (detailsDiv.classList.contains('d-none')) {
            detailsDiv.classList.remove('d-none');
        } else {
            detailsDiv.classList.add('d-none');
        }
    }
</script>
