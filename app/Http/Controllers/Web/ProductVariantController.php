<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.productVariant'])->first();
        if (!$cart) {
            $cart = new Cart();
        }
        return view('client.layouts.cart', compact('cart'));
    }

    public function listProductVariant()
    {
        $product_variant = Product_variant::with('product')->get();
        return view('admin.product_variant.index', compact('product_variant'));
    }

    public function addProductVariant()
    {
        $products = Product::all();
        return view('admin.product_variant.add', compact('products'));
    }

    public function addPostProductVariant(ProductVariantRequest $request)
    {
        $data = [
            'id_product' => $request->id_product,
            'sku' => $request->sku,
            'price' => $request->price,
            'price_sale' => $request->price_sale,
            'quantity' => $request->quantity,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('products/variants', 'public');
            $data['image'] = $path;
        }

        Product_variant::create($data);

        return redirect()->route('admin.productvariant.listProductVariant')
            ->with('success', 'Thêm biến thể sản phẩm thành công');
    }

    public function editProductVariant($id)
    {
        $products = Product::all();
        $product_variant = Product_variant::findOrFail($id);
        return view('admin.product_variant.edit', compact('products', 'product_variant'));
    }

    public function editPutProductVariant(Request $request, $id)
    {
        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string|in:active,inactive',
            'image' => 'nullable|image|max:2048'
        ], [
            'id_product.required' => 'Sản phẩm không được để trống.',
            'id_product.exists' => 'Sản phẩm không tồn tại trong hệ thống.',
            'sku.required' => 'Mã SKU không được để trống.',
            'sku.string' => 'Mã SKU phải là chuỗi ký tự.',
            'sku.max' => 'Mã SKU không được dài quá 255 ký tự.',
            'price.required' => 'Giá không được để trống.',
            'price.numeric' => 'Giá phải là số.',
            'price.min' => 'Giá không được âm.',
            'price_sale.numeric' => 'Giá khuyến mãi phải là số.',
            'price_sale.min' => 'Giá khuyến mãi không được âm.',
            'price_sale.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'quantity.required' => 'Số lượng không được để trống.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng không được âm.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'image.image' => 'File phải là ảnh.',
            'image.max' => 'Kích thước ảnh tối đa là 2MB.'
        ]);

        $product_variant = Product_variant::findOrFail($id);

        $data = [
            'id_product' => $request->id_product,
            'sku' => $request->sku,
            'price' => $request->price,
            'price_sale' => $request->price_sale,
            'quantity' => $request->quantity,
            'status' => $request->status
        ];

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($product_variant->image) {
                Storage::disk('public')->delete($product_variant->image);
            }
            
            // Lưu ảnh mới
            $image = $request->file('image');
            $path = $image->store('products/variants', 'public');
            $data['image'] = $path;
        }

        $product_variant->update($data);

        return redirect()->route('admin.productvariant.listProductVariant')
            ->with('success', 'Biến thể sản phẩm đã được cập nhật.');
    }

    public function deleteProductVariant($id)
    {
        $product_variant = Product_variant::findOrFail($id);
        
        // Xóa ảnh nếu có
        if ($product_variant->image) {
            Storage::disk('public')->delete($product_variant->image);
        }
        
        $product_variant->delete();
        
        return redirect()->route('admin.productvariant.listProductVariant')
            ->with('success', 'Xóa biến thể sản phẩm thành công');
    }

    public function list($id)
    {
        $product = Product::findOrFail($id);
        $productVariants = Product_variant::list($id)->get();
        return view('admin.product_variant.list', compact('productVariants', 'product'));
    }
}
