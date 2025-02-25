<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\CreateProductByExcel;
use App\Services\ProductService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelController extends Controller
{
    protected $ProductService;
    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }
    function createByExcel(Request $request)
    {
        $request->validate(["excel_file" => "required|mimes:xlsx,xls"]);
        $excelFile = $request->file('excel_file');
        $path = $excelFile->store('excels');
        CreateProductByExcel::dispatch($path, auth()->user()->id);
        return redirect()->route('admin.products.list')->with('success', 'Sản phẩm đã được nhập thành công!');
    }
}
