<?php

namespace App\Services;

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
    public function createUserAddress($request, $userId)
    {
        $user = $this->user->find($userId);
        if (!$user) {
            return null;
        }

        $validated = $request->validate([
            'address_detail' => 'required|string|max:250',
            'is_default' => 'required|boolean',
            'id_ward' => 'required|exists:wards,id',
        ]);
        $addressData = [
            'address_detail' => $validated['address_detail'],
            'is_default' => $validated['is_default'],
            'id_ward' => $validated['id_ward']
        ];
        // Tạo mới địa chỉ cho người dùng
        $address = $this->addressService->storeAddress($user, $user->id, $addressData);

        return $address;
    }
    public function updateUserAddress(Request $request, $id)
    {
        $user = $this->user->find($id);
        if (!$user) {
            return null;
        }
        $validated = $request->validate([
            'address' => 'required|array',
            'address.id' => 'required|exists:addresses,id',
            'address.address_detail' => 'required|string|max:250',
            'address.is_default' => 'required|boolean',
            'address.id_ward' => 'required|exists:wards,id',
        ]);
        $this->addressService->updateAddress(User::class, $user->id, $validated['address']['id'], $validated['address']);
    }
}
