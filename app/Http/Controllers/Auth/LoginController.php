<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * ✅ Simple login - No double-login check
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

        // ✅ CHECK: User is active
        if (!$user->is_active) {
            $this->guard()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // ✅ CHECK: User is not archived
        if ($user->is_archived) {
            $this->guard()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been archived.']);
        }

        // ✅ Generate simple session token (for tracking only)
        $sessionToken = Str::random(60);
        
        // ✅ Update user - just for activity tracking
        $user->update([
            'session_token' => $sessionToken,
            'is_logged_in' => true,
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);

        // ✅ Store in session for activity tracking
        session([
            'user_session_token' => $sessionToken,
            'last_activity_time' => now()->timestamp,
        ]);

        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
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
     * ✅ Logout - clear session
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