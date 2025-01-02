<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserContronler extends Controller
{
    function list()
    {
        return view('admin.user.demoDataTable');
    }
}
