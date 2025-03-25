<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TagService;
use App\Models\Tag;
class TagController extends Controller
{
    protected $TagService;

    public function __construct(TagService $TagService){
        $this->TagService = $TagService;
    }

    public function store(Request $request){
        $data = $this->TagService->createTag($request->all());
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function list(Request $request)
    {
        try {
            $tags = Tag::all(); 
            return response()->json([
                'status' => 'success',
                'data' => $tags
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách thẻ tag!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
