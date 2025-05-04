<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
    public function dashboard()
    {
        if (auth()->check()) {
            $start_date = Carbon::today()->startOfDay();
            $end_date = Carbon::today()->endOfDay();
            $countData = [
                "product" => $this->product->whereBetween('created_at', [$start_date, $end_date])->count(),
                "revenue" => $this->order
                    ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
                    ->whereNotIn('order_statuses.name', [
                        'Cancelled', 
                        'Cancel Requested', 
                        'Cancel Under Review', 
                        'Cancel Approved', 
                        'Cancel Rejected', 
                        'Failed Delivery'
                    ])
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->sum('orders.total'),

                "order" => $this->order
                    ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->count(),

                "user" => $this->user->whereBetween('created_at', [$start_date, $end_date])->count()
            ];
            // 2. Tính topUserPurchases *tĩnh* (loại trừ Cancelled)
            $cancelledStatusId = DB::table('order_statuses')
                ->where('name', 'Cancelled')
                ->value('id');

            // Top 10 users
            // Top 10 users
            $topUsers = DB::table('orders')
                ->leftJoin('users', 'orders.id_user', '=', 'users.id')
                ->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.id_order_status', '!=', $cancelledStatusId)
                ->select(
                    'orders.id_user',
                    DB::raw('MAX(COALESCE(users.name, JSON_UNQUOTE(JSON_EXTRACT(orders.user_data, "$.user_name")))) as user_name'),
                    DB::raw('MAX(COALESCE(users.email, JSON_UNQUOTE(JSON_EXTRACT(orders.user_data, "$.email")))) as email'),
                    DB::raw('MAX(COALESCE(users.phone, JSON_UNQUOTE(JSON_EXTRACT(orders.user_data, "$.phone")))) as phone'),
                    DB::raw('SUM(orders.total) as total_purchase')
                )
                ->groupBy('orders.id_user')
                ->orderByDesc('total_purchase')
                ->limit(10)
                ->get();


            // Chi tiết sản phẩm
            $productRows = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.id_order')
                ->join('product_variants', 'order_details.id_variant', '=', 'product_variants.id')
                ->join('products', 'product_variants.id_product', '=', 'products.id')
                ->whereBetween('orders.created_at', [$start_date, $end_date])
                ->where('orders.id_order_status', '!=', $cancelledStatusId)
                ->select('orders.id_user', 'products.name as product_name', DB::raw('SUM(order_details.quantity) as qty'))
                ->groupBy('orders.id_user', 'products.name')
                ->get()
                ->groupBy('id_user');

                $userStats = $topUsers->map(function ($u) use ($productRows) {
                    $prods = $productRows[$u->id_user] ?? collect();
                    $list  = $prods->map(fn($r) => "{$r->product_name} ({$r->qty})")->implode(', ');
                    return [
                        'id_user'         => $u->id_user,       
                        'user_name'       => $u->user_name,
                        'email'           => $u->email ?: '-',
                        'phone'           => $u->phone ?: '-',
                        'products_bought' => $list ?: '-',
                        'total_purchase'  => $u->total_purchase,
                    ];
                });
            return view('admin.index', compact(
                'countData',
                'userStats',
            ));
        } else {
            return redirect()->route('auth.getFormLogin');
        }
    }
    public function orderStatusStatistics()
    {
        $orderStatusStats = Order::selectRaw('id_order_status, COUNT(*) as total')
            ->groupBy('id_order_status')
            ->with('orderStatus:id,name') // Ensure you get status names
            ->get()
            ->map(function ($order) {
                return [
                    'status' => $order->orderStatus->name ?? 'Unknown',
                    'total' => $order->total
                ];
            });

        return response()->json($orderStatusStats);
    }

    public function dailyStatistics_Dashboard()
    {
        $statusData = Order::whereDate('created_at', Carbon::today())
            ->selectRaw('id_order_status, COUNT(*) as total')
            ->groupBy('id_order_status')
            ->with('orderStatus')
            ->get();

        // Trả về JSON thay vì view
        return response()->json($statusData->map(function ($item) {
            return [
                'status' => $item->orderStatus->name ?? 'Không xác định',
                'total' => $item->total,
            ];
        }));
    }


    public function weeklyOrderStats()
    {
        // Lấy các đơn hàng trong tháng hiện tại
        $orders = Order::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])
            ->get()
            ->groupBy(function ($order) {
                // Nhóm theo tuần trong tháng (từ 1 đến 5)
                return Carbon::parse($order->created_at)->weekOfMonth;
            });

        // Tạo mảng kết quả với 4 tuần (nếu tuần nào không có đơn, mặc định là 0)
        $result = [];
        for ($week = 1; $week <= 4; $week++) {
            $total = isset($orders[$week]) ? $orders[$week]->count() : 0;
            $result[] = [
                'label' => "Tuần $week",
                'total' => $total,
            ];
        }

        return response()->json($result);
    }

    public function checkOrderStatuses()
{
    $orderStatuses = DB::table('order_statuses')->pluck('name');
    dd($orderStatuses);  // In ra tất cả tên trạng thái để kiểm tra
}
}
