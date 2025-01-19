<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Kiểm tra người dùng đã đăng nhập
        if (!auth()->check()) {
            return redirect()->route('auth.getFormLogin'); // Chuyển hướng nếu chưa đăng nhập
        }

        $userRole = auth()->user()->role->name; // Lấy vai trò người dùng

        // Admin luôn có quyền truy cập
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Kiểm tra nếu vai trò người dùng có trong danh sách vai trò được phép
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized action.');
            // Không cho phép truy cập
        }

        return $next($request);
    }
}