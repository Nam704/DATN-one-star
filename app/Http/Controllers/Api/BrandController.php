<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    protected $BrandService;
    public function __construct(
        BrandService $BrandService
    ) {
        $this->BrandService = $BrandService;
    }
    public function store(Request $request)
    {

        $data = $request->all();
        $brand =  $this->BrandService->createBrand($data);
        return response()->json([
            'status' => 'success',
            'data' => [
                "id" => $brand->id,
                "name" => $brand->name,
            ]
        ], 201);
    }
}
