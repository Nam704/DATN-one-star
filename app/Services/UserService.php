<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $user;
    protected $addressService;
    public function __construct(User $user, AddressService $addressService)
    {
        $this->user = $user;
        $this->addressService = $addressService;
    }
    public function details()
    {
        $user = auth()->user();
        return $user;
    }
    public function getAddress($user)
    {
        $addresses = $this->addressService->getFullAddress($user, $user->id);
        return $addresses;
    }

    // public function createUser(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:20',
    //         'profile_image' => 'nullable|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users,email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     // Tạo mới người dùng
    //     $user = $this->user->create([
    //         'name' => $validated['name'],
    //         'phone' => $validated['phone'],
    //         'profile_image' => $validated['profile_image'],
    //         'email' => $validated['email'],
    //         'password' => bcrypt($validated['password']),
    //     ]);

    //     return $user;
    // }
    public function updateUser($request)
    {

        $id = $request->input('id');
        $user = $this->user->find($id);
        if (!$user) {
            return null;
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $id,
            'old_password' => 'nullable|string|required_with:new_password', // Bắt buộc nhập nếu có new_password
            'new_password' => 'nullable|string|min:8|max:15|confirmed',
        ]);

        // Kiểm tra mật khẩu cũ nếu có đổi mật khẩu mới
        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['errors' => ['old_password' => ['Old password is incorrect.']]], 422);
            }
            $user->password = Hash::make($request->new_password);
        }

        // Cập nhật thông tin user
        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? $user->email,
        ]);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }
    public function createUserAddress(Request $request, $userId)
    {
        // If this address is default, unset all other default addresses for this user
        if ($request->is_default) {
            Address::where('addressable_id', $userId)
                ->update(['is_default' => 0]);
        }

        // Create new address with proper validation
        return Address::create([
            'addressable_id' => $userId,
            'addressable_type' => 'App\Models\User',
            'id_ward' => $request->id_ward,
            'address_detail' => $request->address_detail,
            'is_default' => $request->is_default ? 1 : 0
        ]);
    }

    public function updateUserAddress(Request $request, $userId)
    {
        $address = Address::where('id', $request->id)
            ->where('addressable_id', $userId)
            ->first();

        if (!$address) {
            throw new \Exception('Address not found or does not belong to this user');
        }

        // If this address is being set as default, unset all other defaults
        if ($request->is_default) {
            Address::where('addressable_id', $userId)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => 0]);
        }

        // Update the address
        $address->id_ward = $request->id_ward;
        $address->address_detail = $request->address_detail;
        $address->is_default = $request->is_default ? 1 : 0;
        $address->save();

        return $address;
    }

    public function deleteUserAddress($addressId, $userId)
    {
        $address = Address::where('id', $addressId)
            ->where('addressable_id', $userId)
            ->first();

        if (!$address) {
            throw new \Exception('Address not found or does not belong to this user');
        }

        $addressCount = Address::where('addressable_id', $userId)->count();
        if ($address->is_default && $addressCount > 1) {
            $newDefault = Address::where('addressable_id', $userId)
                ->where('id', '!=', $addressId)
                ->first();
            $newDefault->is_default = 1;
            $newDefault->save();
        } elseif ($address->is_default && $addressCount == 1) {
            throw new \Exception('Cannot delete the only default address');
        }

        return $address->forceDelete();
    }

    /**
     * Get addresses with ward, district, and province relations
     */
    public function getAddressesWithRelations($userId)
    {
        return Address::with(['ward.district.province'])
            ->where('addressable_id', $userId)
            ->get();
    }

    /**
     * Set an address as the default address
     */
    public function setDefaultUserAddress($addressId, $userId)
    {
        $address = Address::where('id', $addressId)
            ->where('addressable_id', $userId)
            ->first();

        if (!$address) {
            throw new \Exception('Address not found or does not belong to this user');
        }

        // First, unset all default addresses for this user
        Address::where('addressable_id', $userId)
            ->update(['is_default' => 0]);

        // Then set this address as default
        $address->is_default = 1;
        $address->save();

        return $address;
    }
}
