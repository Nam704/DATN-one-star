<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $statusId = $request->input('status_id');

        // Kiểm tra xem trạng thái mới có hợp lệ không
        if ($order->orderStatus->next_status_id != $statusId) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ cho đơn hàng này.',
            ], 400);
        }

        // Cập nhật trạng thái
        $order->id_order_status = $statusId;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đơn hàng đã được cập nhật.',
        ]);
    }
}
