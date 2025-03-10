<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $query = $request->get('query');
            // Tìm kiếm sản phẩm theo tên
            $products = DB::table('products')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->get();

            $output = '';
            if ($products->count() > 0) {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';
                foreach ($products as $product) {
                    $output .= '<li class="dropdown-item" style="display:flex; align-items:center; padding:5px; border-bottom: 1px solid #ddd;">';
                    $imgPath = asset('storage/' . $product->image_primary);
                    $output .= '<img src="' . $imgPath . '" alt="' . $product->name . '" style="width:50px; height:50px; margin-right:10px;">';
                    $output .= $product->name;
                    $output .= '</li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;"><li class="dropdown-item">Không tìm thấy sản phẩm nào</li></ul>';
            }
            return response($output);
        }
    }
}
