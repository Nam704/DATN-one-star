<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Http\Requests\API\StoreAttributeRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    public function index()
    {
        try {
            $data = Attribute::all();
            return response()->json([
                'message' => 'Danh sách thuộc tính',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreAttributeRequest $request)
    {
        try {
            $data = Attribute::create($request->validated());
            return response()->json([
                'message' => 'Tạo thuộc tính thành công',
                'data' => $data
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi tạo thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        try {
            $data = Attribute::findOrFail($id);
            return response()->json([
                'message' => 'Chi tiết thuộc tính id = ' . $id,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi tìm thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->update($request->validated());
            
            return response()->json([
                'message' => 'Cập nhật thuộc tính thành công',
                'data' => $attribute
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi cập nhật thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->delete();

            return response()->json([
                'message' => 'Xóa thuộc tính thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi xóa thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(string $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->status = $attribute->status === 'active' ? 'inactive' : 'active';
            $attribute->save();

            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $attribute
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi cập nhật trạng thái',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trash()
    {
        try {
            $data = Attribute::onlyTrashed()->get();
            return response()->json([
                'message' => 'Danh sách thuộc tính đã xóa',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách thuộc tính đã xóa',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore(string $id)
    {
        try {
            $attribute = Attribute::withTrashed()->findOrFail($id);
            $attribute->restore();

            return response()->json([
                'message' => 'Khôi phục thuộc tính thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi khôi phục thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            $attribute = Attribute::withTrashed()->findOrFail($id);
            $attribute->forceDelete();

            return response()->json([
                'message' => 'Xóa vĩnh viễn thuộc tính thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thuộc tính có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi xóa vĩnh viễn thuộc tính',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
