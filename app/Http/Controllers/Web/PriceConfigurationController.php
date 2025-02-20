<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PriceConfiguration;
use Illuminate\Http\Request;

class PriceConfigurationController extends Controller
{
    /**
     * Lấy cấu hình giá của một biến thể sản phẩm.
     */
    public function getPriceConfiguration($productVariantId)
    {
        $priceConfig = PriceConfiguration::where('product_variant_id', $productVariantId)->first();

        if (!$priceConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Cấu hình giá không tìm thấy cho biến thể sản phẩm này.'
            ]);
        }

        return response()->json([
            'success' => true,
            'price_config' => $priceConfig
        ]);
    }
}
