<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $product;
    function __construct(Product $product)
    {
        $this->product = $product;
    }
    function total()
    {
        $total =  $this->product->total()->count();
        return response()->json(['total' => $total]);
    }
    function list()
    {
        $products = $this->product->list()->get();
        return response()->json($products);
    }
}
