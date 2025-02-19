<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Address;
use App\Models\District;
use App\Models\Province;
use App\Models\Supplier;
use App\Models\Ward;
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
        $suppliers = $this->supplier->query()->with('address')->latest('id')->paginate(20);

        // Gắn thêm full_address cho từng Supplier
        foreach ($suppliers as $supplier) {
            $address = $supplier->address;
            if ($address) {
                $addressDetails = Address::getAddressSupplier($address->id_ward, $supplier->id);
                // dd($addressDetails);
                $supplier->full_address = $addressDetails->map(function ($detail) {
                    return "{$detail->address_detail}, {$detail->ward_name}, {$detail->district_name}, {$detail->province_name}";
                })->first();
            } else {
                $supplier->full_address = 'N/A';
            }
        }


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
        $supplier->address()->create([
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
    public function getFormUpdate($id)
    {
        $supplier = $this->supplier->query()->with('address')->find($id);
        $fullAddress = null;
        // dd($supplier);
        if ($supplier && $supplier->address) {
            // Lấy thông tin chi tiết địa chỉ dựa trên id_ward
            $addressDetails = Address::getAddressDetailsByWard($supplier->address->id_ward);
            // dd($addressDetails);
            if ($addressDetails->isNotEmpty()) {
                $detail = $addressDetails->first();
                // dd($detail);

                $fullAddress = [
                    'address_detail' => $supplier->address->address_detail,
                    'ward_id' => $supplier->address->id_ward,
                    'ward_name' => $detail->ward_name,
                    'district_id' => $detail->district_id,
                    'district_name' => $detail->district_name,
                    'province_id' => $detail->province_id,
                    'province_name' => $detail->province_name,
                ];
                // dd($fullAddress);
            }
        }

        // Danh sách tỉnh, quận, phường để hiển thị trong form
        $provinces = Province::all();
        $districts = $fullAddress ? District::where('province_id', $fullAddress['province_id'])->get() : [];
        $wards = $fullAddress ? Ward::where('district_id', $fullAddress['district_id'])->get() : [];

        return view('admin.supplier.edit', compact('supplier', 'fullAddress', 'provinces', 'districts', 'wards'));
    }


    public function edit($id, SupplierRequest $request)
    {

        $supplier = $this->supplier->query()->find($id);
        $data = $request->validated();
        $dataAddress = [
            'address_detail' => $request->address_detail,
            'is_default' => false,
            'id_ward' => $request->ward
        ];
        $supplier->update($data);
        $supplier->address->update($dataAddress);
        return redirect()->route('admin.suppliers.list')->with('success', 'supplier updated successfully');
    }

}
