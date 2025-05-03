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
        // Kiểm tra nếu request được gửi qua Ajax
        if ($request->ajax()) {
            // Lấy từ khóa tìm kiếm từ request
            $query = $request->input('query');

            // Tìm kiếm sản phẩm theo tên và giới hạn kết quả chỉ 5 sản phẩm
            $products = Product::where('name', 'LIKE', '%' . $query . '%')
                ->limit(5)
                ->get();

            // Tìm kiếm thương hiệu theo tên (với điều kiện trạng thái active) và giới hạn kết quả 5
            $brands = Brand::where('name', 'LIKE', '%' . $query . '%')
                ->where('status', 'active')
                ->limit(5)
                ->get();

            // Tìm kiếm danh mục theo tên (với điều kiện trạng thái active) và giới hạn kết quả 5
            $categories = Category::where('name', 'LIKE', '%' . $query . '%')
                ->where('status', 'active')
                ->limit(5)
                ->get();

            // Khởi tạo chuỗi output chứa HTML trả về
            $output = '';

            // Nếu có kết quả từ ít nhất một trong 3 model
            if ($products->count() || $brands->count() || $categories->count()) {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';

                // Hiển thị kết quả tìm kiếm sản phẩm
                foreach ($products as $product) {
                    // Lấy đường dẫn ảnh sản phẩm và URL chi tiết sản phẩm
                    $imgPath = asset($product->image_primary);
                    $url = route('client.products.detail', $product->id);

                    $output .= '<li class="dropdown-item" style="padding:5px; border-bottom:1px solid #ddd;">';
                    // Gói toàn bộ nội dung trong thẻ <a> để khi click sẽ chuyển đến trang chi tiết sản phẩm
                    $output .= '<a class="primary_img" href="' . $url . '" style="display:flex; align-items:center; text-decoration:none; color:inherit;">';
                    $output .= '<img src="' . $imgPath . '" alt="' . $product->name . '" style="width:50px; height:50px; margin-right:10px;">';
                    $output .= '<span>' . $product->name . '</span>';
                    $output .= '</a>';
                    $output .= '</li>';
                }

                // Hiển thị kết quả tìm kiếm thương hiệu
                foreach ($brands as $brand) {
                    // Khi click, chuyển hướng đến trang shop với filter theo thương hiệu
                    $url = route('client.shop', ['brands' => $brand->id]); // bạn có thể xóa href nếu không dùng
                    $output .= '<li class="dropdown-item">'
                        . '<a href="#"'
                        . '   data-type="brand"'
                        . '   data-id="' . $brand->id . '"'
                        . '   data-name="' . e($brand->name) . '">'
                        . '<span>Thương hiệu: ' . $brand->name . '</span>'
                        . '</a></li>';
                }

                // Hiển thị kết quả tìm kiếm danh mục
                foreach ($categories as $category) {
                    // Khi click, chuyển hướng đến trang shop với filter theo danh mục
                    $url = route('client.shop', ['categories' => $category->id]);
                    $output .= '<li class="dropdown-item">'
                        . '<a href="#"'
                        . '   data-type="category"'
                        . '   data-id="' . $category->id . '"'
                        . '   data-name="' . e($category->name) . '">'
                        . '<span>Danh mục: ' . $category->name . '</span>'
                        . '</a></li>';
                }
                $output .= '</ul>';
            } else {
                // Nếu không có kết quả nào, hiển thị thông báo
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';
                $output .= '<li class="dropdown-item">Không tìm thấy kết quả nào</li>';
                $output .= '</ul>';
            }
            // Trả về HTML đã render
            return response($output);
        }

        // Nếu không phải request Ajax, trả về JSON rỗng
        return response()->json([]);
    }
}
