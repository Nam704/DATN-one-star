<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function getProvinces()
    {
        try {
            $provinces = Province::orderBy('name')->get();
            return response()->json($provinces);
        } catch (\Exception $e) {
            Log::error('Error fetching provinces: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load provinces'], 500);
        }
    }

    public function getDistricts($provinceId)
    {
        try {
            $districts = District::where('province_id', $provinceId)->orderBy('name')->get();
            return response()->json($districts);
        } catch (\Exception $e) {
            Log::error('Error fetching districts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load districts'], 500);
        }
    }

    public function getWards($districtId)
    {
        try {
            $wards = Ward::where('district_id', $districtId)->orderBy('name')->get();
            return response()->json($wards);
        } catch (\Exception $e) {
            Log::error('Error fetching wards: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load wards'], 500);
        }
    }
}
