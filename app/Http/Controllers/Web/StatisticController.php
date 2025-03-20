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

}
