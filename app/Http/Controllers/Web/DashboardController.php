<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;

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
                    ->where('order_statuses.name', 'Delivered')  // Thay thế 'Delivered' bằng tên trạng thái bạn muốn
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->sum('orders.total'),

                "order" => $this->order
                    ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id')
                    ->where('order_statuses.name', 'Delivered')  // Thay thế 'Delivered' bằng tên trạng thái bạn muốn
                    ->whereBetween('orders.created_at', [$start_date, $end_date])
                    ->count(),
                "user" => $this->user->whereBetween('created_at', [$start_date, $end_date])->count()
            ];
            return view('admin.index', compact(
                'countData',
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



}
