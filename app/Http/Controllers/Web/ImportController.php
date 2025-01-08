<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Models\Import;
use App\Models\Import_detail;
use App\Models\Product;
use App\Models\Product_variant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    protected $import;
    function __construct(Import $import)
    {
        $this->import = $import;
    }
    function detail($id)
    {
        $import = Import::find($id);
        $import_details = Import_detail::where('id_import', $id)->get();
        return view('admin.import.detail', compact('import', 'import_details'));
    }
    function getFormEdit($id)
    {
        $import = Import::with(['supplier', 'import_details.product_variant'])->findOrFail($id);
        $suppliers = Supplier::list()->get();
        $products = Product::list()->get();

        return view('admin.import.edit', compact('import', 'suppliers', 'products'));
    }

    function edit(ImportRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $import = Import::findOrFail($id);

            // Lưu lại các chi tiết cũ để xử lý số lượng kho
            $oldDetails = Import_detail::where('id_import', $id)->get();

            // Cập nhật thông tin chính của import
            $import->update([
                'id_supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note
            ]);

            // Cập nhật lại số lượng kho dựa trên dữ liệu cũ
            foreach ($oldDetails as $oldDetail) {
                $productVariant = Product_variant::find($oldDetail->id_product_variant);
                if ($productVariant) {
                    $productVariant->quantity -= $oldDetail->quantity;
                    $productVariant->save();
                }
            }

            // Xóa chi tiết cũ sau khi xử lý số lượng kho
            Import_detail::where('id_import', $id)->delete();

            // Tạo mới các chi tiết nhập hàng
            foreach ($request->input('variant-product') as $variant) {
                // Kiểm tra tính hợp lệ của từng variant
                $productVariant = Product_variant::find($variant['product_variant_id']);
                if (!$productVariant) {
                    throw new \Exception("Product variant không tồn tại: " . $variant['product_variant_id']);
                }

                // Tạo chi tiết nhập hàng mới
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);

                // Cập nhật lại số lượng kho
                $productVariant->quantity += $variant['quantity'];
                $productVariant->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Import updated successfully',
                'redirect' => route('admin.imports.list')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    function getFormAdd()
    {
        $suppliers = Supplier::list()->get();
        $products = Product::list()->get();
        return view('admin.import.add', compact('suppliers', 'products'));
    }
    function list()
    {
        $imports = Import::list();
        return view('admin.import.list', compact('imports'));
    }
    function add(ImportRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            // Create import invoice
            $import = Import::create([
                'id_supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note
            ]);

            // Create import details
            foreach ($request->input('variant-product') as $variant) {
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);
                // Update product variant quantity
                $productVariant = Product_variant::find($variant['product_variant_id']);
                $productVariant->quantity += $variant['quantity'];
                $productVariant->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Import created successfully',
                'redirect' => route('admin.imports.list')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Failed to create import');
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
