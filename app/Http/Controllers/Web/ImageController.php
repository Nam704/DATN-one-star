<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product_variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::with('productVariant')->get();
        return view('admin.images.index', compact('images'));
    }

    public function create()
    {
        $productVariants = Product_variant::all();
        return view('admin.images.create', compact('productVariants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|image|mimes:jpeg,png,jpg,gif',
            'id_product_variant' => 'required|exists:product_variants,id',
            'status' => 'required|in:active,inactive',
        ]);

        $imagePath = $request->file('url')->store('images', 'public');

        Image::create([
            'url' => $imagePath,
            'id_product_variant' => $request->id_product_variant,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.images.index')->with('success', 'Hình ảnh đã được thêm');
    }


    public function edit($id)
    {
        $image = Image::findOrFail($id);
        $productVariants = Product_variant::all();
        return view('admin.images.edit', compact('image', 'productVariants'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'url' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'id_product_variant' => 'required|exists:product_variants,id',
            'status' => 'required|in:active,inactive',
        ]);

        $image = Image::findOrFail($id);

        if ($request->hasFile('url')) {
            if (Storage::exists('public/' . $image->url)) {
                Storage::delete('public/' . $image->url);
            }
            $imagePath = $request->file('url')->store('images', 'public');
            $image->url = $imagePath;
        }

        $image->id_product_variant = $request->id_product_variant;
        $image->status = $request->status;
        $image->save();

        return redirect()->route('admin.images.index')->with('success', 'Hình ảnh đã được cập nhật');
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        // Xóa ảnh trong thư mục
        if (Storage::exists('public/' . $image->url)) {
            Storage::delete('public/' . $image->url);
        }

        $image->delete();

        return redirect()->route('admin.images.index')->with('success', 'Hình ảnh đã được xóa');
    }

    public function show($id)
{
    $image = Image::with('productVariant')->findOrFail($id);
    $productVariants = Product_variant::all();
    return view('admin.images.show', compact('image', 'productVariants'));
}
}
