<?php

namespace App\Jobs;

use App\Models\Attribute;
use App\Models\Attribute_value;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product_albums;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Services\NotificationService;
use App\Services\ProductAuditService;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateProductByExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $excel_file;
    protected $user_id;
    protected $ProductService;
    function __construct(

        $excel_file,
        $user_id

    ) {
        $this->user_id = $user_id;
        $this->excel_file = $excel_file;
    }

    /**
     * Execute the job.
     */
    public function handle(ProductAuditService $ProductAuditService, NotificationService $NotificationService): void
    {
        $user = \App\Models\User::find($this->user_id);
        if (!$user) {
            \Log::error("Không tìm thấy người dùng với ID: {$this->user_id}");
            return;
        }
        $fullPath = storage_path("app/{$this->excel_file}");
        if (!file_exists($fullPath)) {
            \Log::error("File không tồn tại: {$fullPath}");
            return;
        }
        $excel_file = $fullPath;
        $this->createProduct($excel_file, $user, $ProductAuditService, $NotificationService);
    }
    function createProduct($excel_file, $user, $ProductAuditService, $NotificationService)
    {
        try {
            DB::beginTransaction();
            $reader = new Xlsx();
            $spreadsheet = $reader->load($excel_file);
            $messageErrors = [];
            $productsSheet = $spreadsheet->getSheetByName('Products');

            $variantsSheet = $spreadsheet->getSheetByName('Variants');

            // Lấy dòng đầu tiên làm header (tên cột)
            $headerRow = $productsSheet->getRowIterator(1)->current();
            $header = []; // mảng các tiêu đề

            // Chuyển đổi RowCellIterator thành mảng
            foreach ($headerRow->getCellIterator() as $cell) {
                $nameHeader = $cell->getValue();
                $header[] = Str::of($nameHeader)->trim()->replace('  ', ' ')->toString();
            }

            // Lặp qua các dòng sản phẩm và in ra theo header
            foreach ($productsSheet->getRowIterator(2) as $row) {
                $rowData = [];
                $rowIndex = $row->getRowIndex();
                $albums = [];
                $data = [];
                // Lấy giá trị cho mỗi cột theo header
                foreach ($header as $index => $columnName) {
                    $cellValue = $productsSheet->getCellByColumnAndRow($index + 1, $rowIndex)->getValue();
                    $rowData[$columnName] = $cellValue;
                    if ($columnName == 'Name') {
                        $data['name'] = $cellValue;
                    }
                    if (($columnName) == 'Description') {
                        $data['description'] = $cellValue;
                    }
                    if (($columnName) == 'Category') {
                        $category = Category::firstOrCreate(['name' => $cellValue]);
                        $data['id_category'] = $category->id;
                    }
                    if (($columnName) == 'Brand') {
                        $brand = Brand::firstOrCreate(['name' => $cellValue]);
                        $data['id_brand'] = $brand->id;
                    }
                    if (($columnName) == 'Main Image') {
                        $mainImageCoordinate = $this->getImageCoordinates($productsSheet, $rowIndex, 'Main Image');
                        if ($mainImageCoordinate) {
                            $imagePath = $this->saveImage($mainImageCoordinate);
                            $rowData['Main Image'] = $imagePath;
                            $data['image_primary'] = $imagePath;
                        }
                    }
                    if (strpos($columnName, 'Album Image') !== false) {

                        $albumImageCoordinate = $this->getImageCoordinates($productsSheet, $rowIndex, $columnName);
                        if ($albumImageCoordinate) {
                            $imagePath = $this->saveImage($albumImageCoordinate, 'gallery/');
                            $rowData[$columnName] = $imagePath;
                            // echo '<img src="' . $imagePath . '" alt="Album Image" width="100">';
                            $albums[] = $imagePath;
                        }
                    }
                    // echo $columnName . ' - ' . $rowData[$columnName] . '<br>';
                }
                // dd($rowData);
                $product = Product::create($data);
                // echo $product->id . '<br>';
                // Lưu hình ảnh vào thư mục

                if (!empty($albums)) {
                    foreach ($albums as $album) {

                        Product_albums::create([
                            'id_product' => $product->id,
                            'image_path' => $album
                        ]);
                    }
                }
            }
            $this->createvariant($variantsSheet, $messageErrors, $user, $ProductAuditService);
            DB::commit();
            $dataNotification = [
                'title' => 'New Product',
                'message' => $user->name . ' đã tạo sản phẩm mới!',
                'from_user_id' => $user->id,
                'to_user_id' => null,
                'type' => 'products',
                'status' => 'unread',
                'goto_id' => null,

            ];
            // dd($dataNotification);
            $NotificationService->sendAdmin($dataNotification);
        } catch (\Throwable $th) {
            DB::rollBack();
            // throw $th;
            Log::error('Error: ' . $th->getMessage());
        }
    }
    function createvariant($variantsSheet, $messageErrors, $user, $ProductAuditService)
    {
        try {
            $headerRow = $variantsSheet->getRowIterator(1)->current();
            $header = []; // mảng các tiêu đề

            // Chuyển đổi RowCellIterator thành mảng
            foreach ($headerRow->getCellIterator() as $cell) {
                $nameHeader = $cell->getValue();
                $header[] = Str::of($nameHeader)->trim()->replace('  ', ' ')->toString();
            }
            foreach ($variantsSheet->getRowIterator(2) as $row) {
                $rowIndex = $row->getRowIndex();
                $data = [];
                $varianValues = [];
                // Lấy giá trị cho mỗi cột theo header
                foreach ($header as $index => $columnName) {
                    $cellValue = $variantsSheet->getCellByColumnAndRow($index + 1, $rowIndex)->getValue();
                    if ($columnName == 'Product Name') {
                        $product = Product::where('name', $cellValue)->first();

                        if (!$product) {
                            $messageErrors[] = "Product Name: $cellValue not found";
                            return $messageErrors;
                        } else {
                            $data['id_product'] = $product->id;
                        }
                    }
                    if ($columnName == 'Variant SKU') {
                        $variant = Product_variant::where('sku', $cellValue)->first();
                        if ($variant) {
                            $messageErrors[] = "Variant SKU: $cellValue already exists";
                            return $messageErrors;
                        } else {
                            $data['sku'] = $cellValue;
                        }
                    }
                    if ($columnName == 'Image') {
                        $albumImageCoordinate = $this->getImageCoordinates($variantsSheet, $rowIndex, $columnName);
                        if ($albumImageCoordinate) {
                            $imagePath = $this->saveImage($albumImageCoordinate, 'variants/');
                            $data['image'] = $imagePath;
                        } else {
                            $messageErrors[] = "Image not found for Variant SKU: $cellValue";
                            return $messageErrors;
                        }
                    }

                    if ($columnName != 'Product Name' && $columnName != 'Variant SKU' && $columnName != 'Image') {
                        $i = 0;
                        // Kiểm tra nếu giá trị không rỗng
                        if (!empty($cellValue)) {
                            // Lưu thuộc tính vào cơ sở dữ liệu (Cần có bảng attributes nếu chưa có)
                            $attribute = Attribute::firstOrCreate(['name' => $columnName]);
                            $attributeValue = Attribute_value::firstOrCreate([
                                'id_attribute' => $attribute->id,
                                'value' => Str::of($cellValue)->trim()->replace('  ', ' ')->toString()
                            ]);
                            $varianValues[] = $attributeValue->id;
                        }
                    }
                }
                // dd($varianValues);
                $variant = Product_variant::create([
                    'id_product' => $data['id_product'],
                    'sku' => $data['sku'],

                ]);
                $variant->images()->create([
                    'url' => $data['image']
                ]);
                $ProductAuditService->createAudit([
                    'id_user' => $user->id,
                    'id_product_variant' => $variant->id,
                    'action_type' => 'create',
                    'status' => 'pending',
                    'reason' => ""
                ]);

                // Gắn thuộc tính vào sản phẩm
                foreach ($varianValues as $key => $value) {
                    Product_variant_attribute::create([
                        'id_product_variant' => $variant->id, // Gắn vào variant SKU đã tạo
                        'id_attribute_value' => $value
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    private function getImageCoordinates($sheet, $rowIndex, $columnName)
    {
        $headerRow = $sheet->getRowIterator(1)->current();

        // Chuyển RowCellIterator thành mảng để dễ dàng tìm kiếm
        $columns = [];
        foreach ($headerRow->getCellIterator() as $cell) {
            $columns[] = $cell->getValue();
        }

        $imageColumn = array_search($columnName, $columns);  // Tìm chỉ số cột theo tên

        if ($imageColumn !== false) {
            $drawingCollection = $sheet->getDrawingCollection();
            foreach ($drawingCollection as $drawing) {
                // Kiểm tra xem tọa độ của hình ảnh có trùng với tọa độ của cột này không
                if ($drawing->getCoordinates() == $sheet->getCellByColumnAndRow($imageColumn + 1, $rowIndex)->getCoordinate()) {
                    return $drawing->getPath(); // Trả về đường dẫn của hình ảnh
                }
            }
        }
        return null;
    }

    // Hàm để lưu hình ảnh vào thư mục public
    private function saveImage($imagePath, $folder = null)
    {
        // Lấy thời gian hiện tại và tạo tên tệp duy nhất
        $timestamp = Carbon::now()->timestamp; // Hoặc dùng Carbon::now()->format('Y_m_d_His') để có dạng khác
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION); // Lấy phần mở rộng của tệp (jpg, png, v.v.)

        // Tạo tên ảnh mới với thời gian
        $newFileName = $timestamp . '_' . uniqid() . '.' . $extension;

        // Đặt đường dẫn lưu ảnh
        $img_url = "/storage/products/" . $folder . $newFileName;
        $img_path = public_path($img_url);

        // Đọc nội dung của ảnh và lưu vào thư mục
        $contents = file_get_contents($imagePath);
        file_put_contents($img_path, $contents);

        return $img_url; // Trả về đường dẫn ảnh mới
    }
}
