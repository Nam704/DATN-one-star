<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserContronler extends Controller
{
    protected $user;
    function __construct(User $user)
    {
        $this->user = $user;
    }
    function listAdmin()
    {
        $users = $this->user->list();
        // dd($users);
        return view('admin.user.list', compact('users'));
    }

    function resetPassword($id)
    {
        $user = User::find($id);
    }
}
