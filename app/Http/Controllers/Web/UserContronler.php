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
        $userCurrent = auth()->user();

        if ($userCurrent->isAdmin()) {
            $users = $this->user->list();
        } elseif ($userCurrent->isEmployee()) {
            $users = $this->user->list()->whereIn('role_name', ['employee', 'user']);
        } else {
            return redirect()->route('auth.getFormLogin');
        }

        return view('admin.user.list', compact('users'));
    }


    function resetPassword($id)
    {
        $user = User::find($id);
    }
}
