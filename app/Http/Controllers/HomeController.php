<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirect users to their appropriate dashboard based on role.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Check if user is archived or inactive
        if ($user->is_archived) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been archived.');
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is currently inactive.');
        }

        // Update last login
        $user->updateLastLogin();

        // Redirect based on primary role
        if ($user->isMunicipalityAdmin()) {
            return redirect()->route('barangay.dashboard');
        }

        if ($user->isAbcPresident()) {
            return redirect()->route('abc.dashboard');
        }

        if ($user->isBarangayStaff()) {
            return redirect()->route('barangay.dashboard');
        }

        if ($user->isLupon()) {
            return redirect()->route('lupon.dashboard');
        }

        if ($user->isResident()) {
            // Check if resident profile exists
            if (!$user->residentProfile) {
                return redirect()->route('resident.profile.create')
                               ->with('info', 'Please complete your resident profile to continue.');
            }
            
            return redirect()->route('resident.dashboard');
        }
        return redirect()->route('guest.dashboard');

        // Default fallback
        return redirect()->route('home')->with('error', 'Unable to determine your dashboard. Please contact administrator.');
    }
    public function guestDashboard()
        {

        }
    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        if ($user->hasRole('resident')) {
            // code...
        return view('profile.resident', compact('user'));

        }
        if ($user->hasRole('lupon-member')) {
            // code...
        return view('lupon.profile', compact('user'));

        }

        else{
        return view('profile.show', compact('user'));

        }

    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix' => 'nullable|string|max:10',
            'contact_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only([
            'first_name', 'last_name', 'middle_name', 'suffix',
            'contact_number', 'birth_date', 'gender', 'address', 'email'
        ]));

        // Update the name field (for backwards compatibility)
        $user->update(['name' => $user->full_name]);

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(public_path('uploads/photos/' . $user->profile_photo))) {
                unlink(public_path('uploads/photos/' . $user->profile_photo));
            }

            // Upload new photo
            $file = $request->file('profile_photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/photos'), $filename);

            $user->update(['profile_photo' => $filename]);
        }

        return redirect()->route('profile')->with('success', 'Profile photo updated successfully.');
    }
    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile')->with('success', 'Password changed successfully.');
    }
}