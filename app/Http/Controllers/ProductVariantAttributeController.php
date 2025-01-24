<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attribute_value;
use App\Models\Product;
use App\Models\Product_variant;
use App\Models\Product_variant_attribute;
use App\Http\Requests\ProductVariantAttributeRequest;

class ProductVariantAttributeController extends Controller
{
    public function index()
    {
        $pva = Product_variant_attribute::with(['productVariant', 'attributeValue', 'product'])->orderBy('id', 'desc')->get();
        return view('admin.product_variant_attribute.list', compact('pva'));
    }

    public function create()
    {
        $product_variant = Product_variant::with('product')->get();
        $attribute_value = Attribute_value::with('attribute')->get();
        return view('admin.product_variant_attribute.add', compact('product_variant', 'attribute_value'));
    }

    public function store(ProductVariantAttributeRequest $request)
    {
        $exists = Product_variant_attribute::where('id_product_variant', $request->id_product_variant)
            ->where('id_attribute_value', $request->id_attribute_value)
            ->exists();

        if ($exists) {
            return redirect()->back()->with(['message_error' => 'Thuộc tính biến thể này đã tồn tại.']);
        }

        Product_variant_attribute::create([
            'id_attribute_value' => $request->id_attribute_value,
            'id_product_variant' => $request->id_product_variant,
        ]);

        return redirect()->route('admin.product_variant_attributes.index')->with([
            'message' => 'Thêm giá trị thuộc tính biến thể thành công!',
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $product_variant = Product_variant::with('product')->get();
        $attribute_value = Attribute_value::with('attribute')->get();
        $pva = Product_variant_attribute::with(['productVariant', 'attributeValue', 'product'])->find($id);
        return view('admin.product_variant_attribute.update', compact('product_variant', 'attribute_value', 'pva'));
    }

    public function update(ProductVariantAttributeRequest $request, string $id)
    {
        $exists = Product_variant_attribute::where('id_product_variant', $request->id_product_variant)
            ->where('id_attribute_value', $request->id_attribute_value)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with(['message_error' => 'Thuộc tính biến thể này đã tồn tại.']);
        }

        $productVariantAttribute = Product_variant_attribute::findOrFail($id);

        $productVariantAttribute->update([
            'id_attribute_value' => $request->id_attribute_value,
            'id_product_variant' => $request->id_product_variant,
        ]);

        return redirect()->route('admin.product_variant_attributes.index')->with([
            'message' => 'Cập nhật giá trị thuộc tính biến thể thành công!',
        ]);
    }

    public function destroy(string $id)
    {
        $pva = Product_variant_attribute::find($id);
        $pva->delete();

        return redirect()->route('admin.product_variant_attributes.index')->with([
            'message' => 'Xóa giá trị thuộc tính biến thể thành công!',
        ]);
        ;
    }

    public function trash()
    {
        $trash_pva = Product_variant_attribute::with(['productVariant', 'attributeValue', 'product'])->onlyTrashed()->get();
        return view('admin.product_variant_attribute.trash', compact('trash_pva'));
    }

    public function restore(string $id)
    {
        $pva = Product_variant_attribute::withTrashed()->find($id);
        $pva->restore();

        return redirect()->route('admin.product_variant_attributes.index')->with([
            'message' => 'Khôi phục giá trị thuộc tính thành công!',
        ]);
        ;
    }
}
