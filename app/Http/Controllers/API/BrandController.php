<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\API\StoreBrandRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()
    {
        try {
            $data = Brand::all();
            return response()->json([
                'message' => 'Danh sách thương hiệu',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            $data = Brand::create($request->validated());
            return response()->json([
                'message' => 'Tạo thương hiệu thành công',
                'data' => $data
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi tạo thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(string $id)
    {
        try {
            $data = Brand::findOrFail($id);
            return response()->json([
                'message' => 'Chi tiết thương hiệu id = ' . $id,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi tìm thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update($request->validated());
            
            return response()->json([
                'message' => 'Cập nhật thương hiệu thành công',
                'data' => $brand
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi cập nhật thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();

            return response()->json([
                'message' => 'Xóa thương hiệu thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi xóa thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->status = $brand->status === 'active' ? 'inactive' : 'active';
            $brand->save();

            return response()->json([
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $brand
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
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
            $data = Brand::onlyTrashed()->get();
            return response()->json([
                'message' => 'Danh sách thương hiệu đã xóa',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Lỗi khi lấy danh sách thương hiệu đã xóa',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore(string $id)
    {
        try {
            $brand = Brand::withTrashed()->findOrFail($id);
            $brand->restore();

            return response()->json([
                'message' => 'Khôi phục thương hiệu thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi khôi phục thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            $brand = Brand::withTrashed()->findOrFail($id);
            $brand->forceDelete();

            return response()->json([
                'message' => 'Xóa vĩnh viễn thương hiệu thành công'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);

            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Không tìm thấy thương hiệu có id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lỗi khi xóa vĩnh viễn thương hiệu',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
