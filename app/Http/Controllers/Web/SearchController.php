<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        if ($request->ajax()) {
            $query = $request->get('query');

            // Tìm kiếm sản phẩm theo tên (có thể bổ sung thêm điều kiện status, deleted_at nếu cần)
            $products = Product::where('name', 'LIKE', '%' . $query . '%')->get();

            // Tìm kiếm thương hiệu theo tên
            $brands = Brand::where('name', 'LIKE', '%' . $query . '%')
                ->where('status', 'active')
                ->get();

            // Tìm kiếm danh mục theo tên
            $categories = Category::where('name', 'LIKE', '%' . $query . '%')
                ->where('status', 'active')
                ->get();

            $output = '';
            if ($products->count() || $brands->count() || $categories->count()) {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';

                // Kết quả tìm kiếm sản phẩm
                foreach ($products as $product) {
                    $imgPath = asset($product->image_primary);
                    $url = route('client.products.detail', $product->id);

                    $output .= '<li class="dropdown-item" style="padding:5px; border-bottom:1px solid #ddd;">';
                    // Thẻ <a> bao bọc toàn bộ nội dung
                    $output .= '<a class="primary_img" href="' . $url . '" style="display:flex; align-items:center; text-decoration:none; color:inherit;">';
                    $output .= '<img src="' . $imgPath . '" alt="' . $product->name . '" style="width:50px; height:50px; margin-right:10px;">';
                    $output .= '<span>' . $product->name . '</span>';
                    $output .= '</a>';
                    $output .= '</li>';
                }

                // Kết quả tìm kiếm thương hiệu
                foreach ($brands as $brand) {
                    // Khi click, chuyển sang trang shop và truyền tham số filter theo thương hiệu
                    $url = route('client.shop', ['brands' => $brand->id]);
                    $output .= '<li class="dropdown-item" style="padding:5px; border-bottom:1px solid #ddd;">';
                    $output .= '<a class="primary_img" href="' . $url . '" style="display:flex; align-items:center; text-decoration:none; color:inherit;">';
                    $output .= '<span>Thương hiệu: ' . $brand->name . '</span>';
                    $output .= '</a>';
                    $output .= '</li>';
                }

                // Kết quả tìm kiếm danh mục
                foreach ($categories as $category) {
                    // Khi click, chuyển sang trang shop và truyền tham số filter theo danh mục sử dụng key "categories"
                    $url = route('client.shop', ['categories' => $category->id]);
                    $output .= '<li class="dropdown-item" style="padding:5px; border-bottom:1px solid #ddd;">';
                    $output .= '<a class="primary_img" href="' . $url . '" style="display:flex; align-items:center; text-decoration:none; color:inherit;">';
                    $output .= '<span>Danh mục: ' . $category->name . '</span>';
                    $output .= '</a>';
                    $output .= '</li>';
                }

                $output .= '</ul>';
            } else {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';
                $output .= '<li class="dropdown-item">Không tìm thấy kết quả nào</li>';
                $output .= '</ul>';
            }
            return response($output);
        }
    }
}
