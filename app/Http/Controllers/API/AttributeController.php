<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Http\Requests\API\StoreAttributeRequest;
use App\Services\AttributeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Request;

class AttributeController extends Controller
{
    protected $AttributeService;
    public function __construct(AttributeService $AttributeService)
    {
        $this->AttributeService = $AttributeService;
    }
    public function getAll()
    {
        $attributes = $this->AttributeService->getAttributes();

        return response()->json([
            "status" => "success",
            "data" => $attributes
        ], 200);
    }
    public function getAttributeById($id)
    {
        $attribute = $this->AttributeService->getAttributeById($id);

        return response()->json([
            "status" => "success",
            "data" => $attribute
        ], 200);
    }
    function createValue($id)
    {
        $value = request()->value;

        $attribute = $this->AttributeService->createValue($id, $value);
        return response()->json([
            "status" => "success",
            "data" => $attribute
        ], 200);
    }
}
