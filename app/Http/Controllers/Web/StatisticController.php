<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
                    'name' => 'Product 1',
                    'image_primary' => 'product1.jpg',
                    'total_comments' => 100,
                ],
                [
                    'name' => 'Product 2',
                    'image_primary' => 'product2.jpg',
                    'total_comments' => 80,
                ],
                [
                    'name' => 'Product 3',
                    'image_primary' => 'product3.jpg',
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

}
