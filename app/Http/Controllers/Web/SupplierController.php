<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplier;
    function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;

        // Chỉ cho phép user đã đăng nhập mới được truy cập
        // $this->middleware('auth');
    }
    function getFormAdd()
    {
        return view('admin.supplier.add');
    }
    public function add(SupplierRequest $request)
    {
        $data = $request->validated();
        $data["address"] =  $data["addressSelected"] . " " . $data["address"];
        // dd($data);
        $supplier = Supplier::create($data);
        return redirect()->back()->with('success', 'Supplier created successfully!');
    }
}
