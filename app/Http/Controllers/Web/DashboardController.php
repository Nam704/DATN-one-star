<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (auth()->check()) {
            return view('admin.index');
        } else {
            return redirect()->route('auth.getFormLogin');
        }
    }
}
