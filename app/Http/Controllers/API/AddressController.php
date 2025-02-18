<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Controller
    public function getProvinces()
    {
        $provinces = Province::all(); // Lấy tất cả các provinces
        return response()->json($provinces);
    }

    public function getDistrictsByProvince($provinceId)
    {
        $province = Province::findOrFail($provinceId);
        $districts = $province->districts;
        return response()->json($districts);
    }

    public function getWardsByDistrict($districtId)
    {
        $district = District::findOrFail($districtId);
        $wards = $district->wards;
        return response()->json($wards);
    }
}
