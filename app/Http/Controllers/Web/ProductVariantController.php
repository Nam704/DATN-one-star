<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function list($id)
    {
        $product = Product::find($id);
        $productVariants = Product_variant::list($id)->get();
        // dd($productVariants);
        return view('admin.product_variant.list', compact('productVariants', 'product'));
    }
}
