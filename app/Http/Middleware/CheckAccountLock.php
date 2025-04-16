<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountLock
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_lock) {
            Auth::logout();
            return redirect()->route('auth.getFormLogin')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
        }

        return $next($request);
    }
}
