<?php

namespace App\Http\Controllers\Web;

use App\Models\Order;
use App\Models\Refund;
use App\Enums\RefundStatus;
use App\Services\RefundService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends Controller
{
    // Form yêu cầu hoàn tiền (cho người dùng)
    public function create()
    {
        $orders = auth()->user()->orders()
            ->with('status')
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['Return Requested', 'Return Approved']);
            })
            ->get();

        return view('client.refunds.create', compact('orders'));
    }

    // Xử lý submit form (cho người dùng)
    public function store(Request $request, RefundService $service)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20|regex:/^[0-9]+$/',
            'account_holder' => 'required|string|max:255',
        ], [
            'account_number.regex' => 'Số tài khoản chỉ được chứa chữ số.',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            $bankDetails = $request->only('bank_name', 'account_number', 'account_holder');

            // Kiểm tra xem đơn hàng đã có yêu cầu hoàn tiền chưa
            if ($order->refunds()->exists()) {
                throw new \Exception('Đơn hàng này đã có yêu cầu hoàn tiền trước đó.');
            }

            $service->createRefundRequest($order, $bankDetails);
            return redirect()->back()->with('success', 'Đã gửi yêu cầu hoàn tiền!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput() // Giữ lại giá trị đã nhập
                ->with('error', $e->getMessage());
        }
    }

    // Danh sách yêu cầu chờ xử lý (cho nhân viên)
    public function index()
    {
        $refunds = Refund::with(['order', 'user'])
            ->pending()
            ->get();

        return view('admin.refunds.index', compact('refunds'));
    }

    // Duyệt yêu cầu (cho nhân viên)
    public function approve(Refund $refund)
    {
        $refund->update(['status' => RefundStatus::APPROVED]);
        return back()->with('success', 'Đã duyệt yêu cầu!');
    }

    // Từ chối yêu cầu (cho nhân viên)
    public function reject(Refund $refund, Request $request)
    {
        $request->validate(['notes' => 'required|string']);

        $refund->update([
            'status' => RefundStatus::REJECTED,
            'staff_notes' => $request->notes
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu!');
    }

    // Xử lý hoàn tiền cuối cùng (cho quản lý)
    public function finalProcess(Refund $refund, RefundService $service)
    {
        try {
            $service->processManualRefund($refund);
            return back()->with('success', 'Hoàn tiền thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
