<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\UserAddress;
use App\Services\UserService;
use App\Services\AddressService;
use App\Services\OrderService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $cartService;
    protected $userService;
    protected $addressService;
    protected $orderService;
    protected $voucherService;
    
    public function __construct(
        UserService $userService,
        AddressService $addressService,
        OrderService $orderService,
        VoucherService $voucherService
    ) {
        $this->orderService = $orderService;
        $this->userService = $userService;
        $this->addressService = $addressService;
        $this->voucherService = $voucherService;    
    }

    public function myAccount(Request $request)
    {
        $user = $this->userService->details();
        $addresses = $user ? $this->userService->getAddress($user) : collect([]);
        $data = $this->orderService->searchOrders($request, 10); // Gọi hàm searchOrders đã cập nhật
        $wardData = [];
        if ($addresses->count() > 0) {
            $wardIds = $addresses->pluck('id_ward')->filter()->unique()->toArray();
            $wards = Ward::with(['district.province'])->whereIn('id', $wardIds)->get();
            foreach ($wards as $ward) {
                $wardData[$ward->id] = $ward;
            }
        }
        $vouchers = $this->voucherService->getValidVouchers();
        if (isset($data['errors'])) {
            return view('client.user.index', compact('user', 'addresses', 'wardData','vouchers'))
                ->withErrors($data['errors']);
        }
        return view('client.user.index', compact('user', 'addresses', 'wardData','vouchers'), $data);
    }

    public function createAddress(Request $request)
    {
        // Remove the dd() call that was causing debugging issues
        // dd($request->all());
        $user = auth()->user();
        try {
            $address = $this->userService->createUserAddress($request, $user->id);
            return response()->json(['message' => 'Address created successfully', 'address' => $address]);
        } catch (\Exception $e) {
            Log::error('Error creating address: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function updateAddress(Request $request)
    {
        $user = auth()->user();

        try {
            $address = $this->userService->updateUserAddress($request, $user->id);
            return response()->json(['message' => 'Address updated successfully', 'address' => $address]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function deleteAddress(Request $request)
    {
        $user = auth()->user();

        try {
            $this->userService->deleteUserAddress($request->id, $user->id);
            return response()->json(['message' => 'Address deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request)
    {
        $response = $this->userService->updateUser($request);
        if ($response->status() === 422) {
            return response()->json($response->original, 422);
        }
        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Get all addresses for the authenticated user
     */
    public function getAddresses()
    {
        $user = auth()->user();

        try {
            $addresses = $this->userService->getAddressesWithRelations($user->id);
            return response()->json(['addresses' => $addresses]);
        } catch (\Exception $e) {
            Log::error('Error fetching addresses: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Set an address as the default address for the authenticated user
     */
    public function setDefaultAddress(Request $request)
    {
        $user = auth()->user();

        try {
            $address = $this->userService->setDefaultUserAddress($request->id, $user->id);
            return response()->json(['message' => 'Default address updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error setting default address: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
