<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserContronler extends Controller
{
    // protected $user;
    // function __construct(User $user)
    // {
    //     $this->user = $user;
    // }
    // function listAdmin()
    // {
    //     $userCurrent = auth()->user();

    //     if ($userCurrent->isAdmin()) {
    //         $users = $this->user->list();
    //     } elseif ($userCurrent->isEmployee()) {
    //         $users = $this->user->list()->whereIn('role_name', ['employee', 'user']);
    //     } else {
    //         return redirect()->route('auth.getFormLogin');
    //     }

    //     return view('admin.user.list', compact('users'));
    // }


    // function resetPassword($id)
    // {
    //     $user = User::find($id);
    // }

    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })
            ->where('is_lock', false)  // Exclude locked users
            ->get();

        return view('admin.user.list', compact('users'));
    }


    public function listemployee()
    {
        $users = User::with('role') // Lấy người dùng kèm vai trò
            ->whereHas('role', function ($query) {
                $query->where('name', 'employee');
            })
            ->where('is_lock', '!=', 'inactive')
            ->get();

        return view('admin.user.listemployee', compact('users'));
    }

    public function listuser()
    {
        // Lấy danh sách người dùng có vai trò 'user'
        $users = User::with('role') // Lấy quan hệ với role
            ->whereHas('role', function ($query) {
                $query->where('name', 'user'); // Đảm bảo không có khoảng trắng dư
            })
            ->where('is_lock', '!=', 'inactive')
            ->get();

        // Trả về view với danh sách người dùng
        return view('admin.user.listuser', compact('users'));
    }
    public function listtkkhoa()
    {

        $listTaiKhoan = User::where('is_lock', 'inactive')->get(); // Lọc các tài khoản bị khóa
        return view('admin.user.listtkkhoa', compact('listTaiKhoan'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[\w\.-]+@(fpt\.edu\.vn|gmail\.com)$/',
            ],
            'phone' => [
                'required',
                'unique:users,phone',
                'regex:/^0(3|5|7|8|9)\d{8}$/', // Thêm dấu phân cách đúng
            ],
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'address_detail' => 'required|string|max:255',
            'id_role' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên người dùng.',
            'name.unique' => 'Tên người dùng đã tồn tại.',

            'email.required' => 'Email không được trống',
            'email.regex' => 'Email không hợp lệ',
            'email.max' => 'Email không quá 255 ký tự',
            'email.unique' => 'Email đã có',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'phone.regex' => 'Số điện thoại không hợp lệ. Số điện thoại phải bắt đầu bằng 03, 05, 07, 08, 09 và có 10 chữ số.',

            'password.required' => 'Mật khẩu không được trống',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự',
            'password.regex' => 'Mật khẩu phải chứa ít nhất một chữ cái viết hoa, một chữ cái viết thường, một chữ số và một ký tự đặc biệt.',

            'province_id.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
            'province_id.exists' => 'Tỉnh / Thành phố không hợp lệ.',

            'district_id.required' => 'Vui lòng chọn Quận / Huyện.',
            'district_id.exists' => 'Quận / Huyện không hợp lệ.',

            'ward_id.required' => 'Vui lòng chọn Xã / Phường.',
            'ward_id.exists' => 'Xã / Phường không hợp lệ.',

            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết.',

            'id_role.required' => 'Vui lòng chọn quyền hạn.',
            'id_role.exists' => 'Quyền hạn không hợp lệ.',

            'profile_image.image' => 'Tệp tải lên phải là ảnh.',
            'profile_image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif,webp,svg.',
            'profile_image.max' => 'Kích thước ảnh tối đa là 2MB.',
        ]);


        // Handle profile image upload (optional)
        $profileImagePath = null;

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            if ($file->isValid()) {
                $profileImagePath = $file->store('profile_image', 'public');
            } else {
                return back()->withErrors(['profile_image' => 'Ảnh không hợp lệ hoặc bị lỗi khi tải lên.'])->withInput();
            }
        }

        $currentUser = Auth::user();

        // Lấy role được chọn từ id_role
        $selectedRole = Role::find($request->id_role);

        if (!$selectedRole) {
            return redirect()->back()->with('error', 'Quyền hạn không hợp lệ.');
        }

        // Nếu là nhân viên thì chỉ được tạo người dùng
        if ($currentUser->role->name === 'employee') {
            if (in_array($selectedRole->name, ['admin', 'employee'])) {
                return redirect()->back()->with('error', 'Bạn không có quyền tạo tài khoản quản trị hoặc nhân viên.');
            }

            // Tạo tài khoản người dùng trực tiếp
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'id_role' => $request->id_role,
                'profile_image' => $profileImagePath,
                'status' => 'active',
                'is_lock' => 'active',
            ]);

            $user->address()->create([
                'address_detail' => $request->address_detail,
                'id_ward' => $request->ward_id,
                'is_default' => true,
            ]);

            return redirect()->route('admin.users.listuser')->with('success', 'Tài khoản người dùng đã được tạo thành công');
        }


        // Nếu là Admin thì tạo tài khoản trực tiếp
        if ($currentUser->role->name === 'admin') {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'id_role' => $request->id_role,
                'profile_image' => $profileImagePath,
                'status' => 'active',
                'is_lock' => 'active',
            ]);

            $user->address()->create([
                'address_detail' => $request->address_detail,
                'id_ward' => $request->ward_id,
                'is_default' => true,
            ]);

            $roleName = $user->role->name;

            if ($roleName == 'admin') {
                return redirect()->route('admin.users.index')->with('success', 'Tài khoản admin đã được tạo thành công');
            } elseif ($roleName == 'employee') {
                return redirect()->route('admin.users.listemployee')->with('success', 'Tài khoản nhân viên đã được tạo thành công');
            } elseif ($roleName == 'user') {
                return redirect()->route('admin.users.listuser')->with('success', 'Tài khoản người dùng đã được tạo thành công');
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được tạo thành công');
    }


    public function show($id)
    {
        $user = User::with('addresses.ward.district.province')->findOrFail($id);
        $currentUser = Auth::user();

        // Admin: không được xem admin khác
        if ($currentUser->role->name === 'admin') {
            if ($user->role->name === 'admin' && $currentUser->id !== $user->id) {
                return redirect()->back()->with('error', 'Bạn không có quyền xem thông tin của admin khác.');
            }

            $order = Order::where('id_user', $id)->with('orderDetails')->get();
            return view('admin.user.detail', compact('user', 'order'));
        }

        // Employee: chỉ được xem user thường
        if ($currentUser->role->name === 'employee') {
            if ($user->role->name === 'user' || $currentUser->id === $user->id) {
                $order = Order::where('id_user', $id)->with('orderDetails')->get();
                return view('admin.user.detail', compact('user', 'order'));
            } else {
                return redirect()->back()->with('error', 'Bạn không có quyền xem thông tin người này.');
            }
        }

        return redirect()->back()->with('error', 'Không xác định quyền truy cập.');
    }


    public function edit($id)
    {
        // Lấy thông tin người dùng cần chỉnh sửa
        $user = User::findOrFail($id);
        $roles = Role::all(); // Lấy tất cả các quyền (roles) để hiển thị trong dropdown
        $provinces = Province::all();
        $currentUser = Auth::user();

        if ($currentUser->role->name === 'admin') {
            if ($user->role->name === 'admin' && $currentUser->id !== $user->id) {
                return redirect()->back()->with('error', 'Bạn không có quyền sửa admin khác.');
            }
        }

        // Nhân viên chỉ được sửa user, không được sửa chính mình, admin hoặc nhân viên khác
        if ($currentUser->role->name === 'employee') {
            if (
                $currentUser->id === $user->id || // không được sửa chính mình
                $user->role->name !== 'user' // không được sửa admin hoặc nhân viên
            ) {
                return redirect()->back()->with('error', 'Bạn không có quyền sửa người này.');
            }
        }

        // Lấy thông tin quận/huyện và xã/phường của người dùng
        $districts = [];
        $wards = [];

        if ($user->address) {
            // Nếu người dùng đã có địa chỉ, lấy thông tin quận và xã tương ứng
            $districts = District::where('province_id', $user->address->ward->district->province->id)->get();
            $wards = Ward::where('district_id', $user->address->ward->district->id)->get();
        }
        // Trả về view với thông tin người dùng và danh sách quyền
        return view('admin.user.edit', compact('user', 'roles', 'provinces', 'districts', 'wards'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $id,
                'regex:/^[\w\.-]+@(fpt\.edu\.vn|gmail\.com)$/',
            ],
            'phone' => [
                'required',
                'unique:users,phone,' . $id,
                'regex:/^0(3|5|7|8|9)\d{8}$/', // Thêm dấu phân cách đúng
            ],
            'password' => 'nullable|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'address_detail' => 'nullable|string|max:255',
            'id_role' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên người dùng.',
            'name.unique' => 'Tên người dùng đã tồn tại.',

            'email.required' => 'Email không được trống',
            'email.regex' => 'Email không hợp lệ',
            'email.max' => 'Email không quá 255 ký tự',
            'email.unique' => 'Email đã có',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'phone.regex' => 'Số điện thoại không hợp lệ. Số điện thoại phải bắt đầu bằng 03, 05, 07, 08, 09 và có 10 chữ số.',

            'password.required' => 'Mật khẩu không được trống',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự',
            'password.regex' => 'Mật khẩu phải chứa ít nhất một chữ cái viết hoa, một chữ cái viết thường, một chữ số và một ký tự đặc biệt.',

            'province_id.required' => 'Vui lòng chọn Tỉnh / Thành phố.',
            'province_id.exists' => 'Tỉnh / Thành phố không hợp lệ.',

            'district_id.required' => 'Vui lòng chọn Quận / Huyện.',
            'district_id.exists' => 'Quận / Huyện không hợp lệ.',

            'ward_id.required' => 'Vui lòng chọn Xã / Phường.',
            'ward_id.exists' => 'Xã / Phường không hợp lệ.',

            'address_detail.required' => 'Vui lòng nhập địa chỉ chi tiết.',

            'id_role.required' => 'Vui lòng chọn quyền hạn.',
            'id_role.exists' => 'Quyền hạn không hợp lệ.',

            'profile_image.image' => 'Tệp tải lên phải là ảnh.',
            'profile_image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif,webp,svg.',
            'profile_image.max' => 'Kích thước ảnh tối đa là 2MB.',
        ]);
        // Cập nhật thông tin người dùng
        $user = User::findOrFail($id);
        // Xử lý ảnh hồ sơ (tùy chọn)
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            if ($file->isValid()) {
                // Xóa ảnh cũ nếu có
                if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
                    Storage::delete('public/' . $user->profile_image);
                }
                // Lưu ảnh mới
                $profileImagePath = $file->store('profile_image', 'public');
            } else {
                return back()->withErrors(['profile_image' => 'Ảnh không hợp lệ hoặc bị lỗi khi tải lên.'])->withInput();
            }
        }



        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'id_role' => $request->id_role,
            'profile_image' => $profileImagePath ?? $user->profile_image,
            'status' => 'active',
            'is_lock' => 'active',
        ]);

        // Kiểm tra nếu người dùng có địa chỉ
        if ($user->address) {
            // Nếu người dùng đã có địa chỉ, cập nhật địa chỉ chi tiết và các trường khác
            $user->address->update([
                'address_detail' => $request->address_detail,
                'id_ward' => $request->ward_id,
            ]);
        } else {
            // Nếu không có địa chỉ, tạo mới địa chỉ
            if ($request->address_detail && $request->ward_id) {
                $user->address()->create([
                    'address_detail' => $request->address_detail,
                    'id_ward' => $request->ward_id,
                    'is_default' => true,
                ]);
            }
        }

        // Kiểm tra quyền và điều hướng
        $roleName = $user->role->name;
        if ($roleName == 'admin') {
            return redirect()->route('admin.users.index')->with('success', 'Tài khoản admin đã được cập nhật thành công');
        } elseif ($roleName == 'employee') {
            return redirect()->route('admin.users.listemployee')->with('success', 'Tài khoản nhân viên đã được cập nhật thành công');
        } elseif ($roleName == 'user') {
            return redirect()->route('admin.users.listuser')->with('success', 'Tài khoản người dùng đã được cập nhật thành công');
        }

        return redirect()->route('admin.users.index')->with('success', 'Tài khoản đã được cập nhật thành công');
    }


    public function lock($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        // Không cho phép tự khóa chính mình
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'Bạn không thể khóa tài khoản của chính mình.');
        }

        $currentRole = $currentUser->role->name;
        $targetRole = $user->role->name;

        // Admin đang đăng nhập
        if ($currentRole === 'admin') {
            if ($targetRole === 'admin') {
                return redirect()->back()->with('error', 'Admin không thể khóa Admin khác.');
            } elseif ($targetRole === 'employee') {
                $user->is_lock = 'inactive';
                $user->status = 'inactive';
                $user->save();
                return redirect()->route('admin.users.listtkkhoa')->with('success', 'Đã khóa tài khoản Nhân viên.');
            } elseif ($targetRole === 'user') {
                $user->is_lock = 'inactive';
                $user->status = 'inactive';
                $user->save();
                return redirect()->route('admin.users.listtkkhoa')->with('success', 'Đã khóa tài khoản Người dùng.');
            }
        }

        if ($currentRole === 'employee') {
            if ($targetRole === 'admin') {
                return redirect()->back()->with('error', 'Bạn không có quyền khóa tài khoản Admin.');
            }

            if ($targetRole === 'employee') {
                return redirect()->back()->with('error', 'Bạn không có quyền khóa tài khoản Nhân viên khác.');
            }

            if ($targetRole === 'user') {
                // Khóa trực tiếp tài khoản user
                $user->is_lock = 'inactive';
                $user->status = 'inactive';
                $user->save();

                return redirect()->route('admin.users.listtkkhoa')->with('success', 'Đã khóa tài khoản Người dùng.');
            }
        }

        return redirect()->back()->with('success', 'Tài khoản đã bị khóa.');
    }
    public function opentk($id)
    {
        $user = User::findOrFail($id); // Người bị mở khóa
        $currentUser = Auth::user();   // Người đang đăng nhập

        // Không cho phép tự mở khóa chính mình viết để bảo mật =))
        if ($currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'Bạn không thể mở khóa tài khoản của chính mình.');
        }

        $currentRole = $currentUser->role->name;
        $targetRole = $user->role->name;

        if ($currentRole === 'admin') {
            if ($targetRole === 'admin') {
                return redirect()->back()->with('error', 'Admin không thể mở khóa tài khoản Admin khác.');
            }

            $user->is_lock = 'active';
            $user->status = 'active';
            $user->save();

            if ($targetRole === 'employee') {
                return redirect()->route('admin.users.listemployee')->with('success', 'Đã mở khóa tài khoản Nhân viên.');
            } elseif ($targetRole === 'user') {
                return redirect()->route('admin.users.listuser')->with('success', 'Đã mở khóa tài khoản Người dùng.');
            }
        }

        if ($currentRole === 'employee') {
            if ($targetRole === 'user') {
                $user->is_lock = 'active';
                $user->status = 'active';
                $user->save();

                return redirect()->route('admin.users.listuser')->with('success', 'Đã mở khóa tài khoản Người dùng.');
            } else {
                return redirect()->back()->with('error', 'Bạn không có quyền mở khóa tài khoản này.');
            }
        }

        return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
    }




    public function chart_user($id)
    {
        // tong chi tieu
        $totalSpent = Order::where('id_user', $id)
            ->where('payment_status', 'paid')
            ->sum('total');

        // tong don hang
        $totalOrders = Order::where('id_user', $id)->count();

        // list don hang
        $order = Order::where('id_user', $id)->with('orderDetails')->get();

        //list dia chi
        $user = User::with('addresses.ward.district.province')->find($id);
        return view('admin.user.chart_user', compact('user', 'order', 'totalSpent', 'totalOrders'));
    }

    public function charts(Request $request)
    {
        // Tổng số tài khoản đã đăng ký
        $totalUsers = User::count();

        // Tổng số tài khoản đang hoạt động
        $activeUsers = User::where('status', 'active')->count();

        // Tổng số tài khoản ngừng hoạt động
        $inactiveUsers = $totalUsers - $activeUsers;

        // Tính phần trăm tài khoản đang hoạt động
        $activePercentage = ($totalUsers > 0) ? ($activeUsers / $totalUsers) * 100 : 0;

        // Số tài khoản đăng ký trong tháng này
        $currentMonthCount = User::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Số tài khoản đăng ký trong tháng trước
        $previousMonthCount = User::whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        // Tính phần trăm tăng trưởng
        if ($previousMonthCount > 0) {
            $growthRate = (($currentMonthCount - $previousMonthCount) / $previousMonthCount) * 100;
        } else {
            $growthRate = $currentMonthCount > 0 ? 100 : 0; // Nếu tháng trước không có user nào thì tăng 100%
        }

        // Lấy danh sách người dùng đã từng mua hàng ít nhất 1 lần
        $usersWithOrders = User::whereHas('orders')->get();
        $totalUsersWithOrders = $usersWithOrders->count();

        // Lấy danh sách người dùng chưa từng mua hàng
        $usersWithoutOrders = User::whereDoesntHave('orders')->get();
        $totalUsersWithoutOrders = $usersWithoutOrders->count();

        // Lấy danh sách người dùng mua nhiều hơn 3 đơn hàng
        $usersWithMultipleOrders = User::whereHas('orders', function ($query) {
            $query->havingRaw('COUNT(*) > 3');
        })->get();
        $totalUsersWithMultipleOrders = $usersWithMultipleOrders->count();

        return view('admin.user.charts', compact(
            'currentMonthCount',
            'growthRate',
            'totalUsers',
            'activeUsers',
            'activePercentage',
            'inactiveUsers',
            'usersWithOrders',
            'totalUsersWithOrders',
            'totalUsersWithoutOrders',
            'totalUsersWithMultipleOrders',
            'usersWithoutOrders',
            'usersWithMultipleOrders'
        ));
    }

    public function getUserStats(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::now()->toDateString()); // Lấy ngày từ request, mặc định là hôm nay
        $date = Carbon::parse($selectedDate);

        // Thống kê theo ngày (7 ngày tính từ ngày đã chọn)
        $dailyUsers = User::whereBetween('created_at', [$date->copy()->subDays(6), $date])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->pluck('count', 'date');

        // Thống kê theo tuần (4 tuần tính từ tuần chứa ngày đã chọn)
        $weeklyUsers = User::whereBetween('created_at', [$date->copy()->subWeeks(3), $date])
            ->groupBy('week')
            ->orderBy('week', 'asc')
            ->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
            ->pluck('count', 'week');

        // Thống kê theo tháng (6 tháng tính từ tháng chứa ngày đã chọn)
        $monthlyUsers = User::whereBetween('created_at', [$date->copy()->subMonths(5), $date])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->pluck('count', 'month');

        // Thống kê theo năm (5 năm tính từ năm chứa ngày đã chọn)
        $yearlyUsers = User::whereBetween('created_at', [$date->copy()->subYears(4), $date])
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->pluck('count', 'year');

        return response()->json([
            'dailyUsers' => $dailyUsers,
            'weeklyUsers' => $weeklyUsers,
            'monthlyUsers' => $monthlyUsers,
            'yearlyUsers' => $yearlyUsers
        ]);
    }

    public function getUserLocationStats()
    {
        // Đếm số lượng tài khoản theo tỉnh/thành phố
        $locationStats = User::with('addresses.ward.district.province')
            ->get()
            ->pluck('addresses')
            ->flatten()
            ->pluck('ward.district.province.name')
            ->countBy();

        return response()->json($locationStats);
    }

    public function getTopSpenders()
    {
        $topUsers = Order::select('id_user', DB::raw('SUM(total) as total_spent'))
            ->groupBy('id_user')
            ->orderByDesc('total_spent')
            ->take(2)
            ->with(['user:id,name,email'])
            ->get();

        return response()->json($topUsers);
    }

    public function getOrderStatusStats($id)
    {
        $orderStats = Order::where('id_user', $id)
            ->selectRaw("
            SUM(CASE WHEN id_order_status = 5 THEN 1 ELSE 0 END) AS received_orders,
            SUM(CASE WHEN id_order_status = 6 THEN 1 ELSE 0 END) AS returned_orders,
            SUM(CASE WHEN id_order_status = 7 THEN 1 ELSE 0 END) AS cancelled_orders
        ")
            ->first();

        return response()->json($orderStats);
    }
}
