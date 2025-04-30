<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductDashboardController extends Controller
{
    private $product;

    public function __construct( Product $product)
    {
        $this->product = $product;
    }
    public function dashboardProduct()
    {
        $countData = [
            "product" => $this->product->whereDate('created_at', Carbon::today())->count(),
        ];
            return view('admin.index', compact(
                'countData',
            ));
    }
     // api biểu đồ sản phẩm bán chạy nhất
     public function topSaleProducts(Request $request)
     {
         $start_date = $request->start_date;
         $end_date = $request->end_date;
         $top_sale_products = $this->product->top_sale_products_today($start_date, $end_date);
         return response()->json($top_sale_products);
     }
 
     public function topViewProducts(Request $request)
     {
         $start_date = $request->start_date;
         $end_date = $request->end_date;
         $top_view_product = $this->product->top_view_product_today($start_date, $end_date);
         return response()->json($top_view_product);
     }
 
     public function topLeastProducts(Request $request)
     {
         $start_date = $request->start_date;
         $end_date = $request->end_date;
         $top_least_products = $this->product->least_sold_products_today($start_date, $end_date);
         return response()->json($top_least_products);
     }

     public function lowStockProducts(Request $request)
     {
         $start_date = $request->start_date;
         $end_date = $request->end_date;
         $low_stock_products = $this->product->low_stock_products($start_date, $end_date);
         return response()->json($low_stock_products);
     }
}
