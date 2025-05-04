<?php

namespace App\Services;

use App\Exports\ProductExport;
use App\Imports\CreateProductByExcel;
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
    protected $CreateProductByExcel;
    protected $user;

    public function __construct(
        AttributeService $AttributeService,
        BrandService $BrandService,
        CategoryService $CategoryService,
        CreateProductImport $CreateProductImport,
        ProductAuditService $ProductAudit,
        NotificationService $NotificationService,
        CreateProductByExcel $CreateProductByExcel
    ) {
        $this->CreateProductByExcel = $CreateProductByExcel;
        $this->AttributeService = $AttributeService;
        $this->BrandService = $BrandService;
        $this->CategoryService = $CategoryService;
        $this->CreateProductImport = $CreateProductImport;
        $this->ProductAuditService = $ProductAudit;
        $this->NotificationService = $NotificationService;
        $this->product = new Product();
    }

    public function createByExcel($file)
    {
        $this->CreateProductByExcel->index($file);
        return true;
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
        $importData = $this->CreateProductImport->process($data[0], $data[1], $images);
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
     * Tạo sản phẩm và lưu dữ liệu vào database
     */
    public function createProduct(array $data)
    {
        try {
            $this->user = auth()->user();

            return DB::transaction(function () use ($data) {
                // Tạo sản phẩm
                $product = Product::create([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'id_category' => $data['id_category'],
                    'id_brand' => $data['id_brand'],
                    'image_primary' => $this->uploadImage($data['image_primary'], 'products'),
                ]);

                // Lưu ảnh album (nếu có)
                if (!empty($data['images']) && is_array($data['images'])) {
                    foreach ($data['images'] as $image) {
                        $imagePath = $this->uploadImage($image, 'products/gallery');
                        $product->product_albums()->create(['image_path' => $imagePath]);
                    }
                }

                // Xử lý các biến thể
                $variants = $data['variants'] ?? [];
                $variantSkus = []; // Kiểm tra SKU trùng lặp trong cùng giao dịch

                foreach ($variants as $index => $variant) {
                    // Kiểm tra SKU trùng lặp trong dữ liệu gửi lên
                    if (in_array($variant['code'], $variantSkus)) {
                        throw new \Exception("Mã SKU '{$variant['code']}' của biến thể bị trùng lặp trong dữ liệu gửi lên.");
                    }
                    $variantSkus[] = $variant['code'];

                    $variantModel = Product_variant::create([
                        'id_product' => $product->id,
                        'sku' => $variant['code'],
                        'price' => $variant['price'],
                        'quantity' => $variant['quantity'] ?? 0,
                    ]);

                    // Lưu ảnh biến thể
                    if (!empty($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $variantModel->images()->create([
                            'url' => $this->uploadImage($variant['image'], 'products/variants'),
                        ]);
                    }

                    // Lưu thuộc tính của biến thể
                    $attributeValuesToAttach = [];
                    foreach ($variant['attributes'] as $attribute) {
                        $attributeValuesToAttach[] = $attribute['value']['id_value'];
                    }
                    $variantModel->attributeValues()->attach($attributeValuesToAttach);

                    // Ghi log audit
                    $this->ProductAuditService->createAudit([
                        'id_user' => $this->user->id,
                        'id_product_variant' => $variantModel->id,
                        'action_type' => 'create',
                        'status' => 'pending',
                        'reason' => "",
                    ]);
                }

                // Gửi thông báo
                $dataNotification = [
                    'title' => 'New Product',
                    'message' => $this->user->name . ' đã tạo sản phẩm mới!',
                    'from_user_id' => $this->user->id,
                    'to_user_id' => null,
                    'type' => 'products',
                    'status' => 'unread',
                    'goto_id' => $product->id,
                ];
                $this->NotificationService->sendAdmin($dataNotification);

                return $product;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                // Xử lý lỗi trùng tên sản phẩm
                if (strpos($e->getMessage(), 'products_name_unique') !== false) {
                    throw new \Exception('Tên sản phẩm đã tồn tại, vui lòng chọn tên khác.');
                }
                // Xử lý lỗi trùng SKU
                elseif (strpos($e->getMessage(), 'product_variants_sku_unique') !== false) {
                    preg_match("/Duplicate entry '(.+?)' for key/", $e->getMessage(), $matches);
                    $duplicateSku = $matches[1] ?? 'không xác định';
                    throw new \Exception("Mã SKU '$duplicateSku' đã tồn tại trong hệ thống, vui lòng sử dụng mã khác.");
                }
            }
            // Các lỗi cơ sở dữ liệu khác
            throw new \Exception('Lỗi cơ sở dữ liệu không xác định: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Ném lại các lỗi khác để ProductController xử lý
            throw $e;
        }
    }

    /**
     * Xử lý và định dạng dữ liệu sản phẩm từ request
     */
    public function processProductData(array $data)
    {
        $formattedData = [
            '_token' => $data['_token'] ?? null,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'id_category' => $data['id_category'] ?? null,
            'id_brand' => $data['id_brand'] ?? null,
            'image_primary' => $data['image_primary'] ?? null,
            'images' => $data['images'] ?? [],
        ];

        // Xử lý biến thể sản phẩm
        $variants = $data['variants'] ?? [];

        $formattedData['variants'] = array_map(function ($variant, $index) use ($data) {
            // Giải mã chuỗi JSON trong attributes nếu cần
            $attributes = is_string($variant['attributes']) ? json_decode($variant['attributes'], true) : $variant['attributes'];

            return [
                'id' => $variant['id'] ?? null,
                'code' => $variant['code'],
                'price' => $variant['price'] ?? 0,
                'quantity' => $variant['quantity'] ?? 0,
                'attributes' => array_map(function ($attribute) {
                    return [
                        'attribute_id' => $attribute['attribute_id'],
                        'attribute_name' => $attribute['attribute_name'],
                        'value' => $attribute['value'],
                    ];
                }, $attributes),
                'image' => $data["image_variant_{$index}"] ?? null, // Lấy file từ image_variant_${index}
            ];
        }, $variants, array_keys($variants));

        return $formattedData;
    }

    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct(array $data, $id)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            $formattedData = $this->processProductData($data);

            if (!empty($formattedData['image_primary']) && $formattedData['image_primary'] instanceof \Illuminate\Http\UploadedFile) {
                if ($product->image_primary) {
                    Storage::delete($product->image_primary);
                }
                $product->image_primary = $this->uploadImage($formattedData['image_primary'], 'products');
            }

            $product->update([
                'name' => $formattedData['name'],
                'description' => $formattedData['description'],
                'id_category' => $formattedData['id_category'],
                'id_brand' => $formattedData['id_brand'],
                'image_primary' => $product->image_primary,
            ]);

            if (!empty($formattedData['images']) && is_array($formattedData['images'])) {
                foreach ($product->product_albums as $album) {
                    Storage::delete($album->image_path);
                    $album->delete();
                }
                foreach ($formattedData['images'] as $image) {
                    $imagePath = $this->uploadImage($image, 'products/gallery');
                    $product->product_albums()->create(['image_path' => $imagePath]);
                }
            }

            foreach ($formattedData['variants'] as $variant) {
                if (!empty($variant['id'])) {
                    $variantModel = Product_variant::findOrFail($variant['id']);
                    $variantModel->update([
                        'sku' => $variant['code'],
                        'price' => $variant['price'],
                        'quantity' => $variant['quantity'] ?? 0,
                    ]);
                } else {
                    $variantModel = Product_variant::create([
                        'id_product' => $product->id,
                        'sku' => $variant['code'],
                        'price' => $variant['price'],
                        'quantity' => $variant['quantity'] ?? 0,
                    ]);
                }

                if (!empty($variant['image']) && $variant['image'] instanceof \Illuminate\Http\UploadedFile) {
                    if ($variantModel->images()->exists()) {
                        Storage::delete(str_replace(url('/storage'), '', $variantModel->images()->first()->url));
                        $variantModel->images()->delete();
                    }
                    $variantModel->images()->create([
                        'url' => $this->uploadImage($variant['image'], 'products/variants'),
                    ]);
                }

                $attributeValuesToAttach = [];
                foreach ($variant['attributes'] as $attribute) {
                    $attributeValuesToAttach[] = $attribute['value']['id_value'];
                }
                $variantModel->attributeValues()->sync($attributeValuesToAttach);
            }

            DB::commit();
            return $product;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'products_name_unique') !== false) {
                throw new \Exception('Tên sản phẩm đã tồn tại, vui lòng chọn tên khác.');
            }
            throw new \Exception('Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function uploadImage($image, $folder = 'products')
    {
        if (!$image) {
            return null;
        }

        $filename = time() . '_' . md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $image->storeAs($folder, $filename, 'public');
        return '/storage/' . $folder . '/' . $filename;
    }

    public function prepareData()
    {
        return [
            'attributes' => $this->AttributeService->getAttributes(),
            'brands' => $this->BrandService->getBrands(),
            'categories' => $this->CategoryService->getCategories(),
        ];
    }

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

    public function productDetail($id)
    {
        $product = Product::findOrFail($id);
        $product->getProductWithDetails();

        $prices = $product->getPriceRange();
        $product->min_price = $prices->min_price;
        $product->max_price = $prices->max_price;
        $product->quantity = $product->quantity();
        return $product;
    }
}
