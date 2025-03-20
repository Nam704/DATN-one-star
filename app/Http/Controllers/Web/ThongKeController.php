<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function statistics(Request $request)
    {
        // Lấy ngày thống kê từ request, nếu không có thì dùng ngày hiện tại
        $date = $request->input('date', now()->toDateString());

        // Tổng số đơn hàng trong ngày
        $totalOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->count();

        // Lấy ID của các trạng thái "Paid" và "Delivered"
        // $paidStatusId = DB::table('order_statuses')
        //     ->where('name', 'Paid')
        //     ->value('id');
        $deliveredStatusId = DB::table('order_statuses')
            ->where('name', 'Delivered')
            ->value('id');

        // Tổng doanh thu trong ngày
        $totalRevenue = DB::table('orders')
            ->whereDate('created_at', $date)
            ->whereIn('id_order_status', [$deliveredStatusId])
            ->sum('total');

        // Lấy ID của các trạng thái cần thiết
        $pendingStatusId   = DB::table('order_statuses')->where('name', 'Pending')->value('id');
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');

        // Số đơn hàng theo từng trạng thái trong ngày
        $pendingOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $pendingStatusId)
            ->count();

        $deliveredOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Giả sử "Completed Orders" là các đơn Delivered
        $completedOrders = $deliveredOrders;

        // Dữ liệu cho biểu đồ: đếm số đơn hàng theo trạng thái (trong ngày)
        $statistics = DB::table('orders')
            ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
            ->select('order_statuses.name as status', DB::raw('COUNT(orders.id) as total'))
            ->whereDate('orders.created_at', $date)
            ->groupBy('order_statuses.name')
            ->get();


        // Thống kê sản phẩm bán chạy nhất trong ngày
        // Giả sử bảng order_details có cột: id_order, id_product_variant, quantity
        // Và bảng product_variants liên kết với products (với cột id_product)
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereDate('orders.created_at', $date)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();


        return view('admin.statistics.order_statistics', compact(
            'date',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'cancelledOrders',
            'completedOrders',
            'totalRevenue',
            'statistics',
            'productsSales'
        ));
    }
    public function weeklyStatistics(Request $request)
    {
        // 1. Xác định khoảng thời gian của tuần
        $dateInput = $request->input('date', now()->toDateString());
        $selectedDate = Carbon::parse($dateInput);
        $startOfWeek = $selectedDate->copy()->startOfWeek(); // Thứ Hai
        $endOfWeek   = $selectedDate->copy()->endOfWeek();     // Chủ Nhật
        $dateRange = [$startOfWeek->toDateTimeString(), $endOfWeek->toDateTimeString()];

        // 2. Lấy ID của các trạng thái cần thiết
        $paidStatusId = DB::table('order_statuses')->where('name', 'Paid')->value('id');
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');

        // 3. Tính toán các chỉ số tổng quan

        // Tổng doanh thu (chỉ tính đơn hàng có trạng thái Delivered)
        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        // Tổng số đơn hàng (tất cả)
        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Số đơn hàng theo từng trạng thái
        $paidOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $paidStatusId)
            ->count();

        $deliveredOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Tổng số đơn hàng thành công (Paid & Delivered)
        $totalSuccessfulOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->whereIn('id_order_status', [$paidStatusId, $deliveredStatusId])
            ->count();

        // Giá trị trung bình mỗi đơn hàng (AOV) dựa trên doanh thu Delivered
        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo ngày trong tuần
        // Lấy dữ liệu từ DB (chỉ những ngày có đơn hàng)
        $rawDailyStats = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                // Tính doanh thu trong ngày chỉ từ các đơn Delivered
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as day_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        // Tạo danh sách đầy đủ các ngày trong tuần (sử dụng CarbonPeriod)
        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);
        $dailyStats = [];
        foreach ($period as $date) {
            $day = $date->toDateString();
            if ($rawDailyStats->has($day)) {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => $rawDailyStats[$day]->total_orders,
                    'delivered_orders' => $rawDailyStats[$day]->delivered_orders,
                    'cancelled_orders' => $rawDailyStats[$day]->cancelled_orders,
                    'day_revenue'      => $rawDailyStats[$day]->day_revenue,
                ];
            } else {
                $dailyStats[] = [
                    'order_date'       => $day,
                    'total_orders'     => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'day_revenue'      => 0,
                ];
            }
        }

        // 5. Top 20 sản phẩm bán chạy trong tuần
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();

        // 6. Top 20 người mua nhiều nhất trong tuần (tính theo tổng giá trị mua hàng, chỉ Delivered)
        // 6. Top 20 người mua nhiều nhất trong tuần (tính theo tổng giá trị mua hàng và tổng số lượng sản phẩm, chỉ Delivered)
        $topCustomers = DB::table('orders')
            ->select('id_user', 'user_name',
                DB::raw('SUM(total) as total_purchase'))
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(20)
            ->get();

        // Lấy chi tiết các sản phẩm mà mỗi khách hàng mua (dành cho các đơn hàng Delivered)
        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('orders.id_user', 'products.name')
            ->get();

        // Nhóm dữ liệu chi tiết theo id_user
        $customerProductsGrouped = $customerProducts->groupBy('id_user');


        return view('admin.statistics.weekly_statistics', compact(
            'selectedDate',
            'startOfWeek',
            'endOfWeek',
            'allTotalOrders',
            'totalRevenue',
            'paidOrders',
            'deliveredOrders',
            'cancelledOrders',
            'averageOrderValue',
            'dailyStats',
            'productsSales',
            'totalSuccessfulOrders',
            'topCustomers',
            'customerProductsGrouped'
        ));
    }
}
