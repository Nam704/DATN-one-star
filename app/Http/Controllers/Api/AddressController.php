<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    function detail(Request $request)
    {
        $id = $request->input('id');
        // Log::info($idWard);
        $address = Address::find($id);
        $obj = $address->addressable;
        $details = $address->getAddress($obj, $obj->id, $id);
        // Log::info($obj);
        return response()->json($details);
    }
    function detailDefault(Request $request)
    {
        $id = $request->input('id');
        // Log::info($idWard);
        $address = Address::find($id);
        $obj = $address->addressable;
        $details = $address->getAddressDefault($obj, $obj->id, $id);
        // Log::info($obj);
        return response()->json($details);
    }
}
