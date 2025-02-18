<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product_audit;
use App\Models\Product_variant;
use App\Models\User;
use App\Services\ProductAuditService;
use Illuminate\Http\Request;

class ProductAuditController extends Controller
{
    protected $ProductAuditService;
    public function __construct(ProductAuditService $ProductAuditService)
    {
        $this->ProductAuditService = $ProductAuditService;
    }
    function list()
    {
        $audits = $this->ProductAuditService->getAuditApproved();
        // $audits = $this->ProductAuditService->getAuditPending();


        return view('admin.product_audits.list', compact('audits'));
    }
    public function index()
    {
        $audits = Product_audit::with(['user', 'productVariant'])->get();
        return view('admin.product_audits.index', compact('audits'));
    }

    public function create()
    {
        $users = User::all();
        $productVariants = Product_variant::all();
        return view('admin.product_audits.create', compact('users', 'productVariants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_product_variant' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer',
            'action_type' => 'required|in:add,remove,update',
            'reason' => 'nullable|string',
        ]);

        Product_audit::create($validated);
        return redirect()->route('admin.product_audits.index')->with('success', 'Product Audit created successfully.');
    }

    public function edit($id)
    {
        $audit = Product_audit::findOrFail($id);
        $users = User::all();
        $productVariants = Product_variant::all();
        return view('admin.product_audits.edit', compact('audit', 'users', 'productVariants'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_product_variant' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer',
            'action_type' => 'required|in:add,remove,update',
            'reason' => 'nullable|string',
        ]);

        $audit = Product_audit::findOrFail($id);
        $audit->update($validated);
        return redirect()->route('admin.product_audits.index')->with('success', 'Product Audit updated successfully.');
    }

    public function show($id)
    {
        $audit = Product_audit::with(['user', 'productVariant'])->findOrFail($id);
        return view('admin.product_audits.show', compact('audit'));
    }
}
