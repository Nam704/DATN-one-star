<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected $import;
    function __construct(Import $import)
    {
        $this->import = $import;
    }
    function getFormAdd()
    {
        $suppliers = Supplier::list()->get();
        $products = Product::list()->get();
        return view('admin.import.add', compact('suppliers', 'products'));
    }
}
