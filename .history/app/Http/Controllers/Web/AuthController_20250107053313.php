<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\ClientRegisterRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Mail\ForgotPasswordMail;
use App\Mail\RegisterMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function getFormLogin()
    {
        return view('admin.auth.login');
    }
    function getProfileAdmin()
    {
        $user = Auth::user();
        return view('admin.auth.profile_admin', compact('user'));
    }
    public function login(AuthRequest $request)
    {

        try {

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->filled('remember'))) {

                // dd($request);
                $user = Auth::user();
                // dd($user);
                if ($user->role->name === 'admin') {
                    return redirect()->route('admin.dashboard'); // Admin dashboard
                } elseif ($user->role->name === 'user') {
                    return "go to user dashboard";
                    // return redirect()->route('user.dashboard'); // User dashboard
                } elseif ($user->role->name === 'employee') {
                    return "go to employee dashboard";
                    // return redirect()->route('employee.dashboard'); // Client dashboard
                }
            } else {
                // return "out auth attempt";
                return redirect()->back()->with('error', 'Cannot log in, please check your email and password again.');
            }
        } catch (\Exception $e) {
            // Handle the exception
            // return "in try catch";
            return redirect()->back()->with('error', 'An error occurred during login.');
        }
    }
    function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login');
    }
    public function getFormRegister()
    {
        return view('admin.auth.register');
    }


    public function register(ClientRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'id_role' => 3 // Regular user role
        ]);

        Mail::to($user->email)->send(new RegisterMail($user));

        Auth::login($user);

        return redirect()->route('auth.login')->with('success', 'Registration successful! Please login.');
    }
    function  getFormForgotPassword()
    {
        return view('admin.auth.forgotpw');
    }
    function getfromResetPassword($token)
    {
        return view('admin.auth.change-pw', compact('token'));
    }
    public function sendPasswordResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email not found');
        }

        $password_reset_token = bin2hex(random_bytes(16));
        $expires_at = Carbon::now()->addMinutes(10);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $password_reset_token, 'created_at' => now(), 'expires_at' => $expires_at]
        );

        Mail::to($user->email)->send(new ForgotPasswordMail($password_reset_token));

        return redirect()->back()->with('success', 'Password reset
        email sent. Please check your email.');
    }
    public function resetPassword(PasswordResetRequest $request)
    {
        $request->validated();


        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        // dd($record);
        if (!$record || $record->token !== $request->password_reset_token || Carbon::parse($record->expires_at)->isPast()) {
            return redirect()->back()->with('error', 'Invalid password reset token');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email not found');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('auth.login')->with('success', 'Password reset successful. Please login with your new password.');
    }
}
