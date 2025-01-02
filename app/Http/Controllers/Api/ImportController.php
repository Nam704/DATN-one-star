<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Import_detail;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Http\Request;

class ImportController extends Controller
{
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
}