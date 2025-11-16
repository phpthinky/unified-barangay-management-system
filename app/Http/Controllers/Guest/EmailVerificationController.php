<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice page
     */
    public function notice()
    {
        $user = Auth::user();

        // If already verified, redirect to dashboard
        if ($user->hasVerifiedEmail()) {
            return $this->redirectToDashboard();
        }

        return view('guest.verify-email', compact('user'));
    }

    /**
     * Handle email verification with code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('info', 'Your email is already verified.');
        }

        // Check if exceeded attempts
        if ($user->hasExceededVerificationAttempts()) {
            return back()->with('error', 'Too many failed attempts. Please request a new verification code.');
        }

        // Check if code is expired
        if ($user->isVerificationCodeExpired()) {
            return back()->with('error', 'Verification code has expired. Please request a new code.');
        }

        // Verify the code
        if ($user->verifyEmailWithCode($request->verification_code)) {
            return redirect()->route('verification.success')
                ->with('success', '🎉 Email verified successfully! You can now access all services.');
        }

        // Failed verification
        $remainingAttempts = 5 - $user->email_verification_attempts;
        return back()
            ->with('error', "Invalid verification code. You have {$remainingAttempts} attempts remaining.")
            ->withInput();
    }

    /**
     * Resend verification code
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Your email is already verified.');
        }

        // Check rate limiting
        if (!$user->canResendVerificationCode()) {
            $seconds = $user->secondsUntilCanResend();
            return back()->with('error', "Please wait {$seconds} seconds before requesting another code.");
        }

        // Reset attempts if user requests new code
        $user->resetVerificationAttempts();

        // Send new code
        if ($user->sendEmailVerificationCode()) {
            return back()->with('success', '✅ New verification code sent to your email!');
        }

        return back()->with('error', 'Failed to send verification code. Please try again later.');
    }

    /**
     * Show success page after verification
     */
    public function success()
    {
        $user = Auth::user();

        // If not verified, redirect back to notice
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return view('guest.verification-success', compact('user'));
    }

    /**
     * Redirect to appropriate dashboard based on user role
     */
    private function redirectToDashboard()
    {
        $user = Auth::user();

        if ($user->hasRole('resident')) {
            return redirect()->route('resident.dashboard');
        }

        if ($user->hasRole('barangay-captain')) {
            return redirect()->route('captain.dashboard');
        }

        if ($user->hasAnyRole(['barangay-secretary', 'barangay-staff'])) {
            return redirect()->route('secretary.dashboard');
        }

        return redirect()->route('dashboard');
    }
}