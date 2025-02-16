<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $product;
    protected $ProductService;

    function __construct(
        Product $product,
        ProductService $ProductService
    ) {
        $this->product = $product;
        $this->ProductService = $ProductService;
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
