<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;

class StatisticController extends Controller
{
    private $user;
    private $product;
    private $category;
    private $order;

    public function __construct(User $user, Product $product, Category $category, Order $order)
    {
        $this->user = $user;
        $this->product = $product;
        $this->category = $category;
        $this->order = $order;
    }
    public function productStatistics()
    {
        if (auth()->check()) {
            $countData = [
                "product" => $this->product->count(),
                "revenue" => $this->order->where('id_order_status', '4')->sum('total'),
                "order" => $this->order->where('id_order_status', '4')->count(),
                "user" => $this->user->count()
            ];
            $topProduct = [
                "top_sale_products" => $this->product->top_10_products(),
                "least_sold_products" => $this->product->least_sold_products(),
            ];
            $low_stock_products = $this->product->low_stock_products();
            $categories_with_revenue = $this->category->categories_with_revenue();
            $top_view_products = $this->product->where('view', '>', 0)->orderBy('view', 'desc')->take(10)->get();
            $top_comment_products = [
                [
                    'name' => 'Iphone 14',
                    'image_primary' => '/storage/products/1742179523_67d78cc366b50.png',
                    'total_comments' => 100,
                ],
                [
                    'name' => 'Google Pixel 7 Pro',
                    'image_primary' => '/storage/products/1742179523_67d78cc37b4e1.png',
                    'total_comments' => 80,
                ],
                [
                    'name' => 'Samsung Galaxy A34 5G',
                    'image_primary' => '/storage/products/1742179523_67d78cc383c82.png',
                    'total_comments' => 60,
                ]
            ];
            return view('admin.statistic.productstatistic', compact(
                'countData',
                'topProduct',
                'low_stock_products',
                'categories_with_revenue',
                'top_view_products',
                'top_comment_products'
            ));
        } else {
            return redirect()->route('admin.statistics.productStatistics');
        }
    }
    public function dashboardStatistics()
    {
        return view('admin.statistics.dashboard_statistics');
    }
    public function dailyStatistics(Request $request)
    {
        // Lấy ngày thống kê từ request, nếu không có thì dùng ngày hiện tại
        $date = $request->input('date', now()->toDateString());

        // Tổng số đơn hàng trong ngày
        $totalOrders = DB::table('orders')
            ->whereDate('created_at', $date)
            ->count();

        // Lấy ID của các trạng thái cần thiết
        $deliveredStatusId = DB::table('order_statuses')
            ->where('name', 'Delivered')
            ->value('id');
        $pendingStatusId   = DB::table('order_statuses')
            ->where('name', 'Pending')
            ->value('id');
        $cancelledStatusId = DB::table('order_statuses')
            ->where('name', 'Cancelled')
            ->value('id');

        // Tổng doanh thu trong ngày (chỉ tính đơn Delivered)
        $totalRevenue = DB::table('orders')
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

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

        // Top 10 người mua nhiều nhất trong ngày (chỉ tính các đơn Delivered)
        $topCustomers = DB::table('orders')
            ->select('id_user', 'user_name', DB::raw('SUM(total) as total_purchase'))
            ->whereDate('created_at', $date)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
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
            'productsSales',
            'topCustomers'
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
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ tính đơn hàng đã giao hàng
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->get();

        // 6. Top 20 người mua nhiều nhất trong tuần (tính theo tổng giá trị mua hàng, chỉ Delivered)
        // 6. Top 20 người mua nhiều nhất trong tuần (tính theo tổng giá trị mua hàng và tổng số lượng sản phẩm, chỉ Delivered)
        $topCustomers = DB::table('orders')
            ->select(
                'id_user',
                'user_name',
                DB::raw('SUM(total) as total_purchase')
            )
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
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
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
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

    public function monthlyStatistics(Request $request)
    {
        // 1. Xác định khoảng thời gian của tháng
        $monthInput = $request->input('month', now()->format('Y-m'));
        $selectedMonth = Carbon::parse($monthInput . '-01');
        $startOfMonth = $selectedMonth->copy()->startOfMonth();
        $endOfMonth   = $selectedMonth->copy()->endOfMonth();
        $dateRange = [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()];

        // 2. Lấy ID trạng thái
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');
        $paidStatusId = DB::table('order_statuses')->where('name', 'Paid')->value('id');

        // 3. Tính toán các chỉ số tổng quan

        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Tính số đơn hàng theo từng trạng thái
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

        $totalSuccessfulOrders = $deliveredOrders; // Chỉ tính Delivered

        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo ngày trong tháng
        $rawDailyStats = DB::table('orders')
            ->select(
                DB::raw('DATE(created_at) as order_date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as day_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
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

        // 5. Top 10 sản phẩm bán chạy
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(50)
            ->get();


        // 6. Top 20 người mua nhiều nhất (chi tiết theo Delivered)
        $topCustomers = DB::table('orders')
            ->select(
                'id_user',
                'user_name',
                DB::raw('SUM(total) as total_purchase')
            )
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(50)
            ->get();


        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId) // Chỉ lấy đơn đã giao hàng
            ->groupBy('orders.id_user', 'products.name')
            ->get();

        $customerProductsGrouped = $customerProducts->groupBy('id_user');

        return view('admin.statistics.monthly_statistics', compact(
            'selectedMonth',
            'startOfMonth',
            'endOfMonth',
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

    public function yearlyStatistics(Request $request)
    {
        // 1. Xác định năm cần thống kê (mặc định là năm hiện tại)
        $yearInput = $request->input('year', now()->format('Y'));
        $selectedYear = $yearInput; // ví dụ "2025"
        $startOfYear = Carbon::parse($selectedYear . '-01-01')->startOfDay();
        $endOfYear   = Carbon::parse($selectedYear . '-12-31')->endOfDay();
        $dateRange = [$startOfYear->toDateTimeString(), $endOfYear->toDateTimeString()];

        // 2. Lấy ID các trạng thái cần thiết
        $deliveredStatusId = DB::table('order_statuses')->where('name', 'Delivered')->value('id');
        $cancelledStatusId = DB::table('order_statuses')->where('name', 'Cancelled')->value('id');

        // 3. Tính toán các chỉ số tổng quan
        // Tổng doanh thu (chỉ tính đơn Delivered)
        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->sum('total');

        // Tổng số đơn hàng (tất cả) – tuy nhiên, nếu thống kê theo đơn thành công, bạn có thể chỉ lấy Delivered
        $allTotalOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->count();

        // Số đơn Delivered và Cancelled
        $deliveredOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->count();

        $cancelledOrders = DB::table('orders')
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $cancelledStatusId)
            ->count();

        // Tổng số đơn thành công (chỉ Delivered)
        $totalSuccessfulOrders = $deliveredOrders;

        // Giá trị trung bình mỗi đơn (AOV) dựa trên đơn Delivered
        $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

        // 4. Dữ liệu cho biểu đồ theo tháng trong năm
        $rawMonthlyStats = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN 1 ELSE 0 END) as delivered_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $cancelledStatusId . ' THEN 1 ELSE 0 END) as cancelled_orders'),
                DB::raw('SUM(CASE WHEN id_order_status = ' . $deliveredStatusId . ' THEN total ELSE 0 END) as month_revenue')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('month');

        $monthlyStats = [];
        for ($m = 1; $m <= 12; $m++) {
            if ($rawMonthlyStats->has($m)) {
                $monthlyStats[$m] = [
                    'month'           => $m,
                    'total_orders'    => $rawMonthlyStats[$m]->total_orders,
                    'delivered_orders' => $rawMonthlyStats[$m]->delivered_orders,
                    'cancelled_orders' => $rawMonthlyStats[$m]->cancelled_orders,
                    'month_revenue'   => $rawMonthlyStats[$m]->month_revenue,
                ];
            } else {
                $monthlyStats[$m] = [
                    'month'           => $m,
                    'total_orders'    => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'month_revenue'   => 0,
                ];
            }
        }

        // Tính % tăng giảm doanh thu so với tháng trước
        $monthlyStatsWithComparison = [];
        $prevRevenue = null;
        foreach ($monthlyStats as $m => $data) {
            if (is_null($prevRevenue) || $prevRevenue == 0) {
                $data['pct_change'] = null;
            } else {
                $data['pct_change'] = (($data['month_revenue'] - $prevRevenue) / $prevRevenue) * 100;
            }
            $prevRevenue = $data['month_revenue'];
            $monthlyStatsWithComparison[] = $data;
        }

        // 5. Top 10 sản phẩm bán chạy trong năm (chỉ tính đơn Delivered)
        $productsSales = DB::table('order_details')
            ->join('orders', 'order_details.id_order', '=', 'orders.id')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // 6. Top 20 người mua nhiều nhất trong năm (chỉ tính đơn Delivered)
        $topCustomers = DB::table('orders')
            ->select('id_user', 'user_name', DB::raw('SUM(total) as total_purchase'))
            ->whereBetween('created_at', $dateRange)
            ->where('id_order_status', $deliveredStatusId)
            ->groupBy('id_user', 'user_name')
            ->orderByDesc('total_purchase')
            ->limit(20)
            ->get();

        // 7. Chi tiết sản phẩm mỗi khách hàng đã mua (chỉ tính các đơn Delivered)
        $customerProducts = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.id_order')
            ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
            ->join('products', 'product_variants.id_product', '=', 'products.id')
            ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as quantity'))
            ->whereBetween('orders.created_at', $dateRange)
            ->where('orders.id_order_status', $deliveredStatusId)
            ->groupBy('orders.id_user', 'products.name')
            ->get();
        $customerProductsGrouped = $customerProducts->groupBy('id_user');

        return view('admin.statistics.yearly_statistics', compact(
            'selectedYear',
            'startOfYear',
            'endOfYear',
            'allTotalOrders',
            'totalRevenue',
            'deliveredOrders',
            'cancelledOrders',
            'averageOrderValue',
            'monthlyStatsWithComparison',
            'productsSales',
            'totalSuccessfulOrders',
            'topCustomers',
            'customerProductsGrouped'
        ));
    }
    // api biểu đồ sản phẩm bán chạy nhất
    public function topSaleProducts(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $top_sale_products = $this->product->top_sale_products($start_date, $end_date);
        return response()->json($top_sale_products);
    }

    // tạo api cho biểu đồ sản phẩm đã bán
    public function productSold(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $top_sale_products = $this->product->productSold($start_date, $end_date);
        return response()->json($top_sale_products);
    }

    //tạo api cho biểu đồ danh mục sản phẩm
    public function categoryStatistics(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $query = Category::select('categories.name')
            ->leftJoin('products', 'categories.id', '=', 'products.id_category')
            ->whereNull('categories.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('COUNT(products.id) as total_products');

        if ($start_date && $end_date) {
            $query->whereBetween('products.created_at', [$start_date, $end_date]);
        }

        $categories = $query->orderBy('total_products', 'desc')->get();

        return response()->json($categories);
    }
}
