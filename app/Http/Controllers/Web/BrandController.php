<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Models\Brand;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index()


    {
        $brands = Brand::withoutTrashed()->latest()->get();
        return view('admin.brands.index', compact('brands'));
    }


    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                'unique:brands,name',
                'regex:/^[\p{L}\s]+$/u',
                'string'
            ],
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'The brand name must not be empty',
            'name.max' => 'The brand name must not exceed 50 characters',
            'name.unique' => 'This brand name already exists',
            'name.regex' => 'Only letters are allowed in the name',
            'status.required' => 'Please select a status'
        ]);

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully');
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        $brand->delete();

        return response()->json([
            'success' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }

    public function trash()
    {
        $trashedBrands = Brand::onlyTrashed()->get();
        return view('admin.brands.trash', compact('trashedBrands'));
    }

    public function restore($id)
    {
        $brand = Brand::withTrashed()->find($id);
        $brand->restore();

        return response()->json([
            'success' => true,
            'message' => 'Brand restored successfully'
        ]);
    }

    public function forceDelete($id)
    {
        $brand = Brand::withTrashed()->find($id);
        $brand->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Brand permanently deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = $brand->status === 'active' ? 'inactive' : 'active';
        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'newStatus' => $brand->status,
            'id' => $brand->id
        ]);
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                'regex:/^[\p{L}\s]+$/u',
                'string',
                Rule::unique('brands')->ignore($id)
            ],
            'status' => 'required|in:active,inactive'
        ], [
            'name.required' => 'The brand name must not be empty',
            'name.max' => 'The brand name must not exceed 100 characters',
            'name.regex' => 'Only letters are allowed in the name',
            'name.unique' => 'This brand name already exists',
            'status.required' => 'Please select a status'
        ]);

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully');
    }
}
