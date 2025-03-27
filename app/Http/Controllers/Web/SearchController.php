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
                    $imgPath = asset($product->image_primary);
                    $url = route('client.products.detail', $product->id);

                    $output .= '<li class="dropdown-item" style="padding:5px; border-bottom: 1px solid #ddd;">';
                    $output .= '<a class="primary_img" href="' . $url . '" style="display:flex; align-items:center; text-decoration:none; color:inherit;">';
                    $output .= '<img src="' . $imgPath . '" alt="' . $product->name . '" style="width:50px; height:50px; margin-right:10px;">';
                    $output .= '<span>' . $product->name . '</span>';
                    $output .= '</a>';
                    $output .= '</li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<ul class="dropdown-menu" style="display:block; width:100%;">';
                $output .= '<li class="dropdown-item">Không tìm thấy sản phẩm nào</li>';
                $output .= '</ul>';
            }
            return response($output);
        }
    }
}
