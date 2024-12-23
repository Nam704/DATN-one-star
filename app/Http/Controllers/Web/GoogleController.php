<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {


        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {

        try {
            $user = Socialite::driver('google')->user();

            $email = $user->getEmail();
            $name = $user->getName();
            $googleId = $user->getId();
            $avatar = $user->getAvatar();

            $finduser = User::where('google_id', $user->id)->first();
            if ($finduser) {
                Auth::login($finduser);
                return redirect()->intended('/');
            } else {
                if ($avatar) {
                    $filename = Str::random(20) . '.jpg';
                    $imageContent = file_get_contents($avatar);
                    Storage::put('public/avatars/' . $filename, $imageContent);
                    $profile_image = 'avatars/' . $filename;
                }
                $newUser = User::updateOrCreate(['email' => $email], [
                    'name' => $name,
                    'google_id' => $googleId,
                    'profile_image' => $profile_image,
                    'password' => encrypt('123456dummy'),
                    'id_role' => 1
                ]);
                Auth::login($newUser);
                return redirect()->intended('/');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
