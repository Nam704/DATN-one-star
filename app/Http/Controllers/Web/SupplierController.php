<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Province;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplier;
    function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;

        // Chỉ cho phép supplier đã đăng nhập mới được truy cập
        // $this->middleware('auth');
    }
    function list()
    {
        $suppliers = $this->supplier->list();
        return view('admin.supplier.list', compact('suppliers'));
    }
    function getFormAdd()
    {
        $provinces = Province::all();
        return view('admin.supplier.add', compact('provinces'));
    }
    public function add(SupplierRequest $request)
    {
        $data = $request->validated();

        $data["address"] =  $data["addressSelected"] . " " . $data["address"];
        $supplier = Supplier::create($data);
        return redirect()->back()->with('success', 'Supplier created successfully!');
    }
    function lockOrActive($id)
    {
        $supplier = $this->supplier->query()->find($id);

        $isLock = ($supplier->status == 'inactive') ? true : false;
        if ($isLock) {
            $supplier->status = 'active';
            $supplier->save();
            return redirect()->route('admin.suppliers.list')->with('success', 'supplier actived successfully');
        } else {
            $supplier->status = 'inactive';
            $supplier->save();
            return redirect()->route('admin.suppliers.list')->with('success', 'supplier locked successfully');
        }
    }
    function getFormUpdate($id)
    {
        $supplier = $this->supplier->query()->find($id);
        return view('admin.supplier.edit', compact('supplier'));
    }
    public function edit($id, SupplierRequest $request)
    {
        $supplier = $this->supplier->query()->find($id);
        $data = $request->validated();
        $data["address"] =  $data["addressSelected"] . " " . $data["address"];
        $supplier->update($data);
        return redirect()->route('admin.suppliers.list')->with('success', 'supplier updated successfully');
    }
}
