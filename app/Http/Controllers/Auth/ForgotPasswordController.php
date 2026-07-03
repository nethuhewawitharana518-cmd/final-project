<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Show request link form.
     */
    public function show()
    {
        return view('auth.forgot-password');
    }

    /**
     * Generate OTP and send email.
     */
    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = (string) rand(100000, 999999);

        // Store OTP in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email'      => $request->email,
                'token'      => $otp,
                'created_at' => now()
            ]
        );

        // Keep email in session to verify OTP
        session(['reset_email' => $request->email]);

        try {
            Mail::to($request->email)->send(new ForgotPasswordOtpMail($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ForgotPassword OTP mail error: ' . $e->getMessage());
            return back()->with('error', 'Unable to send email. Please verify mail settings in .env file.');
        }

        return redirect()->route('password.otp.show')->with('success', 'A verification OTP code has been successfully generated and sent to your email.');
    }

    /**
     * Show OTP verification form.
     */
    public function showOtpForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.forgot-password-otp');
    }

    /**
     * Verify the inputted OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $request->otp)
            ->first();

        // Expire after 15 minutes
        if (!$tokenData || now()->diffInMinutes(\Carbon\Carbon::parse($tokenData->created_at)) > 15) {
            return back()->with('error', 'Invalid or expired OTP code.');
        }

        // Grant access to password reset
        session(['otp_verified' => true]);

        return redirect()->route('password.reset.otp')->with('success', 'OTP code verified successfully. Please choose a new password.');
    }

    /**
     * Show reset password form.
     */
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password-otp');
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        if (!session('otp_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Cleanup reset database token and session
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now login with your new password.');
    }
}
