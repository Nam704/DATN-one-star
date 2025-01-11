<?php

namespace App\Imports;

use App\Models\Import;
use App\Models\Import_detail;
use App\Models\ImportDetail;
use App\Models\Product_variant;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Services\ProductAuditService;
use Illuminate\Support\Facades\Auth;

class ImportProducts
{
    protected $productAuditService;
    protected $user;

    public function __construct(ProductAuditService $productAuditService)
    {
        $this->user = Auth::user();
        if (!$this->user) {
            throw new \Exception("Không xác định được người dùng đang thực hiện import.");
        }

        $this->productAuditService = $productAuditService;
    }

    public function process(array $importData, array $detailsData)
    {
        $today = date('Y-m-d H:i:s');

        foreach ($importData as $index => $row) {
            if ($index === 0) continue; // Bỏ qua tiêu đề
            $validator = Validator::make([
                'supplier_name' => $row[0],
                'note' => $row[1],
            ], [
                'supplier_name' => 'required|string|max:100',
                'note' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new \Exception("Dòng " . ($index + 1) . " có lỗi: " . implode(", ", $validator->errors()->all()));
            }
            // Chuyển đổi ngày tháng từ Sheet1
            $importDate = $today;

            $supplier = Supplier::where('name', $row[0])->first();
            if (!$supplier) {
                throw new \Exception("Dòng " . ($index + 1) . " có lỗi: " . "Không tìm thấy nhà cung cấp");
            }
            DB::beginTransaction();


            // Tạo bản ghi nhập hàng từ Sheet1
            $import = Import::create([
                'id_supplier' => $supplier->id,
                'name' => $supplier->name . "_" . date('Y-m-d H:i:s'),
                'import_date' => $importDate,
                'total_amount' => 0,
                'note' => $row[1] ?? null,
            ]);
            // dd($detailsData);
            $totalAmount = 0;
            foreach ($detailsData as $index => $detail) {
                if ($index === 0) continue;
                $productVariant = Product_variant::where('sku', $detail[0])->first();
                // dd($detail);
                if (!$productVariant) {
                    throw new \Exception("Product Variant SKU: {$detail[0]} không tồn tại");
                }
                $totalPrice = $detail[1] * $detail[2];
                $totalAmount += $totalPrice;
                $quantity = $detail[1];
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $productVariant->id,
                    'quantity' => $detail[1],
                    'price_per_unit' => $detail[2],
                    'expected_price' => $detail[3],
                    'total_price' => $totalPrice,
                ]);
                $this->productAuditService->createAudit([
                    'id_user' => $this->user->id,
                    'id_product_variant' => $productVariant->id,
                    'action_type' => 'import',
                    'quantity' => $quantity,
                    'reason' => "" //$this->user->name . " import" . " at: " . $today,
                ]);
                // $productVariant->quantity += $detail[1];
                // $productVariant->save();
            }
            $import->update(['total_amount' => $totalAmount]);

            DB::commit();
        }
        return $import;
    }
}