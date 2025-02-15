<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected $AttributeService;
    protected $BrandService;
    protected $CategoryService;

    public function __construct(
        AttributeService $AttributeService,
        BrandService $BrandService,
        CategoryService $CategoryService
    ) {
        $this->AttributeService = $AttributeService;
        $this->BrandService = $BrandService;
        $this->CategoryService = $CategoryService;
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
                'code'       => $variant['code'],
                'price'      => $variant['price'],
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
}
