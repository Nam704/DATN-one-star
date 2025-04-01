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
            "product" => $this->product->whereDate('created_at', Carbon::today())->count(),
        ];
            return view('admin.index', compact(
                'countData',
            ));
        } else {
            return redirect()->route('auth.getFormLogin');
        }
    }


}
