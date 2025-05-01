<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function chart_user()
    {
        $id = auth()->user()->id;
        $totalSpent = Order::join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->where('orders.id_user', $id)  // Lọc theo user
            ->whereNotIn('order_statuses.name', ['Cancelled']) // Loại trừ trạng thái Hủy
            ->sum('orders.total');

        // Xác định hạng khách hàng
        if ($totalSpent >= 500000000) {
            $customerRank = 'VIP';
        } elseif ($totalSpent >= 250000000) {
            $customerRank = 'Kim Cương';
        } elseif ($totalSpent >= 100000000) {
            $customerRank = 'Vàng';
        } elseif ($totalSpent >= 30000000) {
            $customerRank = 'Bạc';
        } else {
            $customerRank = 'Đồng';
        }
        // tong don hang
        $totalOrders = Order::where('id_user', $id)->count();

        // list don hang
        $order = Order::where('id_user', $id)->with('orderDetails')->get();

        //list dia chi
        $user = User::with('addresses.ward.district.province')->find($id);

        // Thống kê top sản phẩm mua nhiều nhất
        $topPurchasedProducts = DB::table('order_details')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->where('orders.id_user', $id) // Lọc theo user ID
            ->whereNotIn('order_statuses.name', ['Cancelled']) // Loại trừ đơn hàng bị hủy
            ->select(
                'products.id',
                'products.name',
                'products.image_primary',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name', 'products.image_primary')
            ->orderBy('total_quantity', 'desc') // Sắp xếp theo số lượng mua
            ->limit(10) // Lấy 10 sản phẩm mua nhiều nhất
            ->get();

        // Đơn hàng có số tiền lớn nhất
        $maxOrder = Order::where('id_user', $id)
            ->whereNotIn('order_statuses.name', ['Cancelled'])
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->orderByDesc('orders.total')  // Sắp xếp giảm dần theo tổng tiền
            ->orderByDesc('orders.id')  // Nếu có nhiều đơn hàng có tổng tiền giống nhau, sắp xếp theo ID
            ->select('orders.*') // Chỉ chọn các cột từ bảng orders
            ->first();  // Lấy đơn hàng có số tiền lớn nhất

        // Đơn hàng có số tiền nhỏ nhất
        $minOrder = Order::where('id_user', $id)
            ->whereNotIn('order_statuses.name', ['Cancelled'])
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->orderBy('orders.total')
            ->orderBy('orders.id')
            ->select('orders.*')
            ->first();

        // Nếu chỉ có 1 đơn hoặc max và min giống nhau => minOrder = null
        if ($maxOrder && $minOrder && $maxOrder->id === $minOrder->id) {
            $minOrder = null;
        }

        $totalProductsBought = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->where('orders.id_user', $id)
            ->whereNotIn('order_statuses.name', ['Cancelled'])
            ->sum('order_details.quantity');

        // Đơn hàng gần nhất
        $latestOrder = Order::where('id_user', $id)
            ->whereHas('orderStatus', function ($query) {
                $query->where('name', '!=', 'Cancelled'); // Lọc đơn hàng không bị hủy
            })
            ->orderByDesc('created_at') // Sắp xếp theo thời gian tạo đơn hàng (mới nhất)
            ->first();

        return view('client.user.chart_user', compact('user', 'order', 'totalSpent', 'totalOrders', 'topPurchasedProducts', 'maxOrder', 'minOrder', 'totalProductsBought', 'latestOrder', 'customerRank'));
    }
    public function getOrderStatusStats()
{
    $id = auth()->id();

    $orderStats = Order::where('id_user', $id)
        ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
        ->selectRaw("
            SUM(CASE WHEN order_statuses.name = 'Delivered' THEN 1 ELSE 0 END) AS delivered_orders,
            SUM(CASE WHEN order_statuses.name IN ('Return Requested', 'Return Under Review', 'Return Approved', 'Return Rejected', 'Refunded') THEN 1 ELSE 0 END) AS returned_orders,
            SUM(CASE WHEN order_statuses.name = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
        ")
        ->first();

    return response()->json($orderStats);
}

}
