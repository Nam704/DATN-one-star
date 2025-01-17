<?php

namespace App\Http\Controllers\Web;


use App\Events\ImportNotificationSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Imports\ImportProducts;
use App\Models\Import;
use App\Models\Import_detail;
use App\Models\Product;
use App\Models\Product_variant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ProductAuditService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    protected $import;
    function __construct(Import $import)
    {
        $this->import = $import;
        $this->middleware('role:admin')->only('approveImport');

        // Employee có thể truy cập viewImport
        $this->middleware('role:employee')->only('viewImport');
    }
    public function importExcel(Request $request, ProductAuditService $productAuditService)
    {
        $user = auth()->user();
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file);
            $importProducts = new ImportProducts($productAuditService);
            $importData =   $importProducts->process($data[0], $data[1]);
            // dd($importData);
            broadcast(new ImportNotificationSent($importData, $user));
            return redirect()->route('admin.imports.listApproved')->with('success', 'Thông báo đã được gửi đi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    function detail($id)
    {
        $import = Import::find($id);
        $import_details = Import_detail::where('id_import', $id)->get();
        return view('admin.import.detail', compact('import', 'import_details'));
    }
    function getFormEdit($id)
    {
        $import = Import::with(['supplier', 'import_details.product_variant'])->findOrFail($id);
        $suppliers = Supplier::list()->get();
        $products = Product::list()->get();

        return view('admin.import.edit', compact('import', 'suppliers', 'products'));
    }

    function edit(ImportRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $import = Import::findOrFail($id);

            // Lưu lại các chi tiết cũ để xử lý số lượng kho
            $oldDetails = Import_detail::where('id_import', $id)->get();

            // Cập nhật thông tin chính của import
            $import->update([
                'id_supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note
            ]);

            // Cập nhật lại số lượng kho dựa trên dữ liệu cũ
            foreach ($oldDetails as $oldDetail) {
                $productVariant = Product_variant::find($oldDetail->id_product_variant);
                if ($productVariant) {
                    $productVariant->quantity -= $oldDetail->quantity;
                    $productVariant->save();
                }
            }

            // Xóa chi tiết cũ sau khi xử lý số lượng kho
            Import_detail::where('id_import', $id)->delete();

            // Tạo mới các chi tiết nhập hàng
            foreach ($request->input('variant-product') as $variant) {
                // Kiểm tra tính hợp lệ của từng variant
                $productVariant = Product_variant::find($variant['product_variant_id']);
                if (!$productVariant) {
                    throw new \Exception("Product variant không tồn tại: " . $variant['product_variant_id']);
                }

                // Tạo chi tiết nhập hàng mới
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);

                // Cập nhật lại số lượng kho
                $productVariant->quantity += $variant['quantity'];
                $productVariant->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Import updated successfully',
                'redirect' => route('admin.imports.list')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    function getFormAdd()
    {
        $suppliers = Supplier::list()->get();
        $products = Product::list()->get();
        return view('admin.import.add', compact('suppliers', 'products'));
    }
    function listApproved()
    {
        $imports = Import::listApproved()->paginate(100);
        return view('admin.import.list', compact('imports'));
    }
    function listPending()
    {
        $imports = Import::listPending()->paginate(100);
        return view('admin.import.list', compact('imports'));
    }
    function listRejected()
    {
        $imports = Import::listReject()->paginate(100);
        return view('admin.import.list', compact('imports'));
    }

    function accept(Request $request, $id)
    {
        try {
            $import = Import::findOrFail($id);
            if ($import->status == 'approved') {
                return redirect()->back()->with('error', 'Import đã được chấp nhận trước đó!');
            } elseif ($import->status == 'rejected') {
                return redirect()->back()->with('error', 'Import đã bị từ chối trước đó!');
            } elseif ($import->status == 'pending') {
                $import->status = 'approved';
                $import->save();
                return redirect()->route('admin.imports.listApproved')->with('success', 'Import đã được chấp nhận!');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    function reject(Request $request, $id)
    {
        try {
            $import = Import::findOrFail($id);
            if ($import->status == 'approved') {
                return redirect()->back()->with(
                    'error',
                    'Import đã được chấp nhận trước đó!'
                );
            } elseif ($import->status == 'rejected') {
                return redirect()->back()->with(
                    'error',
                    'Import đã bị từ chối trước đó!'
                );
            } elseif ($import->status == 'pending') {
                $import->status = 'rejected';
                $import->save();
                return redirect()->route('admin.imports.listRejected')
                    ->with('success', 'Import đã bị từ chối!');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function add(ImportRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            // Create import invoice
            $import = Import::create([
                'id_supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note
            ]);

            // Create import details
            foreach ($request->input('variant-product') as $variant) {
                Import_detail::create([
                    'id_import' => $import->id,
                    'id_product_variant' => $variant['product_variant_id'],
                    'quantity' => $variant['quantity'],
                    'price_per_unit' => $variant['price_per_unit'],
                    'expected_price' => $variant['expected_price'],
                    'total_price' => $variant['total_price']
                ]);
                // Update product variant quantity
                $productVariant = Product_variant::find($variant['product_variant_id']);
                $productVariant->quantity += $variant['quantity'];
                $productVariant->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Import created successfully',
                'redirect' => route('admin.imports.list')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Failed to create import');
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
