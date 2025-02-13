<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller 
{
    /* public function index()
{
    $newProducts = Product::with(['variants' => function($query) {
        $query->where('status', 'active')
              ->orderBy('price', 'asc');
    }])
    ->where('status', 'active')
    ->latest()
    ->take(8)
    ->get();
    
    return view('client.layouts.home', compact('newProducts'));
} */
public function index()
{
    $newProducts = Product::where('status', 'active')
                         ->latest()
                         ->take(8)
                         ->get();
    
    return view('client.layouts.home', compact('newProducts'));
}



}
