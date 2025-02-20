<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ImportDetail;
use Illuminate\Http\Request;

class ImportDetailController extends Controller
{
    /**
     * Lấy thông tin chi tiết nhập hàng của biến thể sản phẩm.
     */
    public function getImportDetail($productVariantId)
    {
        $importDetail = ImportDetail::where('id_product_variant', $productVariantId)->first();

        if (!$importDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy chi tiết nhập hàng cho biến thể sản phẩm này.'
            ]);
        }

        return response()->json([
            'success' => true,
            'import_detail' => $importDetail
        ]);
    }
}
