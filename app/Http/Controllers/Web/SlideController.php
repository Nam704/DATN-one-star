<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Image;
use App\Services\SlideService;

class SlideController extends Controller
{

    protected $SlideService;

    public function __construct(SlideService $SlideService)
    {
        $this->SlideService = $SlideService;
    }
    public function index()
    {
        $slides = Slide::with('category')->get();
        return view('admin.slides.index', compact('slides'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.slides.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $blog_data = $this->SlideService->createSlide($request);
        // Lấy danh sách blog mới nhất
        $slides = Slide::latest()->get();
        return view('admin.slides.index', compact('slides'));
    }

    public function show(string $id)
    {
        $slide_img = Slide::with(['primaryImage', 'secondaryImages'])->find($id);
        $slide = Slide::with('category')->find($id);
        return view('admin.slides.detail', compact('slide'));
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $slide = Slide::findOrFail($id);
            $slide->delete(); // hoặc dùng soft delete

            return response()->json([
                'success' => true,
                'message' => 'Slide đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa thất bại. Có lỗi xảy ra!'
            ], 500);
        }
    }
}
