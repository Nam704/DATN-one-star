<?php

namespace App\Imports;

use App\Models\Attribute;
use App\Models\Attribute_value;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product_albums;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Services\NotificationService;
use App\Services\ProductAuditService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CreateProductImport

{
    protected $user;
    protected $uploadedImages;
    protected $productAuditService;
    protected $notificationService;
    public function __construct(
        ProductAuditService $productAuditService,
        NotificationService $notificationService
    ) {
        $this->productAuditService = $productAuditService;
        $this->notificationService = $notificationService;
    }

    public function process($sheet1, $sheet2, $images)
    {
        $this->user = auth()->user();
        DB::beginTransaction();
        $products = [];
        $variants = [];

        // Xử lý sheet Products

        foreach ($sheet1 as $index => $row) {
            if ($index == 0) continue; // Bỏ qua dòng tiêu đề

            // Tìm hoặc tạo Category
            $category = Category::firstOrCreate(['name' => trim($row[2])]);
            $categoryId = $category->id;

            // Tìm hoặc tạo Brand
            $brand = Brand::firstOrCreate(['name' => trim($row[3])]);
            $brandId = $brand->id;

            // Xử lý ảnh chính
            $imageName = $row[4]; // Cột Main Image
            $imagePath = $this->getImagePath($images, $imageName);

            // Tạo sản phẩm mới
            $product = Product::updateOrCreate(
                ['name' => $row[0]],
                [
                    'description' => $row[1],
                    'id_category' => $categoryId,
                    'id_brand' => $brandId,
                    'image_primary' => $imagePath,
                ]
            );

            // Lưu album ảnh sản phẩm
            for ($i = 5; $i <= 8; $i++) {
                if (!empty($row[$i])) {
                    $albumImagePath = $this->getImagePath($images, $row[$i]);
                    Product_albums::create([
                        'id_product' => $product->id,
                        'image_path' => $albumImagePath,
                    ]);
                }
            }

            $products[$product->name] = $product;
        }

        // Xử lý sheet Variants
        foreach ($sheet2 as $index => $row) {

            if ($index == 0) continue; // Bỏ qua dòng tiêu đề

            $productName = $row[0];

            if (!isset($products[$productName])) {
                continue; // Bỏ qua nếu không tìm thấy sản phẩm
            }

            $variant = Product_variant::updateOrCreate(
                ['sku' => $row[1]], // SKU là unique
                [
                    'id_product' => $products[$productName]->id,
                ]
            );
            if (!empty($row[2])) {
                // dd($this->getImagePath($images, $row[2]));
                $variant->images()->create([
                    'url' => url('storage/' . $this->getImagePath($images, $row[2])),
                ]);
            }
            // $temp = [];
            // Lưu các thuộc tính động
            for ($i = 3; $i < count($row); $i++) {
                if ($row[$i]) {
                    $attribute = Attribute::findOrCreate($sheet2[0][$i]);
                    $attributeValue = Attribute_value::findOrCreate($attribute->name, $row[$i]);
                    // dd($attributeValue->id);
                    $variantAttributeValue = Product_variant_attribute::create(
                        [
                            'id_product_variant' => $variant->id,
                            'id_attribute_value' => $attributeValue->id
                        ]
                    );
                    // array_push($temp, $variantAttributeValue);
                }
            }
            // dd($temp);
            $this->productAuditService->createAudit([
                'id_user' => $this->user->id,
                'id_product_variant' => $variant->id,
                'action_type' => 'create',
                'status' => 'pending',
                'reason' => "" //$this->user->name . " import" . " at: " . $today,
            ]);
            $variants[$variant->sku] = $variant;
        }
        DB::commit();
        $dataNotification = [
            'title' => 'New Product',
            'message' => $this->user->name . ' đã tạo sản phẩm mới!',
            'from_user_id' => $this->user->id,
            'to_user_id' => null,
            'type' => 'products',
            'status' => 'unread',
            'goto_id' => null,

        ];
        // dd($dataNotification);
        $this->notificationService->sendAdmin($dataNotification);
        return true;
    }
    function getImagePath($images, $fileName)
    {
        return $images[$fileName] ?? null;
    }
}
