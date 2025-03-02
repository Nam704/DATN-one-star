<?php

namespace App\Services;

use App\Models\Brand;

use Illuminate\Support\Facades\Validator;

class BrandService
{
    public function __construct()
    {
        // Constructor logic
    }
    public function getBrands()
    {
        return Brand::all();
    }
    public function getBrandById($id)
    {
        return Brand::find($id);
    }
    public function createBrand($data)
    {
        try {
            $validatedData = Validator::make($data, [
                'name' => 'required|string|max:255|unique:brands,name',
            ])->validated();
            $brand = Brand::create($validatedData);
            return $brand;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function updateBrand($id, $data)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return
                null;
            $brand->update($data);
            return $brand;
        }
    }
    public function deleteBrand($id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            $brand->delete();
            return true;
        }
        return false;
    }
}
