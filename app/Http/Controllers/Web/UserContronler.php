<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
        $users = User::with('role')->get();
        return view('admin.user.list', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request) {}

    public function show($id)
    {
        $order = Order::where('id_user', $id)->with('orderDetails')->get();
        $user = User::with('addresses.ward.district.province')->find($id);
        return view('admin.user.detail', compact('user', 'order'));
    }

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}

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
}
