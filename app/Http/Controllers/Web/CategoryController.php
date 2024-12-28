<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.category.index');
    }
    public function addCategory(){
        return view('admin.category.add');
    }
}
