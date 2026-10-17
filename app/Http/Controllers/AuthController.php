<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'メールアドレスまたはパスワードが正しくありません．'
            ]);
        }
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showSignupForm() {
        return view('signup');
    }

    public function signup(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email'=>'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $user = User::create($validated);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/');
    }
}
