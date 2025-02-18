<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attribute_value;

class AttributeValueController extends Controller
{
    //
    public function getAllByAttributeId($id)
    {
        try {
           
            $data = Attribute_value::where('id_attribute', $id)->get();

            // Nếu không có dữ liệu
            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Không tìm thấy giá trị thuộc tính cho id_attribute = ' . $id,
                ], 404);
            }

            // Trả về dữ liệu
            return response()->json([
                'message' => 'Danh sách giá trị thuộc tính cho id_attribute = ' . $id,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            // Ghi log lỗi
            \Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            // Trả về lỗi
            return response()->json([
                'message' => 'Lỗi khi lấy giá trị thuộc tính',
            ], 500);
        }
    }

}
