<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    public function payment(Request $request)
    {
        // $this->paymentService->vnpay_payment();
    }
    public function index()
    {
        $data = session('dataCheckout');
        $user = $data["user"];
        $address = $user->getfullAddress();
        return view('client.checkout.index', compact('data', 'address'));
    }
    public function create(Request $request)
    {
        $data = $request->input('data');
        $cart = isset($data['cart']) ? $data['cart'] : [];
        $details = isset($data['details']) ? $data['details'] : [];
        $id_user = isset($cart['id_user']) ? $cart['id_user'] : [];
        $user = User::find($id_user);
        $dataFomatted = [
            'cart' => $cart,
            'details' => $details,
            'user' => $user,
        ];
        session(
            [
                'dataCheckout' => $dataFomatted,
            ]
        );
        $link = route('client.checkout.index');
        return response()->json([
            'message' => 'success',
            'link' => $link,

        ]);
    }
}
