<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Address;
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
    public function list()
    {
        // Lấy danh sách các Supplier kèm quan hệ address
        $suppliers = $this->supplier->query()->with('addresses')->latest('id')->paginate(10);

        // Gắn thêm full_address cho từng Supplier
        foreach ($suppliers as $supplier) {
            $address = $supplier->addresses;
            if ($address) {
                $addressDetails = Address::getAddressDetailsByWard($address->id_ward);

                $supplier->full_address = $addressDetails->map(function ($detail) {
                    return "{$detail->address_detail}, {$detail->ward_name}, {$detail->district_name}, {$detail->province_name}";
                })->first(); // Lấy địa chỉ đầy đủ (nếu có).
            } else {
                $supplier->full_address = 'N/A';
            }
        }

        // Trả về view cùng dữ liệu suppliers
        return view('admin.supplier.list', compact('suppliers'));
    }

    function getFormAdd()
    {

        return view('admin.supplier.add');
    }
    public function add(SupplierRequest $request)
    {
        $data = $request->validated();
        $supplier = Supplier::create($data);
        $supplier->addresses()->create([
            'address_detail' => $request->address_detail,
            'is_default' => false,
            'id_ward' => $request->ward
        ]);
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
