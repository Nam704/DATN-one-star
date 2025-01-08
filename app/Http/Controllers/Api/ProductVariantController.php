<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product_variant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    protected $product_variant;
    function __construct(Product_variant $product_variant)
    {
        $this->product_variant = $product_variant;
    }
    public function getProductVariants($idProduct)
    {
        $variants = Product_variant::list($idProduct)->get();

        return response()->json($variants);
    }
    function total($idProduct)
    {
        $total =  $this->product_variant->total($idProduct)->count();
        return response()->json(['total' => $total]);
    }
}
