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
use App\Models\Cart_details;
use App\Models\Order_detail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        $categories = Category::select('id', 'name')->where('status', 'Active')->get();
        $brands     = Brand::select('id', 'name')->where('status', 'Active')->get();
        // dd(compact('categories', 'brands'));

        return view('admin.product.list', compact('products', 'categories', 'brands'));
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
                        'name' => $attr->attribute_name,
                        'value' => $attr->value,

                    ];
                })
            ];
        });
        // return $product->variants;
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
            ->with(['order.user', 'order.orderStatus', 'order.address']) // Lấy thông tin đơn hàng, người dùng và trạng thái
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
    public function lock($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'inactive';
        $product->delete(); // Xóa mềm sản phẩm
        $product->save();

        // Xóa các mục trong giỏ hàng liên quan đến sản phẩm này
        DB::transaction(function () use ($product) {
            $variantIds = $product->variants()->pluck('id'); // Lấy danh sách id_variant của sản phẩm
            Cart_details::whereIn('id_variant', $variantIds)->delete(); // Xóa các mục trong CartDetail
        });

        return redirect()->route('admin.products.list')->with('success', 'Ngừng bán sản phẩm và đã xóa khỏi giỏ hàng của người dùng');
    }

    public function trash()
    {
        $products = Product::onlyTrashed()->get();
        return view('admin.product.listlock', compact('products'));
    }
    public function openProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($product) {
            // Khôi phục sản phẩm
            $product->restore();
            // Cập nhật trạng thái thành active
            $product->status = 'active';
            $product->save();

            // (Tùy chọn) Khôi phục các biến thể liên quan nếu chúng cũng bị xóa mềm
            $product->variants()->onlyTrashed()->restore();
            $product->variants()->update(['status' => 'active']);
        });

        return redirect()->route('admin.products.list')->with('success', 'Sản phẩm đã được khôi phục và kích hoạt!');
    }
    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category'     => 'nullable|exists:categories,id',
            'brand'        => 'nullable|exists:brands,id',
            'stock'        => 'nullable|in:in_stock,out_of_stock,low_stock,quantity',
            'quantity'     => 'nullable|required_if:stock,quantity|integer|min:0',
            'sort_view'    => 'nullable|in:asc,desc',
            'min_price'    => 'nullable|numeric|min:0',
            'max_price'    => 'nullable|numeric|min:0|gte:min_price',
            'created_from' => 'nullable|date',
            'created_to'   => 'nullable|date|after_or_equal:created_from|before_or_equal:today',
        ], [
            'quantity.required_if'         => 'Khi chọn “Số lượng cụ thể” bạn phải nhập số lượng.',
            'max_price.gte'                => 'Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu.',
            'created_to.after_or_equal'    => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'created_to.before_or_equal'   => 'Ngày kết thúc không được lớn hơn hôm nay.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $query = (new Product)->listActive();

        if ($request->filled('category')) {
            $query->where('id_category', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('id_brand', $request->brand);
        }

        if ($request->stock === 'in_stock') {
            $query->having('total_quantity', '>', 0);
        } elseif ($request->stock === 'out_of_stock') {
            $query->having('total_quantity', '=', 0);
        } elseif ($request->stock === 'low_stock') {
            $query->having('total_quantity', '>', 0)
                ->having('total_quantity', '<=', 10);
        } elseif ($request->stock === 'quantity' && $request->filled('quantity')) {
            $query->having('total_quantity', '=', (int)$request->quantity);
        }
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $min = $request->min_price;
            $max = $request->max_price;
            $query->whereHas('variants', function ($q) use ($min, $max) {
                $q->whereBetween('price', [$min, $max]);
            });
        } elseif ($request->filled('min_price')) {
            $min = $request->min_price;
            $query->whereHas('variants', function ($q) use ($min) {
                $q->where('price', '>=', $min);
            });
        } elseif ($request->filled('max_price')) {
            $max = $request->max_price;
            $query->whereHas('variants', function ($q) use ($max) {
                $q->where('price', '<=', $max);
            });
        }

        // 1. Xóa hết orderBy cũ nếu có
        $query->getQuery()->orders = null;

        // 2. Apply order theo View nếu được chọn
        if ($request->filled('sort_view')) {
            if ($request->sort_view === 'asc') {
                $query->orderBy('view', 'asc');
            } elseif ($request->sort_view === 'desc') {
                $query->orderBy('view', 'desc');
            }
        }

        // 3. Nếu không có sort_view, mặc định sort theo id DESC
        if (!$request->filled('sort_view')) {
            $query->orderBy('id', 'desc');
        }
        $from = $request->input('created_from');
        $to   = $request->input('created_to');

        if ($from) {
            $query->whereDate('products.created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('products.created_at', '<=', $to);
        }

        $products = $query->get();

        return view('admin.product.product_table', compact('products'))->render();
    }
}
