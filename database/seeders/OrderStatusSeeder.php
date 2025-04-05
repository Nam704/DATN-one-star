<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách tất cả trạng thái
        $statuses = [
            // Thanh toán & xử lý đơn
            'Awaiting Payment',
            'Payment Verification',
            'Paid',
            'Pending',
            'Processing',
            'Shipping',
            'Delivered',

            // Lỗi & Retry thanh toán
            'Payment Failed',
            'Payment Expired',
            'Payment Retry Requested',

            // Hủy đơn
            'Cancel Requested',
            'Cancel Under Review',
            'Cancel Approved',
            'Cancel Rejected',
            'Cancelled',

            // Hoàn đơn
            'Return Requested',
            'Return Under Review',
            'Return Approved',
            'Return Rejected',
            'Refunded',

            // Trường hợp khác
            'Failed Delivery',
        ];

        // Chèn các trạng thái vào DB và lưu ID
        $statusIds = [];
        foreach ($statuses as $statusName) {
            $statusIds[$statusName] = DB::table('order_statuses')->insertGetId([
                'name' => $statusName,
                'created_at' => now(),
                'updated_at' => now(),
                'next_status_id' => null,
            ]);
        }

        // Luồng xử lý đơn hàng chính
        DB::table('order_statuses')->where('id', $statusIds['Awaiting Payment'])->update(['next_status_id' => $statusIds['Payment Verification']]);
        DB::table('order_statuses')->where('id', $statusIds['Payment Verification'])->update(['next_status_id' => $statusIds['Paid']]);
        DB::table('order_statuses')->where('id', $statusIds['Paid'])->update(['next_status_id' => $statusIds['Pending']]);
        DB::table('order_statuses')->where('id', $statusIds['Pending'])->update(['next_status_id' => $statusIds['Processing']]);
        DB::table('order_statuses')->where('id', $statusIds['Processing'])->update(['next_status_id' => $statusIds['Shipping']]);
        DB::table('order_statuses')->where('id', $statusIds['Shipping'])->update(['next_status_id' => $statusIds['Delivered']]);

        // Luồng lỗi & retry thanh toán
        DB::table('order_statuses')->where('id', $statusIds['Payment Failed'])->update(['next_status_id' => $statusIds['Payment Retry Requested']]);
        DB::table('order_statuses')->where('id', $statusIds['Payment Expired'])->update(['next_status_id' => $statusIds['Payment Retry Requested']]);
        DB::table('order_statuses')->where('id', $statusIds['Payment Retry Requested'])->update(['next_status_id' => $statusIds['Awaiting Payment']]);

        // Luồng hủy đơn
        DB::table('order_statuses')->where('id', $statusIds['Cancel Requested'])->update(['next_status_id' => $statusIds['Cancel Under Review']]);
        DB::table('order_statuses')->where('id', $statusIds['Cancel Under Review'])->update(['next_status_id' => $statusIds['Cancel Approved']]);
        DB::table('order_statuses')->where('id', $statusIds['Cancel Approved'])->update(['next_status_id' => $statusIds['Cancelled']]);

        // Luồng hoàn đơn
        DB::table('order_statuses')->where('id', $statusIds['Return Requested'])->update(['next_status_id' => $statusIds['Return Under Review']]);
        DB::table('order_statuses')->where('id', $statusIds['Return Under Review'])->update(['next_status_id' => $statusIds['Return Approved']]);
        DB::table('order_statuses')->where('id', $statusIds['Return Approved'])->update(['next_status_id' => $statusIds['Refunded']]);

        // Giao hàng thất bại → hủy hoặc hoàn
        DB::table('order_statuses')->where('id', $statusIds['Failed Delivery'])->update(['next_status_id' => $statusIds['Cancel Requested']]);

        // Các trạng thái kết thúc
        $finalStates = ['Delivered', 'Cancelled', 'Refunded', 'Cancel Rejected', 'Return Rejected'];
        foreach ($finalStates as $state) {
            DB::table('order_statuses')->where('id', $statusIds[$state])->update(['next_status_id' => null]);
        }
    }
}
