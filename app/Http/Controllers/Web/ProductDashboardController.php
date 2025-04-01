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
}
