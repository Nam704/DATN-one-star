<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function list()
    {

        $banners = Banner::all();
        return view('admin.banner.list')->with([
            'banners' => $banners
        ]);
    }

    public function create()
    {
        return view('admin.banner.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|min:5|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'title.min' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'image.required' => 'Vui lòng chọn ảnh.',
            'image.image' => 'File phải là ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif,webp,svg.',
            'image.max' => 'Kích thước ảnh tối đa là 2MB.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải có định dạng hợp lệ.',

            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải có định dạng hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $profileImagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if ($file->isValid()) {
                $profileImagePath = $file->store('banner', 'public');
            } else {
                return back()->withErrors(['image' => 'Ảnh không hợp lệ hoặc bị lỗi khi tải lên.'])->withInput();
            }
        }

        // Create banner
        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $profileImagePath,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('admin.banner.list')->with('success', 'Thêm banner thành công!');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'title' => 'nullable|string|min:5|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'title.min' => 'Tiêu đề phải có ít nhất 5 ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'image.image' => 'File phải là ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif,webp,svg.',
            'image.max' => 'Kích thước ảnh tối đa là 2MB.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'start_date.date' => 'Ngày bắt đầu phải có định dạng hợp lệ.',

            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'end_date.date' => 'Ngày kết thúc phải có định dạng hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $banner = Banner::findOrFail($id);

        // Nếu có ảnh mới, thì xóa ảnh cũ và lưu ảnh mới
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            if ($file->isValid()) {
                // Xóa ảnh cũ nếu tồn tại
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                // Lưu ảnh mới
                $profileImagePath = $file->store('banner', 'public');
                $banner->image = $profileImagePath;
            } else {
                return back()->withErrors(['image' => 'Ảnh không hợp lệ hoặc bị lỗi khi tải lên.'])->withInput();
            }
        }
        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image' => $banner->image, // Nếu có ảnh mới, dòng này đã được cập nhật ở trên
        ]);

        return redirect()->route('admin.banner.list')->with('success', 'Sửa banner thành công!');
    }
    public function detail($id)
    {
        $banner = Banner::findOrFail($id);

        return view('admin.banner.detail', compact('banner'));
    }

    public function delete($id)
    {
        $banner = Banner::findOrFail($id);

        // Xóa ảnh cũ nếu có
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        // Xóa bản ghi trong database
        $banner->delete();

        return redirect()->route('admin.banner.list')->with('success', 'Xóa banner thành công!');
    }
}
