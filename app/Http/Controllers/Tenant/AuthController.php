<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Already logged in — redirect to dashboard
        if (Auth::guard('tenant')->check()) {
            return redirect()->route('tenant.dashboard');
        }

        $school = tenant();

        return view('tenant.auth.login', compact('school'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Check if account is active
        $user = \App\Models\TenantUser::where('email', $credentials['email'])->first();

        if ($user && !$user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account has been deactivated. Contact your school administrator.']);
        }

        if (Auth::guard('tenant')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('tenant')->user();

            // Role-based redirect
            return redirect()->intended(route($user->getDashboardRoute()));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}