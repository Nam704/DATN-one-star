<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function detail($id)
    {
        $product = $this->productService->productDetail($id);
        // return $product;
        return view('client.detail.index', compact('product'));
    }
}
