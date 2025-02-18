<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product_variant;
use App\Services\ProductService;
use App\Imports\CreateProductImport;
use App\Models\Image;
use App\Models\Product_albums;
use App\Models\Product_variant_attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    protected $ProductService;


    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }
    public function detail($id)
    {
        $product = Product::getProductWithDetails($id)->findOrFail($id);

        $product->variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => $variant->quantity,
                'image' => optional($variant->images)->url,
                'values' => $variant->attributeValues->map(function ($attr) {
                    return [
                        'value_id' => $attr->id,
                        'attribute_id' => $attr->attribute_id,
                        'name' => $attr->name,
                        'value' => $attr->value,

                    ];
                })
            ];
        });

        // return $product;
        return view('admin.product.detail')
            ->with([
                'product' => $product
            ]);
    }
    public function list()
    {

        $products = $this->ProductService->list();
        return view('admin.product.list', compact('products'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Lưu ảnh vào storage
        $uploadedImages = [];
        foreach ($request->file('product_images') as $file) {
            $path = $file->store('products', 'public');
            $uploadedImages[$file->getClientOriginalName()] = $path;
        }
        $this->ProductService->createProductByExcel($request->excel_file, $uploadedImages);
        // dd($uploadedImages);
        // Nhập dữ liệu từ file Excel
        // Excel::import(new CreateProductImport($uploadedImages), $request->file('excel_file'));

        return back()->with('success', 'Sản phẩm đã được nhập thành công!');
    }
    function exportCreateExcel()
    {
        return $this->ProductService->exportProducts();
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
    public function edit($id)
    {
        // $product = Product::with(['variants.images'])->findOrFail($id);
        $product = Product::getProductWithDetails($id)->findOrFail($id);

        $product->variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'quantity' => $variant->quantity,
                'image' => optional($variant->images)->url,
                'values' => $variant->attributeValues->map(function ($attr) {
                    return [
                        'value_id' => $attr->id,
                        'attribute_id' => $attr->attribute_id,
                        'name' => $attr->name,
                        'value' => $attr->value,

                    ];
                })
            ];
        });
        $prepareData = $this->ProductService->prepareData();
        $categories = $prepareData['categories'];
        $brands = $prepareData['brands'];
        $attributes = $prepareData['attributes'];

        return
            view('admin.product.edit')->with([
                'product' => $product,
                'categories' => $categories,
                'brands' => $brands,
                'attributes' => $attributes
            ]);
    }

    public function update(Request $request, $id)
    {

        $product = $this->ProductService->updateProduct($request, $id);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function productDetail($id)
    {
        $product = Product::with(['brand', 'category'])->findOrFail($id);
        $variants = Product_variant::where('id_product', $id)->get();

        foreach ($variants as $variant) {
            $variant->attributes = Product_variant_attribute::where('id_product_variant', $variant->id)
                ->with('attributeValue.attribute')
                ->get();

            $variant->images = Image::where('id_product_variant', $variant->id)->get();
        }
        $albums = Product_albums::where('id_product', $id)->get();


        $relatedProducts = Product::where('id', '!=', $id)
            ->where(function ($query) use ($product) {
                $query->where('id_brand', $product->id_brand) // Lấy sản phẩm cùng brand
                    ->orWhere('id_category', $product->id_category); // Hoặc cùng danh mục
            })
            ->limit(4) // Giới hạn số lượng sản phẩm liên quan
            ->get();



        return view('client.productDetail', compact('product', 'variants', 'albums', 'relatedProducts'));

    }

    public function addCart(Request $request)
    {

        var_dump($request->all());

    }

    public function checkoutProduct()
    {
        return view('client.checkout');
    }
}
