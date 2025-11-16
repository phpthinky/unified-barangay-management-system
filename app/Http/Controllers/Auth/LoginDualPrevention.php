<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginDualPrevention extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * ✅ Override: Check double login BEFORE attempting login
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        Log::info('Login attempt started', [
            'email' => $request->email,
        ]);

        // Get user
        $user = \App\Models\User::where($this->username(), $request->input($this->username()))->first();

        if ($user) {
            Log::info('User found', [
                'user_id' => $user->id, 
                'is_logged_in' => $user->is_logged_in,
                'last_activity' => $user->last_activity_at,
            ]);

            // ✅ CHECK: Is user already logged in?
            if ($user->is_logged_in && $user->session_token) {
                
                // Check if session is stale (> 10 minutes)
                $lastActivity = $user->last_activity_at;
                
                if ($lastActivity && now()->diffInMinutes($lastActivity) > 10) {
                    // Session is stale - clear it and allow login
                    Log::info('Stale session detected, clearing', [
                        'user_id' => $user->id,
                        'minutes_inactive' => now()->diffInMinutes($lastActivity),
                    ]);
                    
                    $user->update([
                        'is_logged_in' => false,
                        'session_token' => null,
                    ]);
                } else {
                    // Active session exists - block login
                    Log::warning('Double login prevented', [
                        'user_id' => $user->id,
                        'minutes_since_activity' => $lastActivity ? now()->diffInMinutes($lastActivity) : 'N/A',
                    ]);
                    
                    return redirect()->back()
                        ->withInput($request->only($this->username()))
                        ->withErrors([
                            $this->username() => 'This account is already logged in on another device. Please wait 10 minutes for automatic logout, or logout from the other session first.',
                        ]);
                }
            }

            // Check if user is inactive/archived
            if (!$user->is_active) {
                Log::warning('Login attempt for inactive user', ['user_id' => $user->id]);
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        $this->username() => 'Your account has been deactivated.',
                    ]);
            }

            if ($user->is_archived) {
                Log::warning('Login attempt for archived user', ['user_id' => $user->id]);
                return redirect()->back()
                    ->withInput($request->only($this->username()))
                    ->withErrors([
                        $this->username() => 'Your account has been archived.',
                    ]);
            }
        }

        // Attempt authentication
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * ✅ Override: Set session token after login
     */
    protected function authenticated(Request $request, $user)
    {
        // ✅ CHECK: Term expiration (for barangay officials)
        if ($user->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-treasurer', 'barangay-councilor', 'barangay-staff'])) {
            
            if ($user->term_end && now()->greaterThan($user->term_end)) {
                
                $user->update([
                    'is_archived' => true,
                    'is_active' => false,
                    'archived_at' => now(),
                    'archived_by' => null,
                ]);
                
                $this->guard()->logout();
                
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your term ended on ' . $user->term_end->format('F d, Y') . '. Your account has been archived.']);
            }
        }

        // ✅ Generate session token (ONLY ONCE HERE)
        $sessionToken = Str::random(60);
        
        // ✅ Update user
        $user->update([
            'session_token' => $sessionToken,
            'is_logged_in' => true,
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);

        // ✅ Store in session
        session([
            'user_session_token' => $sessionToken,
            'last_activity_time' => now()->timestamp,
        ]);

        Log::info('Login successful', [
            'user_id' => $user->id,
            'session_token_set' => true,
        ]);

        // ✅ Role-based redirection
        if ($user->hasRole('municipality-admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('abc-president')) {
            return redirect()->route('abc.dashboard');
        }
        
        if ($user->hasAnyRole(['barangay-captain', 'barangay-councilor', 'barangay-secretary', 'barangay-treasurer', 'barangay-staff'])) {
            return redirect()->route('barangay.dashboard');
        }
        
        if ($user->hasRole('lupon-member')) {
            return redirect()->route('lupon.dashboard');
        }
        
        if ($user->hasRole('resident')) {
            return redirect()->route('resident.dashboard');
        }

        return redirect('/');
    }

    /**
     * ✅ Logout and clear session
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        
        if ($user) {
            $user->update([
                'is_logged_in' => false,
                'session_token' => null,
                'last_activity_at' => now(),
            ]);

            Log::info('User logged out', ['user_id' => $user->id]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}