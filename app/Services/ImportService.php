<?php

namespace App\Services;

use App\Imports\ImportProducts;
use App\Models\Import;
use App\Models\Import_detail;
use App\Models\Product_variant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    protected $productAuditService;
    protected $productService;
    protected $importProducts;
    protected $user;
    public function __construct(
        ProductAuditService $productAuditService,
        ProductService $productService,
        ImportProducts $importProducts

    ) {
        $this->productAuditService = $productAuditService;
        $this->productService = $productService;
        $this->importProducts = $importProducts;

        // Constructor logic
    }
    public function importByExcel($file)
    {
        $data = Excel::toArray([], $file);
        // $importProducts = new ImportProducts($this->productAuditService);
        $importData =   $this->importProducts->process($data[0], $data[1]);
        return $importData;
    }
    public function createImport($data)
    {
        $this->user = Auth::user();

        DB::beginTransaction();
        try {
            // Tạo phiếu nhập
            $import = Import::create([
                'id_supplier' => $data['supplier'],
                'name' => $data['name'],
                'import_date' => $data['import_date'],
                'total_amount' => $data['total_amount'],
                'note' => $data['note']
            ]);

            // Thêm chi tiết nhập hàng
            foreach ($data['variant-product'] as $variant) {
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);
                $dataAudit = [
                    'id_user' => $this->user->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'action_type' => 'import',
                    'quantity' => $variant['quantity'],
                    'reason' => ""
                ];
                $this->productAuditService->createAudit($dataAudit);
                $this->productService->updateStock($variant['product_variant_id'], $variant['quantity'], true);
            }

            DB::commit();
            return $import;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateImport($id, $data)
    {
        $this->user = Auth::user();

        DB::beginTransaction();
        try {
            $import = Import::findOrFail($id);
            $oldDetails = Import_detail::where('id_import', $id)->get();
            // Cập nhật phiếu nhập
            $import->update([
                'id_supplier' => $data['supplier'],
                'name' => $data['name'],
                'import_date' => $data['import_date'],
                'total_amount' => $data['total_amount'],
                'note' => $data['note']
            ]);
            Import_detail::where('id_import', $id)->delete();
            // Thêm chi tiết mới
            foreach ($data['variant-product'] as $variant) {
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);
                $dataAudit = [
                    'id_user' => $this->user->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'action_type' => 'import',
                    'quantity' => $variant['quantity'],
                    'reason' => ""
                ];
                $this->productAuditService->createProductAudit($dataAudit);
                $this->productService->updateStock($variant['product_variant_id'], $variant['quantity'], true);
            }
            foreach ($oldDetails as $oldDetail) {
                $this->productService->updateStock($oldDetail->id_product_variant, $oldDetail->quantity, false);
            }
            DB::commit();
            return $import;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
