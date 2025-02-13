<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductVariantRequest;
use App\Models\Product;
use App\Models\Product_variant;
use Illuminate\Http\Request;

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
        return view('admin.product_variant.index')->with([
            'product_variant' => $product_variant
        ]);
    }

    public function addProductVariant()
    {
        $products = Product::all();
        return view('admin.product_variant.add')->with([
            'products' => $products
        ]);
    }


    public function addPostProductVariant(ProductVariantRequest $request)
    {
        Product_variant::create([
            'id_product' => $request->id_product,
            'sku' => $request->sku,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.productvariant.listProductVariant')->with('success', 'Thêm biến thể sản phẩm thành công');
    }

    public function editProductVariant($id)
    {
        $products = Product::all();
        $product_variant = Product_variant::findorFail($id);
        return view('admin.product_variant.edit')->with([
            'products' => $products,
            'product_variant' => $product_variant
        ]);
    }

    public function editPutProductVariant(Request $request, $id)
    {
        $validated = $request->validate([
            'id_product' => 'required|exists:products,id',
            'sku' => 'required|string|max:255',
            'status' => 'required|string',
        ], [
            'id_product.required' => 'Sản phẩm không được để trống.',
            'id_product.exists' => 'Sản phẩm không tồn tại trong hệ thống.',
            'sku.required' => 'Mã SKU không được để trống.',
            'sku.string' => 'Mã SKU phải là chuỗi ký tự.',
            'sku.max' => 'Mã SKU không được dài quá 255 ký tự.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.string' => 'Trạng thái phải là chuỗi ký tự.',
        ]);

        $product_variant = Product_variant::findOrFail($id);

        $product_variant->update([
            'id_product' => $request->id_product,
            'sku' => $request->sku,
            'status' => $request->status

        ]);


        return redirect()->route('admin.productvariant.listProductVariant')->with('success', 'Biến thể sản phẩm đã được cập nhật.');
    }

    public function deleteProductVariant($id)
    {
        $categories = Product_variant::findOrFail($id);
        $categories->delete();
        return redirect()->route('admin.productvariant.listProductVariant')->with('success', 'Xóa thành công');
    }
    public function list($id)
    {
        $product = Product::find($id);
        $productVariants = Product_variant::list($id)->get();
        // dd($productVariants);
        return view('admin.product_variant.list', compact('productVariants', 'product'));
    }
}
