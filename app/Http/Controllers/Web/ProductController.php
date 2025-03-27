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
use App\Models\Order_detail;
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
        $product = Product::findOrFail($id);
        $product->getProductWithDetails();
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
                        'name' => $attr->attribute_name,
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
        return  $this->ProductService->exportProducts();
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
        // $product = Product::getProductWithDetails($id)->findOrFail($id);
        $product = Product::findOrFail($id);
        $product->getProductWithDetails();

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

    public function stas($id)
    {
        // Lấy thông tin sản phẩm với các biến thể
        // $product = Product::getProductWithDetails($id)->findOrFail($id);
        $product = Product::findOrFail($id);
        $product->getProductWithDetails();

        // Map các biến thể sản phẩm
        $product->variants = $product->variants->map(function ($variant) {
            // dd($variant->attributeValues);
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
                        'name' => $attr->attribute_name,
                        'value' => $attr->value,
                    ];
                })
            ];
        });

        // Lấy thông tin đơn hàng liên quan đến sản phẩm
        $orderDetails = Order_detail::whereIn('id_variant', $product->variants->pluck('id'))
            ->with(['order', 'order.orderStatus'])
            ->get();

        // Thống kê trạng thái đơn hàng
        $orderStatusStats = $orderDetails->groupBy('order.id_order_status')
            ->map(function ($orders, $statusId) {
                return [
                    'status_id' => $statusId,
                    'status_name' => $orders->first()->order->orderStatus->name,
                    'total_orders' => $orders->count(),
                    'total_amount' => $orders->sum('total')
                ];
            });

        // Thống kê biến thể sản phẩm
        $variantStats = $orderDetails->groupBy('id_variant')
            ->map(function ($orders, $variantId) {
                return [
                    'variant_id' => $variantId,
                    'total_orders' => $orders->count(),
                    'total_quantity' => $orders->sum('quantity'),
                    'total_amount' => $orders->sum('total')
                ];
            });

        return view('admin.product.stas')
            ->with([
                'product' => $product,
                'orderStatusStats' => $orderStatusStats,
                'variantStats' => $variantStats
            ]);
    }


    // public function variantDetails($productId, $variantId)
    // {
    //     // Lấy thông tin sản phẩm
    //     $product = Product::findOrFail($productId);

    //     // Lấy thông tin biến thể
    //     $variant = Product_variant::findOrFail($variantId);

    //     // Lấy danh sách đơn hàng liên quan đến biến thể này
    //     $orderDetails = Order_detail::where('id_product_variant', $variantId)
    //         ->with(['order.user']) // Sử dụng mối quan hệ order.user
    //         ->get();

    //     // Nhóm danh sách người dùng đã đặt hàng
    //     $users = $orderDetails->map(function ($orderDetail) {
    //         return $orderDetail->order->user;
    //     })->unique();

    //     return view('admin.product.productVariantDetail')
    //         ->with([
    //             'product' => $product,
    //             'variant' => $variant,
    //             'users' => $users
    //         ]);
    // }




    public function variantDetails($productId, $variantId, Request $request)
{
    // Lấy thông tin sản phẩm
    $product = Product::findOrFail($productId);

    // Lấy thông tin biến thể
    $variant = Product_variant::findOrFail($variantId);

    // Lấy danh sách đơn hàng liên quan đến biến thể này
    $orderDetails = Order_detail::where('id_variant', $variantId)
        ->with(['order.user', 'order.orderStatus']) // Lấy thông tin đơn hàng, người dùng và trạng thái
        ->get();

    // Nhóm đơn hàng theo người dùng
    $users = [];
    $allStatuses = []; // Mảng chứa tất cả trạng thái đơn hàng

    foreach ($orderDetails as $orderDetail) {
        $userId = $orderDetail->order->user->id;
        $orderId = $orderDetail->order->id;
        $statusName = $orderDetail->order->orderStatus->name;

        if (!isset($users[$userId])) {
            $users[$userId] = [
                'user' => $orderDetail->order->user,
                'address' => $orderDetail->order->address,
                'orders' => []
            ];
        }

        if (!isset($users[$userId]['orders'][$orderId])) {
            $users[$userId]['orders'][$orderId] = [
                'order_id' => $orderId,
                'statuses' => []
            ];
        }

        if (!in_array($statusName, $users[$userId]['orders'][$orderId]['statuses'])) {
            $users[$userId]['orders'][$orderId]['statuses'][] = $statusName;
        }

        // Thêm trạng thái vào danh sách tất cả trạng thái (loại bỏ trùng lặp)
        if (!in_array($statusName, $allStatuses)) {
            $allStatuses[] = $statusName;
        }
    }

    // Lấy trạng thái được chọn từ request
    $selectedStatus = $request->query('status');

    return view('admin.product.productVariantDetail', [
        'product' => $product,
        'variant' => $variant,
        'users' => array_values($users), // Chuyển từ mảng kết hợp sang mảng tuần tự
        'allStatuses' => $allStatuses, // Danh sách tất cả trạng thái
        'selectedStatus' => $selectedStatus // Trạng thái được chọn
    ]);
}



}
