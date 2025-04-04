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
            $countData = [
                "product" => $this->product->count(),
                "revenue" => $this->order->where('id_order_status', '4')->sum('total'),
                "order" => $this->order->where('id_order_status', '4')->count(),
                "user" => $this->user->count()
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
