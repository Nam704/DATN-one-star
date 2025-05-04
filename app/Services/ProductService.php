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
            DB::beginTransaction();
            $formattedData = $this->processProductData($request);

            $product = Product::create([
                'name'        => $formattedData['name'],
                'description' => $formattedData['description'],
                'id_category' => $formattedData['id_category'],
                'id_brand'    => $formattedData['id_brand'],
                'image_primary' => $this->uploadImage($request->file('image_primary'), 'products'),
            ]);

            if (!empty($formattedData['images'])) {
                foreach ($formattedData['images'] as $image) {
                    $imagePath = $this->uploadImage($image, 'products/gallery');
                    $product->product_albums()->create(['image_path' => $imagePath]);
                }
            }

            foreach ($formattedData['variants'] as $variant) {
                $variantModel = Product_variant::create([
                    'id_product' => $product->id,
                    'sku'        => $variant['code'],
                    'price'      => $variant['price'],
                ]);

                // Lưu ảnh biến thể (nếu có), sử dụng đường dẫn tương đối
                if ($variant['image']) {
                    $variantModel->images()->create([
                        'url' => $variant['image'], // Sử dụng trực tiếp đường dẫn từ uploadImage
                    ]);
                }

                $attributeValuesToAttach = [];
                foreach ($variant['attributes'] as $attribute) {
                    $attributeValuesToAttach[] = $attribute['value']['id_value'];
                }
                $variantModel->attributeValues()->attach($attributeValuesToAttach);
                $this->ProductAuditService->createAudit([
                    'id_user' => $this->user->id,
                    'id_product_variant' => $variantModel->id,
                    'action_type' => 'create',
                    'status' => 'pending',
                    'reason' => "",
                ]);
            }

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
            $this->NotificationService->sendAdmin($dataNotification);
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            $formattedData = $this->processProductData($request);

            if ($request->hasFile('image_primary')) {
                if ($product->image_primary) {
                    Storage::delete($product->image_primary);
                }
                $product->image_primary = $this->uploadImage($request->file('image_primary'), 'products');
            }

            $product->update([
                'name'        => $formattedData['name'],
                'description' => $formattedData['description'],
                'id_category' => $formattedData['id_category'],
                'id_brand'    => $formattedData['id_brand'],
                'image_primary' => $product->image_primary,
            ]);

            if (!empty($formattedData['images'])) {
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
                        'sku'   => $variant['code'],
                        'price' => $variant['price'],
                    ]);
                } else {
                    $variantModel = Product_variant::create([
                        'id_product' => $product->id,
                        'sku'        => $variant['code'],
                        'price'      => $variant['price'],
                    ]);
                }

                // Lưu ảnh biến thể (nếu có), sử dụng đường dẫn tương đối
                if (!empty($variant['image'])) {
                    if ($variantModel->images()->exists()) {
                        Storage::delete(str_replace(url('/storage'), '', $variantModel->images()->first()->url));
                        $variantModel->images()->delete();
                    }
                    $variantModel->images()->create([
                        'url' => $variant['image'], // Sử dụng trực tiếp đường dẫn từ uploadImage
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

        // Tạo tên tệp dựa trên thời gian và hash của tên gốc
        $filename = time() . '_' . md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

        // Lưu ảnh vào thư mục được chỉ định với tên tệp đã tạo
        $image->storeAs($folder, $filename, 'public');

        // Trả về đường dẫn hình ảnh
        return '/storage/' . $folder . '/' . $filename;
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
