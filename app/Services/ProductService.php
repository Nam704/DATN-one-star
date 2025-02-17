<?php

namespace App\Services;

use App\Exports\ProductExport;
use App\Imports\CreateProductImport;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductService
{
    protected $AttributeService;
    protected $BrandService;
    protected $CategoryService;
    protected $product;
    protected $CreateProductImport;
    protected $ProductAuditService;
    protected $NotificationService;
    protected $user;
    public function __construct(
        AttributeService $AttributeService,
        BrandService $BrandService,
        CategoryService $CategoryService,
        CreateProductImport $CreateProductImport,
        ProductAuditService $ProductAudit,
        NotificationService $NotificationService
    ) {
        $this->AttributeService = $AttributeService;
        $this->BrandService = $BrandService;
        $this->CategoryService = $CategoryService;
        $this->CreateProductImport = $CreateProductImport;
        $this->ProductAuditService = $ProductAudit;
        $this->NotificationService = $NotificationService;
        $this->product = new Product();
    }
    public function updatePrice($variantId, $price)
    {
        $productVariant = Product_variant::findOrFail($variantId);
        $productVariant->price = $price;
        $productVariant->save();
        return $productVariant;
    }
    public function exportProducts()
    {
        return Excel::download(new ProductExport, 'product_template.xlsx');
    }
    public function createProductByExcel($file, $images)
    {
        $data = Excel::toArray([], $file);

        $importData =   $this->CreateProductImport->process($data[0], $data[1], $images);
        return $importData;
    }
    public function list()
    {
        try {
            $products = $this->product->listActive()->get();
            return $products;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * Xử lý và định dạng dữ liệu sản phẩm từ request
     */
    public function processProductData(Request $request)
    {
        $formattedData = [
            '_token'       => $request->input('_token'),
            'name'         => $request->input('name'),
            'description'  => $request->input('description'),
            'id_category'  => $request->input('id_category'),
            'id_brand'     => $request->input('id_brand'),
            'image_primary' => $request->file('image_primary'),
            'images'       => $request->file('images', []),
        ];

        // Xử lý biến thể sản phẩm
        $variants = $request->input('variants', []);

        $formattedData['variants'] = array_map(function ($variant, $index) use ($request) {
            // Giải mã chuỗi JSON trong attributes
            $attributes = json_decode($variant['attributes'], true);

            // Lấy file ảnh từ request
            $imageKey = "image_variant_{$index}";
            $imageFile = $request->file($imageKey);

            return [
                'id'    => $variant['id'] ?? null,
                'code'       => $variant['code'],
                'price'      => $variant['price'] ?? 0,
                'attributes' => array_map(function ($attribute) {
                    return [
                        'attribute_id'   => $attribute['attribute_id'],
                        'attribute_name' => $attribute['attribute_name'],
                        'value'          => $attribute['value'],
                    ];
                }, $attributes),
                'image' => $imageFile ? $this->uploadImage($imageFile, 'products/variants') : null,
            ];
        }, $variants, array_keys($variants));

        return $formattedData;
    }

    /**
     * Tạo sản phẩm và lưu dữ liệu vào database
     */
    public function createProduct(Request $request)
    {
        try {
            $this->user = auth()->user();
            // Bắt đầu transaction
            DB::beginTransaction();

            // Xử lý dữ liệu sản phẩm từ request
            $formattedData = $this->processProductData($request);

            // 1. Tạo sản phẩm mới
            $product = Product::create([
                'name'        => $formattedData['name'],
                'description' => $formattedData['description'],
                'id_category' => $formattedData['id_category'],
                'id_brand'    => $formattedData['id_brand'],
                'image_primary'       => $this->uploadImage($request->file('image_primary'), 'products'),
            ]);

            // 2. Lưu danh sách ảnh sản phẩm (gallery)
            if (!empty($formattedData['images'])) {
                foreach ($formattedData['images'] as $image) {
                    $imagePath = $this->uploadImage($image, 'products/gallery');

                    $product->product_albums()->create(['image_path' => $imagePath]);
                }
            }

            // 3. Xử lý và lưu biến thể sản phẩm
            foreach ($formattedData['variants'] as $variant) {
                $variantModel = Product_variant::create([
                    'id_product' => $product->id,
                    'sku'        => $variant['code'],
                    'price'      => $variant['price'],
                ]);

                // Lưu ảnh biến thể (nếu có)
                if ($variant['image']) {
                    $variantModel->images()->create([
                        'url' => url('storage/' . $variant['image']),
                    ]);
                }

                // Lưu các thuộc tính của biến thể
                $attributeValuesToAttach = [];
                foreach ($variant['attributes'] as $attribute) {
                    $attributeValuesToAttach[] = $attribute['value']['id_value'];
                }

                // Gắn thuộc tính vào bảng trung gian
                $variantModel->attributeValues()->attach($attributeValuesToAttach);
                $this->ProductAuditService->createAudit([
                    'id_user' => $this->user->id,
                    'id_product_variant' => $variantModel->id,
                    'action_type' => 'create',
                    'status' => 'pending',
                    'reason' => "" //$this->user->name . " import" . " at: " . $today,
                ]);
            }

            // Commit transaction khi thành công
            DB::commit();
            $dataNotification = [
                'title' => 'New Product',
                'message' => $this->user->name . ' đã tạo sản phẩm mới!',
                'from_user_id' => $this->user->id,
                'to_user_id' => null,
                'type' => 'products',
                'status' => 'unread',
                'goto_id' => $product->id,

            ];
            // dd($dataNotification);
            $this->NotificationService->sendAdmin($dataNotification);
            return $product;
        } catch (\Exception $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xử lý upload ảnh sản phẩm
     */
    private function uploadImage($image, $folder = 'products')
    {
        if (!$image) {
            return null;
        }

        // Lưu ảnh vào thư mục được chỉ định
        return $image->store($folder, 'public');
    }

    /**
     * Chuẩn bị dữ liệu liên quan đến thuộc tính, thương hiệu, danh mục
     */
    public function prepareData()
    {
        return [
            'attributes' => $this->AttributeService->getAttributes(),
            'brands'     => $this->BrandService->getBrands(),
            'categories' => $this->CategoryService->getCategories(),
        ];
    }

    /**
     * Cập nhật số lượng tồn kho cho biến thể sản phẩm
     */
    public function updateStock($variantId, $quantity, $isIncrement = true)
    {
        $productVariant = Product_variant::findOrFail($variantId);

        if ($isIncrement) {
            $productVariant->quantity += $quantity;
        } else {
            $productVariant->quantity -= $quantity;
        }

        $productVariant->save();
        return $productVariant;
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            // Lấy sản phẩm cần cập nhật
            $product = Product::findOrFail($id);

            // Xử lý dữ liệu từ request
            $formattedData = $this->processProductData($request);
            Log::info($formattedData);
            // Nếu có ảnh mới, xóa ảnh cũ và cập nhật ảnh mới
            if ($request->hasFile('image_primary')) {
                // Xóa ảnh cũ
                if ($product->image_primary) {
                    Storage::delete($product->image_primary);
                }
                // Cập nhật ảnh mới
                $product->image_primary = $this->uploadImage($request->file('image_primary'), 'products');
            }

            // Cập nhật thông tin sản phẩm
            $product->update([
                'name'        => $formattedData['name'],
                'description' => $formattedData['description'],
                'id_category' => $formattedData['id_category'],
                'id_brand'    => $formattedData['id_brand'],
                'image_primary' => $product->image_primary,
            ]);

            // Cập nhật danh sách ảnh gallery (nếu có ảnh mới)
            if (!empty($formattedData['images'])) {
                // Xóa ảnh cũ trong gallery
                foreach ($product->product_albums as $album) {
                    Storage::delete($album->image_path);
                    $album->delete();
                }

                // Thêm ảnh mới vào gallery
                foreach ($formattedData['images'] as $image) {
                    $imagePath = $this->uploadImage($image, 'products/gallery');
                    $product->product_albums()->create(['image_path' => $imagePath]);
                }
            }

            // Cập nhật biến thể sản phẩm
            foreach ($formattedData['variants'] as $variant) {
                // Log::info($variant);
                // Kiểm tra xem biến thể đã tồn tại hay chưa
                if (!empty($variant['id'])) {
                    $variantModel = Product_variant::findOrFail($variant['id']);

                    $variantModel->update([
                        'sku'   => $variant['code'],
                        'price' => $variant['price']
                    ]);
                } else {
                    $variantModel = Product_variant::create([
                        'id_product' => $product->id,
                        'sku'        => $variant['code'],
                        'price'      => $variant['price']
                    ]);
                }


                // Nếu có ảnh biến thể mới, xóa ảnh cũ và cập nhật ảnh mới
                if (!empty($variant['image'])) {
                    if ($variantModel->images()->exists()) {
                        Storage::delete($variantModel->images()->first()->url);
                        $variantModel->images()->delete();
                    }
                    $variantModel->images()->create([
                        'url' => url('storage/' . $variant['image']),
                    ]);
                }

                // Cập nhật các thuộc tính của biến thể
                $attributeValuesToAttach = [];
                foreach ($variant['attributes'] as $attribute) {
                    $attributeValuesToAttach[] = $attribute['value']['id_value'];
                }

                // Gắn thuộc tính vào bảng trung gian
                $variantModel->attributeValues()->sync($attributeValuesToAttach);
            }

            // Commit transaction khi thành công
            DB::commit();

            return $product;
        } catch (\Exception $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();
            throw $e;
        }
    }
}
