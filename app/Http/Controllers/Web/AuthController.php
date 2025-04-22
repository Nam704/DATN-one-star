<?php

namespace App\Http\Controllers\Web;

use App\Events\Register;
use App\Events\ResetPassword;
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
use App\Events\UserLogin;
use App\Models\Password_reset_token;
use App\Services\CartService;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function getFormLogin()
    {
        return view('admin.auth.login');
    }
    function getProfileAdmin()
    {
        $user = auth()->user();
        $notifications = $user->notifications->sortByDesc('created_at');
        $productAudits = $user->productAudits->sortByDesc('created_at');
        return view('admin.auth.profile_admin', compact('user', 'notifications', 'productAudits'));
    }
    function editProfileAdmin(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->input('name');
        $user->save();
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
    // public function login(AuthRequest $request)
    // {
    //     try {
    //         $credentials = $request->only('email', 'password');

    //         $user = User::where('email', $credentials['email'])->first();

    //         if ($user && $user->is_lock) {
    //             return redirect()->back()->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.');
    //         }

    //         if (Auth::attempt($credentials, $request->filled('remember'))) {
    //             $user = Auth::user();

    //             //khóa tk
    //             if ($user->is_lock) {
    //                 Auth::logout();
    //                 return redirect()->route('auth.getFormLogin')->withErrors([
    //                     'email' => 'Tài khoản của bạn đã bị khóa.'
    //                 ]);
    //             }


    //             if ($user->isAdmin()) {
    //                 return redirect()->route('admin.dashboard');
    //             } elseif ($user->isUser()) {
    //                 return redirect()->route('client.home');
    //             } elseif ($user->isEmployee()) {
    //                 return redirect()->route('admin.dashboard');
    //             }
    //         } else {
    //             // return "out auth attempt";
    //             return redirect()->back()->with('error', 'Không thể đăng nhập, vui lòng kiểm tra lại email và mật khẩu.');
    //         }

    //         return redirect()->back()->with('error', 'Không thể đăng nhập, vui lòng kiểm tra email và mật khẩu.');
    //     } catch (\Exception $e) {
    //         // Handle the exception
    //         // return "in try catch";
    //         return redirect()->back()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập.');
    //     }
    // }
    public function login(AuthRequest $request)
    {

        try {

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->filled('remember'))) {

                $user = Auth::user();

                //khóa tk
                if ($user->is_lock === 1) {
                    Auth::logout();
                    return redirect()->route('auth.getFormLogin')->withErrors([
                        'email' => 'Tài khoản của bạn đã bị khóa.'
                    ]);
                }
                

                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard'); // Admin dashboard
                } elseif ($user->isUser()) {
                    // return "go to user dashboard";
                    return redirect()->route('client.home'); // Admin dashboard

                    // return redirect()->route('user.dashboard'); // User dashboard
                } elseif ($user->isEmployee()) {
                    return redirect()->route('admin.dashboard'); // Admin dashboard

                    // return "go to employee dashboard";
                    // return redirect()->route('employee.dashboard'); // Client dashboard
                }
            } else {
                // return "out auth attempt";
                return redirect()->back()->with('error', 'Không thể đăng nhập, vui lòng kiểm tra lại email và mật khẩu.');
            }
        } catch (\Exception $e) {
            // Handle the exception
            // return "in try catch";
            return redirect()->back()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập.');
        }
    }
    function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
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

        Register::dispatch($user);

        Auth::login($user);
        $this->cartService->store($user->id);
        return redirect()->route('auth.login')->with('success', 'Registration successful! Please login.');
    }
    function  getFormForgotPassword()
    {
        return view('admin.auth.forgotpw');
    }
    function getfromResetPassword($id, $token)
    {
        $user = User::find($id);

        // dd($user->email);
        $password_reset = Password_reset_token::where('email', $user->email)->first();
        // dd($password_reset);
        if (!$password_reset) {
            return redirect()->back()->with('error', 'Email not found');
        }
        return view('admin.auth.change-pw', compact('password_reset', 'token'));
    }
    public function sendPasswordResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email not found');
        }

        $password_reset_token_plaintext = Str::random(32); // Token plaintext
        $password_reset_token_hashed = Hash::make($password_reset_token_plaintext);
        $expires_at = Carbon::now()->addMinutes(10);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $password_reset_token_hashed, 'created_at' => now(), 'expires_at' => $expires_at]
        );

        // Send email
        $data = [
            'user' => $user,
            'token' => $password_reset_token_plaintext,

        ];
        ResetPassword::dispatch($data);

        return redirect()->back()->with('success', 'Password reset
        email sent. Please check your email.');
    }
    public function resetPassword(PasswordResetRequest $request)
    {
        $request->validated();
        // dd($request->password_reset_token);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        // dd($record);
        if (
            !$record ||
            !Hash::check($request->password_reset_token, $record->token) ||
            Carbon::parse($record->expires_at)->isPast()
        ) {
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
