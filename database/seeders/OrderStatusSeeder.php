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
        // Danh sách tất cả trạng thái với group_status đã gộp
        $statuses = [
            // Thanh toán (Payment)
            'Awaiting Payment' => 'Payment',
            'Payment Verification' => 'Payment',
            'Paid' => 'Payment',
            'Payment Failed' => 'Payment',
            'Payment Expired' => 'Payment',
            'Payment Retry Requested' => 'Payment',

            // Chuẩn bị giao hàng (Awaiting Delivery)
            'Pending' => 'Awaiting Delivery',
            'Processing' => 'Awaiting Delivery',

            // Đang vận chuyển (Shipping)
            'Shipping' => 'Shipping',

            // Hoàn thành (Completed)
            'Delivered' => 'Completed',

            // Hủy đơn (Cancelled)
            'Cancel Requested' => 'Cancelled',
            'Cancel Under Review' => 'Cancelled',
            'Cancel Approved' => 'Cancelled',
            // 'Cancel Rejected' => 'Cancelled',
            'Cancelled' => 'Cancelled',
            'Failed Delivery' => 'Cancelled', // Gộp Failed Delivery vào Cancelled

            // Hoàn đơn/Hoàn tiền (Return/Refund)
            'Return Requested' => 'Return/Refund',
            'Return Under Review' => 'Return/Refund',
            'Return Approved' => 'Return/Refund',
            'Return Rejected' => 'Return/Refund',
            'Refunded' => 'Return/Refund',
        ];

        // Chèn các trạng thái vào DB và lưu ID
        $statusIds = [];
        foreach ($statuses as $statusName => $groupStatus) {
            $statusIds[$statusName] = DB::table('order_statuses')->insertGetId([
                'name' => $statusName,
                'group_status' => $groupStatus,
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

        // Giao hàng thất bại → hủy
        DB::table('order_statuses')->where('id', $statusIds['Failed Delivery'])->update(['next_status_id' => $statusIds['Cancel Requested']]);

        // Các trạng thái kết thúc
        $finalStates = ['Delivered', 'Cancelled', 'Refunded', 'Return Rejected'];
        foreach ($finalStates as $state) {
            DB::table('order_statuses')->where('id', $statusIds[$state])->update(['next_status_id' => null]);
        }
    }
}
