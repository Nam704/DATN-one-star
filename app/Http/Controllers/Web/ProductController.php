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

        return view('admin.product.detail')
            ->with([
                'product' => $product
            ]);
    }

    public function list()
    {
        $products = $this->ProductService->list();
        $categories = Category::select('id', 'name')->where('status', 'Active')->get();
        $brands = Brand::select('id', 'name')->where('status', 'Active')->get();

        return view('admin.product.list', compact('products', 'categories', 'brands'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
            'product_images' => 'required|array',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedImages = [];
        foreach ($request->file('product_images') as $file) {
            $path = $file->store('products', 'public');
            $uploadedImages[$file->getClientOriginalName()] = $path;
        }
        $this->ProductService->createProductByExcel($request->excel_file, $uploadedImages);

        return back()->with('success', 'Sản phẩm đã được nhập thành công!');
    }

    public function exportCreateExcel()
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
        try {
            // Lấy toàn bộ dữ liệu từ request
            $data = $request->all();

            // Decode các trường attributes từ chuỗi JSON thành mảng
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $index => &$variant) {
                    if (isset($variant['attributes']) && is_string($variant['attributes'])) {
                        $variant['attributes'] = json_decode($variant['attributes'], true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new ValidationException(Validator::make([], []), [
                                "variants.{$index}.attributes" => ["Dữ liệu thuộc tính không phải là JSON hợp lệ."]
                            ]);
                        }
                    }
                }
                unset($variant); // Xóa tham chiếu
            }

            // Định nghĩa các quy tắc validate
            $rules = [
                'name' => 'required|string|max:255|unique:products,name',
                'id_category' => 'required|exists:categories,id',
                'id_brand' => 'required|exists:brands,id',
                'description' => 'nullable|string',
                'image_primary' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variants' => 'required|array|max:50',
                'variants.*.code' => 'required|string|max:50',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.quantity' => 'required|numeric|min:0',
                'variants.*.attributes' => 'required|array|min:1',
                'variants.*.attributes.*.attribute_id' => 'required|exists:attributes,id',
                'variants.*.attributes.*.value.id_value' => 'required|exists:attribute_values,id',
            ];

            // Thêm validate cho các ảnh biến thể (image_variant_0, image_variant_1, ...)
            foreach ($data['variants'] as $index => $variant) {
                $rules["image_variant_{$index}"] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            }

            $validator = Validator::make($data, $rules, [
                'name.required' => 'Tên sản phẩm là bắt buộc.',
                'name.unique' => 'Tên sản phẩm đã tồn tại, vui lòng chọn tên khác.',
                'id_category.required' => 'Danh mục là bắt buộc.',
                'id_category.exists' => 'Danh mục không tồn tại.',
                'id_brand.required' => 'Thương hiệu là bắt buộc.',
                'id_brand.exists' => 'Thương hiệu không tồn tại.',
                'image_primary.required' => 'Ảnh chính của sản phẩm là bắt buộc.',
                'image_primary.image' => 'Ảnh chính phải là một file ảnh.',
                'variants.required' => 'Sản phẩm phải có ít nhất một biến thể.',
                'variants.max' => 'Số lượng biến thể không được vượt quá 50.',
                'variants.*.code.required' => 'Mã SKU của biến thể là bắt buộc.',
                'variants.*.price.required' => 'Giá của biến thể là bắt buộc.',
                'variants.*.quantity.required' => 'Số lượng của biến thể là bắt buộc.',
                'variants.*.attributes.required' => 'Biến thể phải có ít nhất một thuộc tính.',
                'variants.*.attributes.*.attribute_id.exists' => 'Thuộc tính không tồn tại.',
                'variants.*.attributes.*.value.id_value.exists' => 'Giá trị thuộc tính không tồn tại.',
                'image_variant_*' => 'Ảnh của biến thể là bắt buộc và phải là file ảnh (jpeg, png, jpg, gif) với kích thước tối đa 2MB.',
            ]);

            // Nếu validation thất bại, ném ngoại lệ
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Chuẩn bị dữ liệu để gửi vào service
            $preparedData = [
                'name' => $data['name'],
                'id_category' => $data['id_category'],
                'id_brand' => $data['id_brand'],
                'description' => $data['description'] ?? '',
                'image_primary' => $request->file('image_primary'),
                'images' => $request->file('images') ?? [],
                'variants' => array_map(function ($variant, $index) use ($request) {
                    return [
                        'code' => $variant['code'],
                        'price' => $variant['price'],
                        'quantity' => $variant['quantity'] ?? 0,
                        'attributes' => $variant['attributes'],
                        'image' => $request->file("image_variant_{$index}"),
                    ];
                }, $data['variants'], array_keys($data['variants'])),
            ];

            // Gọi ProductService để tạo sản phẩm
            $productData = $this->ProductService->createProduct($preparedData);

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được tạo thành công!',
                'product' => $productData
            ], 201);
        } catch (ValidationException $e) {
            // Xử lý lỗi validation
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Xử lý lỗi cơ sở dữ liệu
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'product_variants_sku_unique') !== false) {
                // Lỗi trùng lặp SKU
                preg_match("/Duplicate entry '(.+?)' for key/", $e->getMessage(), $matches);
                $duplicateSku = $matches[1] ?? 'không xác định';
                return response()->json([
                    'success' => false,
                    'message' => "Mã SKU '$duplicateSku' đã tồn tại, vui lòng sử dụng mã khác.",
                    'errors' => ['sku' => ["Mã SKU '$duplicateSku' đã tồn tại."]]
                ], 422);
            }

            // Các lỗi cơ sở dữ liệu khác
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra với cơ sở dữ liệu, vui lòng thử lại sau.',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        } catch (\Exception $e) {
            // Xử lý các lỗi khác
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo sản phẩm, vui lòng thử lại.',
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function edit($id)
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

        $prepareData = $this->ProductService->prepareData();
        $categories = $prepareData['categories'];
        $brands = $prepareData['brands'];
        $attributes = $prepareData['attributes'];

        return view('admin.product.edit')->with([
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'attributes' => $attributes
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = $this->ProductService->updateProduct($request->all(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function stas($id)
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

        $orderDetails = Order_detail::whereIn('id_variant', $product->variants->pluck('id'))
            ->with(['order', 'order.orderStatus'])
            ->get();

        $orderStatusStats = $orderDetails->groupBy('order.id_order_status')
            ->map(function ($orders, $statusId) {
                return [
                    'status_id' => $statusId,
                    'status_name' => $orders->first()->order->orderStatus->name,
                    'total_orders' => $orders->count(),
                    'total_amount' => $orders->sum('total')
                ];
            });

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

    public function variantDetails($productId, $variantId, Request $request)
    {
        $product = Product::findOrFail($productId);
        $variant = Product_variant::findOrFail($variantId);

        $orderDetails = Order_detail::where('id_variant', $variantId)
            ->with(['order.user', 'order.orderStatus', 'order.address'])
            ->get();

        $users = [];
        $allStatuses = [];

        foreach ($orderDetails as $orderDetail) {
            $userId = $orderDetail->order->user->id;
            $orderId = $orderDetail->order->id;
            $statusName = $orderDetail->order->orderStatus->name;

            $address = json_decode($orderDetail->order->address_data);
            $province = $address->name_province ?? 'N/A';
            $district = $address->name_district ?? 'N/A';
            $ward = $address->name_ward ?? 'N/A';
            $addressDetail = $address->address_detail ?? 'N/A';

            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'user' => $orderDetail->order->user,
                    'address' => [
                        'province' => $province,
                        'district' => $district,
                        'ward' => $ward,
                        'address_detail' => $addressDetail,
                    ],
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

            if (!in_array($statusName, $allStatuses)) {
                $allStatuses[] = $statusName;
            }
        }

        $selectedStatus = $request->query('status');

        return view('admin.product.productVariantDetail', [
            'product' => $product,
            'variant' => $variant,
            'users' => array_values($users),
            'allStatuses' => $allStatuses,
            'selectedStatus' => $selectedStatus
        ]);
    }

    public function lock($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'inactive';
        $product->delete();
        $product->save();

        DB::transaction(function () use ($product) {
            $variantIds = $product->variants()->pluck('id');
            Cart_details::whereIn('id_variant', $variantIds)->delete();
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
            $product->restore();
            $product->status = 'active';
            $product->save();
            $product->variants()->onlyTrashed()->restore();
            $product->variants()->update(['status' => 'active']);
        });

        return redirect()->route('admin.products.list')->with('success', 'Sản phẩm đã được khôi phục và kích hoạt!');
    }

    public function filter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'nullable|exists:categories,id',
            'brand' => 'nullable|exists:brands,id',
            'stock' => 'nullable|in:in_stock,out_of_stock,low_stock,quantity',
            'quantity' => 'nullable|required_if:stock,quantity|integer|min:0',
            'sort_view' => 'nullable|in:asc,desc',
            'min_price' => 'nullable|numeric|min:0|max:100000000',
            'max_price' => 'nullable|numeric|min:0|max:100000000|gte:min_price',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date|after_or_equal:created_from|before_or_equal:today',
        ], [
            'quantity.required_if' => 'Khi chọn “Số lượng cụ thể” bạn phải nhập số lượng.',
            'max_price.gte' => 'Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu.',
            'created_to.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'created_to.before_or_equal' => 'Ngày kết thúc không được lớn hơn hôm nay.',
            'min_price.max' => 'Giá tối thiểu không được vượt quá 100.000.000.',
            'max_price.max' => 'Giá tối đa không được vượt quá 100.000.000.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

        $query->getQuery()->orders = null;

        if ($request->filled('sort_view')) {
            if ($request->sort_view === 'asc') {
                $query->orderBy('view', 'asc');
            } elseif ($request->sort_view === 'desc') {
                $query->orderBy('view', 'desc');
            }
        }

        if (!$request->filled('sort_view')) {
            $query->orderBy('id', 'desc');
        }

        $from = $request->input('created_from');
        $to = $request->input('created_to');

        if ($from) {
            $query->whereDate('products.created_at', '>=', $from);
            $to = $to ?: now()->toDateString();
        }
        if ($to) {
            $query->whereDate('products.created_at', '<=', $to);
        }

        $products = $query->get();

        return view('admin.product.product_table', compact('products'))->render();
    }
}
