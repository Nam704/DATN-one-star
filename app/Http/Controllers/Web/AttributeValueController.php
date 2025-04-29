<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\Attribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
{

    public function index()
    {
        // Lấy danh sách giá trị thuộc tính chưa xóa
        $attributes_value = Attribute_value::with('attribute')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.attribute_value.list', compact('attributes_value'));
    }

    
}
