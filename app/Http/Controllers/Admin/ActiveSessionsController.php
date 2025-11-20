<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActiveSessionsController extends Controller
{
    /**
     * Display all active sessions across the system
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $role = $request->get('role');
        $barangay = $request->get('barangay');
        $search = $request->get('search');

        // Build query
        $query = User::where('is_logged_in', true)
                    ->where('is_active', true)
                    ->where('is_archived', false)
                    ->with(['barangay', 'roles']);

        // Apply filters
        if ($role) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($barangay) {
            $query->where('barangay_id', $barangay);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $activeSessions = $query->orderBy('last_activity_at', 'desc')
                               ->paginate(20);

        // Get statistics
        $stats = [
            'total_active' => User::where('is_logged_in', true)->count(),
            'residents' => User::where('is_logged_in', true)
                              ->whereHas('roles', fn($q) => $q->where('name', 'resident'))
                              ->count(),
            'officials' => User::where('is_logged_in', true)
                              ->whereHas('roles', fn($q) => $q->whereIn('name', [
                                  'barangay-captain', 'barangay-secretary', 
                                  'barangay-treasurer', 'barangay-councilor', 'barangay-staff'
                              ]))
                              ->count(),
            'admins' => User::where('is_logged_in', true)
                           ->whereHas('roles', fn($q) => $q->whereIn('name', [
                               'municipality-admin', 'abc-president'
                           ]))
                           ->count(),
        ];

        // Get barangays for filter
        $barangays = \App\Models\Barangay::orderBy('name')->get();

        return view('admin.active-sessions.index', compact('activeSessions', 'stats', 'barangays'));
    }

    /**
     * Force logout a user
     */
    public function forceLogout(User $user)
    {
        // Check permission (ABC President)
        if (!auth()->user()->hasRole('abc-president')) {
            abort(403, 'Unauthorized action.');
        }

        $user->update([
            'session_token' => null,
            'is_logged_in' => false,
        ]);

        // Log the action
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log('Force logged out user from admin panel');

        return redirect()->back()
            ->with('success', "User {$user->name} has been logged out successfully.");
    }

    /**
     * Force logout multiple users
     */
    public function forceLogoutMultiple(Request $request)
    {
        // Check permission (ABC President)
        if (!auth()->user()->hasRole('abc-president')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            $user->update([
                'session_token' => null,
                'is_logged_in' => false,
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Force logged out ' . count($users) . ' users from admin panel');

        return redirect()->back()
            ->with('success', count($users) . " users have been logged out successfully.");
    }

    /**
     * Clear all inactive sessions (no activity for X minutes)
     */
    public function clearInactive(Request $request)
    {
        // Check permission (ABC President)
        if (!auth()->user()->hasRole('abc-president')) {
            abort(403, 'Unauthorized action.');
        }

        $minutes = $request->get('minutes', 30);

        $inactiveUsers = User::where('is_logged_in', true)
                            ->where('last_activity_at', '<', now()->subMinutes($minutes))
                            ->get();

        foreach ($inactiveUsers as $user) {
            $user->update([
                'session_token' => null,
                'is_logged_in' => false,
            ]);
        }

        activity()
            ->causedBy(auth()->user())
            ->log('Cleared ' . $inactiveUsers->count() . ' inactive sessions');

        return redirect()->back()
            ->with('success', $inactiveUsers->count() . " inactive sessions have been cleared.");
    }
}