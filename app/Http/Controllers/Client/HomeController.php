<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $categoryService;
    function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    function index()
    {
        $categories = $this->categoryService->getCategories();
        // return ($categories);
        return view('client.index', compact('categories'));
    }
}
