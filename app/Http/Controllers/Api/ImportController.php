<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Import_detail;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    function acceptAll(Request $request)
    {
        $ids = $request->input('ids');

        $imports = Import::whereIn('id', $ids)->get();
        foreach ($imports as $import) {
            $import->status = 'approved';
            $import->save();
        }
        // return redirect()->route('admin.imports.listApproved')->with('success', 'Đã chấp nhận ' . count($imports) . ' import!');
        return response()->json(['status' => 'success', 'message' => 'Đã chấp nhận ' . count($imports) . ' import!']);
    }
    function rejectAll(Request $request)
    {
        $ids = $request->input('ids');
        $imports = Import::whereIn('id', $ids)->get();
        foreach ($imports as $import) {
            $import->status = 'rejected';
            $import->save();
        }
        //
        return response()->json(['status' => 'success', 'message' => 'Đã từ chối ' . count($imports) . ' import!']);
    }
    public function getImportDetails($id)
    {
        $products = Import_detail::with('product_variant.product')
            ->where('id_import', $id)
            ->get()
            ->pluck('product_variant.product')
            ->unique();
        foreach ($products as $product) {
            $variants = $product->variants;
        }



        $importDetails = Import_detail::with(['product_variant'])
            ->where('id_import', $id)
            ->get()
            ->map(function ($detail) {
                return [
                    'product_id' => $detail->product_variant->id_product,
                    'product_variant_id' => $detail->id_product_variant,
                    'product_variant_sku' => $detail->product_variant->sku,
                    'quantity' => $detail->quantity,
                    'price_per_unit' => $detail->price_per_unit,
                    'expected_price' => $detail->expected_price,
                    'total_price' => $detail->total_price
                ];
            });

        return response()->json([
            "importDetails" => $importDetails,
            "products" => $products,
            // "variants" => $variants
        ]);
    }
    public function confirmImport(Request $request)
    {
        $request->validate([
            'import_id' => 'required|exists:imports,id',
        ]);

        DB::beginTransaction();
        try {
            $import = Import::findOrFail($request->import_id);

            // Cập nhật trạng thái xác nhận
            $import->update(['status' => 'approved']);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Import has been confirmed successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}