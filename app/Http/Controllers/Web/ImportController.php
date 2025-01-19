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
use App\Services\ImportService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\ProductAuditService;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    protected $import;
    protected $ProductService;
    protected $ImportService;
    protected $NotificationService;
    function __construct(
        Import $import,
        NotificationService $notificationService,
        ImportService $importService,
        ProductService $productService
    ) {
        $this->ProductService = $productService;
        $this->NotificationService = $notificationService;
        $this->ImportService = $importService;
        $this->import = $import;
        $this->middleware('role:admin')->only('approveImport');
        $this->middleware('role:employee')->only('viewImport');
    }
    public function importExcel(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
        try {
            $file = $request->file('file');
            $importData = $this->ImportService->importByExcel($file);
            $this->NotificationService->sendImport($importData, $user);
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

        try {
            $data = [
                'supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note,
                'variant-product' => $request->input('variant-product'),
            ];
            $import = $this->ImportService->updateImport($id, $data);
            return response()->json([
                'status' => 'success',
                'message' => 'Import updated successfully',
                'redirect' => route('admin.imports.listPending')
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

        try {
            $data = [
                'supplier' => $request->supplier,
                'name' => $request->name,
                'import_date' => $request->import_date,
                'total_amount' => $request->total_amount,
                'note' => $request->note,
                'variant-product' => $request->input('variant-product'),
            ];
            $import = $this->ImportService->createImport($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Import created successfully',
                'redirect' => route('admin.imports.listPending')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Failed to create import');
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            // return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
