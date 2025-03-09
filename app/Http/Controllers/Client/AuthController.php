<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $cartService;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function myAccount()
    {

        $data = [];
        $user = $this->userService->details();
        $addresses = $this->userService->getAddress($user);
        $orders = $user->orders;
        // return $data;
        return view('client.user.index', compact('user', 'addresses', 'orders'));
    }
    public function createAddress(Request $request)
    {
        $user = auth()->user();
        // Log::info($request->all());
        $address = $this->userService->createUserAddress($request, $user->id);
        return response()->json(['message' => 'Address created successfully', 'address' => $address]);
    }
    public function update(Request $request)
    {
        $response = $this->userService->updateUser($request);

        if ($response->status() === 422) {
            return response()->json($response->original, 422);
        }

        return response()->json(['message' => 'User updated successfully']);
    }
}
