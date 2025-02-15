<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product_variant;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $ProductService;


    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }
    public function list()
    {
        $products = Product::with(['brand', 'category'])->get();;
        return view('admin.product.index')->with([
            'products' => $products
        ]);
    }

    public function create()
    {
        $prepareData = $this->ProductService->prepareData();
        $categories = $prepareData['categories'];
        $brands = $prepareData['brands'];
        $attributes = $prepareData['attributes'];
        return view('admin.product.add')->with(
            [
                'categories' => $categories,
                'brands' => $brands,
                'attributes' => $attributes
            ]
        );
    }
    public function store(Request $request)
    {
        $productData = $this->ProductService->createProduct($request);
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $productData
        ]);
    }
}
