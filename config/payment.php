<?php
return [
    /*
    | Các phương thức thanh toán được phép thử lại.
    | Chỉ những phương thức trong danh sách này được chấp nhận.
    */
    'allowed_retry_methods' => ['VNPAY'],

    /*
    | Số lần thử lại thanh toán tối đa cho mỗi đơn hàng.
    */
    'max_retry_attempts' => 3,

    /*
    | Thời gian tối đa (tính bằng giờ) để thử lại thanh toán kể từ khi tạo đơn hàng.
    */
    'retry_time_limit_hours' => 12,
];
