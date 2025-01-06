<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function listProduct(){
        $products = Product::with(['brand', 'category'])->get();;
        return view('admin.product.index')->with([
            'products' => $products
        ]);
    }

    public function addProduct(){
        $categories= Category::all();
        $brands= Brand::all();
        return view('admin.product.add')->with(
            [
                'categories' => $categories,
                'brands' => $brands
            ]
        );
    }


    public function addPostProduct(ProductRequest $request)
    {
        if ($request->hasFile('image_primary')) {
            $profile = $request->file('image_primary')->store('uploads/products', 'public');
        } else {
            $profile = null;
        }
    
        Product::query()->create([
            'name' => $request->name,
            'id_brand' => $request->id_brand,
            'id_category' => $request->id_category,
            'description' => $request->description,
            'image_primary' => $profile,
            'status' => $request->status,
        ]);
    
        return redirect()->route('admin.products.listProduct')->with('success', 'Thêm sản phẩm thành công');
    }
    

public function editProduct($id){
   $brands= Brand::all();
   $categories= Category::all();
   $products= Product::findOrFail($id);

   return view('admin.product.edit')->with([
     'brands' => $brands,
     'categories' => $categories,
     'product' => $products
   ]);

}

public function editPutProduct(Request $request, $id)
{
 
    $request->validate([
        'name' => 'required|string|max:255',
        'id_brand' => 'required|exists:brands,id',
        'id_category' => 'required|exists:categories,id',
        'description' => 'nullable|string',
        'image_primary' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        'status' => 'required|in:active,inactive',
    ]);

    $product = Product::findOrFail($id);
    $product->fill($request->except('image_primary'));

    $imagePath = null;
    if ($request->hasFile('image_primary')) {
        if ($product->image_primary && Storage::disk('public')->exists($product->image_primary)) {
            Storage::disk('public')->delete($product->image_primary);
        }
        $imagePath= $request->file('image_primary')->store('uploads/products', 'public') ;
    }
    else{
        $imagePath=$product->image_primary; // Giữ lại hình ảnh hiện tại nếu không có hình ảnh mới
    }
    
     $product->update([
        'name'=>$request->name,
        'id_brand'=>$request->id_brand,
        'id_category'=>$request->id_category,
        'description'=>$request->description,
        'image_primary'=>$imagePath,
        'status'=>$request->status
        
     ]);
    
    
    return redirect()->route('admin.products.listProduct')->with('success', 'Sản phẩm đã được cập nhật.');

}

public function deleteProduct($id)
{
    $products=Product::findOrFail($id);
    if ($products->image_primary && Storage::disk('public')->exists($products->image_primary)) {
        Storage::disk('public')->delete($products->image_primary);
    }
    $products->delete();
        return redirect()->route('admin.products.listProduct')->with('success','Xóa thành công');
}
}
