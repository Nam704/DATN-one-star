<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permissions): Response
    {
        if (Auth::guest()) {
            return redirect()->route('auth.getFormLogin');
        }

        // Admin luôn có mọi quyền
        if (Auth::user()->role->name === 'admin') {
            return $next($request);
        }

        // Chuyển đổi chuỗi quyền thành mảng (nếu nhiều quyền được phân tách bằng dấu |)
        $permissions = explode('|', $permissions);

        // Kiểm tra xem người dùng có ít nhất một trong các quyền
        foreach ($permissions as $permission) {
            if (Auth::user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        // Lưu thông tin gỡ lỗi vào session nếu đang trong môi trường phát triển
        if (config('app.debug')) {
            session()->flash('permission_error', [
                'user' => Auth::user()->name,
                'role' => Auth::user()->role->name,
                'required_permissions' => $permissions,
                'user_permissions' => Auth::user()->role->permissions->pluck('name')->toArray()
            ]);
        }

        // Thay vì abort, redirect về dashboard với thông báo
        return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này!');
    }
}
