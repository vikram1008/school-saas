<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->hasRole('Super Admin')) {
            return redirect()->route('superadmin.dashboard');
        }
        return view('superadmin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // STRICT CHECK: Is this user a SaaS Super Admin?
            if (Auth::user()->hasRole('Super Admin')) {
                return redirect()->intended(route('superadmin.dashboard'));
            }

            // Agar koi aur role wala (ya bina role wala) login kare toh kick out
            Auth::logout();
            return back()->withErrors([
                'email' => 'Access Denied: You are not authorized for HQ Access.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Invalid HQ credentials.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('superadmin.login');
    }
}