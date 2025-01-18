<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AttributeValueController extends Controller
{
    public function index()
    {
        try {
            $data = Attribute_value::all();
            return response()->json([
                'message' => 'List of attribute values',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Error getting attribute values list',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = Attribute_value::create($request->validated());
            return response()->json([
                'message' => 'Attribute value created successfully',
                'data' => $data
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            return response()->json([
                'message' => 'Error creating attribute value',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = Attribute_value::findOrFail($id);
            return response()->json([
                'message' => 'Attribute value details id = ' . $id,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Attribute value not found with id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error finding attribute value',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $attributeValue = Attribute_value::findOrFail($id);
            $attributeValue->update($request->validated());
            return response()->json([
                'message' => 'Attribute value updated successfully',
                'data' => $attributeValue
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Attribute value not found with id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error updating attribute value',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $attributeValue = Attribute_value::findOrFail($id);
            $attributeValue->delete();
            return response()->json([
                'message' => 'Attribute value deleted successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Attribute value not found with id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error deleting attribute value',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $attributeValue = Attribute_value::findOrFail($id);
            $attributeValue->status = $attributeValue->status === 'active' ? 'inactive' : 'active';
            $attributeValue->save();
            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $attributeValue
            ]);
        } catch (\Throwable $th) {
            Log::error(__CLASS__ . '@' . __FUNCTION__, [$th]);
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Attribute value not found with id = ' . $id,
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'message' => 'Error updating status',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
