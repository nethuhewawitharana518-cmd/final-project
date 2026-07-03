<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->with('error', 'Your account has been suspended or is pending activation.');
            }

            if ($user->isAdmin()) {
                if ($user->email !== 'nthewawitharana@gmail.com') {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Unauthorized administrative credentials.');
                }
                return redirect()->route('admin.dashboard');
            } elseif ($user->isBusinessOwner()) {
                $business = $user->business;
                if ($business && ($business->isPending() || $business->isRejected())) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your business account registration is still under review or rejected.');
                }
                return redirect()->route('business.dashboard');
            } else {
                return redirect()->route('customer.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
