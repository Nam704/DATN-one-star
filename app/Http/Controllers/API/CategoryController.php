<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $data = Category::all();
            return response()->json([
                'message' => 'List of categories',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Error getting categories list',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = Category::create($request->validated());
            return response()->json([
                'message' => 'Category created successfully',
                'data' => $data
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Error creating category',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = Category::findOrFail($id);
            return response()->json([
                'message' => 'Category details',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Category not found',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error finding category',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->update($request->validated());
            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Category not found',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error updating category',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Category not found',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error deleting category',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();
            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Category not found',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error updating status',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
