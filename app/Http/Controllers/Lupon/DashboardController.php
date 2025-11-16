<?php

namespace App\Http\Controllers\Lupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\ComplaintHearing;
use App\Models\ComplaintType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')
                           ->with('error', 'No barangay assigned to your account.');
        }

        // Lupon member statistics
        $stats = [
            'assigned_complaints' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->count(),
            
            'active_complaints' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->whereIn('workflow_status', [
                    'for_lupon',
                    '1st_hearing_scheduled', '1st_hearing_ongoing', '1st_hearing_completed',
                    '2nd_hearing_scheduled', '2nd_hearing_ongoing', '2nd_hearing_completed',
                    '3rd_hearing_scheduled', '3rd_hearing_ongoing', '3rd_hearing_completed'
                ])
                ->count(),
            
            'resolved_complaints' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->where('workflow_status', 'resolved_by_lupon')
                ->count(),
            
            'hearings_conducted' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('presiding_officer', $user->id)
                ->where('status', 'completed')
                ->count(),
            
            'scheduled_hearings' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('status', 'scheduled')
                ->whereJsonContains('lupon_members', $user->id)
                ->count(),
        ];

        // Assigned complaints by priority
        $assignedComplaints = [
            'for_hearing' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->where('workflow_status', 'for_lupon')
                ->with(['complainant', 'complaintType'])
                ->latest()
                ->get(),
            
            'ongoing_hearings' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->whereIn('workflow_status', [
                    '1st_hearing_ongoing', '2nd_hearing_ongoing', '3rd_hearing_ongoing'
                ])
                ->with(['complainant', 'complaintType', 'latestHearing'])
                ->latest()
                ->get(),
            
            'completed_hearings' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->whereIn('workflow_status', [
                    '1st_hearing_completed', '2nd_hearing_completed', '3rd_hearing_completed'
                ])
                ->with(['complainant', 'complaintType'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        // Upcoming hearings (next 7 days)
        $upcomingHearings = ComplaintHearing::where('barangay_id', $barangay->id)
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now(), now()->addDays(7)])
            ->where(function($query) use ($user) {
                $query->where('presiding_officer', $user->id)
                    ->orWhereJsonContains('lupon_members', $user->id);
            })
            ->with(['complaint.complainant', 'complaint.complaintType'])
            ->orderBy('scheduled_date')
            ->get();

        // Today's hearings
        $todaysHearings = ComplaintHearing::where('barangay_id', $barangay->id)
            ->whereDate('scheduled_date', today())
            ->where(function($query) use ($user) {
                $query->where('presiding_officer', $user->id)
                    ->orWhereJsonContains('lupon_members', $user->id);
            })
            ->with(['complaint.complainant', 'complaint.complaintType'])
            ->orderBy('scheduled_date')
            ->get();

        // Recent activity
        $recentActivity = [
            'new_assignments' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->where('assigned_to_lupon_at', '>=', now()->subDays(7))
                ->with(['complainant', 'complaintType'])
                ->latest('assigned_to_lupon_at')
                ->get(),
            
            'completed_hearings' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('status', 'completed')
                ->where('actual_end_time', '>=', now()->subDays(7))
                ->where(function($query) use ($user) {
                    $query->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->with(['complaint.complainant', 'complaint.complaintType'])
                ->latest('actual_end_time')
                ->get(),
        ];

        // Performance metrics
        $performance = [
            'resolution_rate' => $this->getResolutionRate($user->id, $barangay->id),
            'avg_resolution_days' => $this->getAverageResolutionDays($user->id, $barangay->id),
            'successful_mediations' => $this->getSuccessfulMediationRate($user->id, $barangay->id),
            'hearing_attendance' => $this->getHearingAttendanceRate($user->id, $barangay->id),
        ];

        // Monthly workload (last 6 months)
        $monthlyWorkload = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $monthlyWorkload[] = [
                'month' => $date->format('M Y'),
                'new_complaints' => Complaint::where('barangay_id', $barangay->id)
                    ->where('assigned_lupon_id', $user->id)
                    ->whereYear('assigned_to_lupon_at', $date->year)
                    ->whereMonth('assigned_to_lupon_at', $date->month)
                    ->count(),
                'resolved' => Complaint::where('barangay_id', $barangay->id)
                    ->where('assigned_lupon_id', $user->id)
                    ->where('workflow_status', 'resolved_by_lupon')
                    ->whereYear('lupon_resolved_at', $date->year)
                    ->whereMonth('lupon_resolved_at', $date->month)
                    ->count(),
                'hearings' => ComplaintHearing::where('barangay_id', $barangay->id)
                    ->whereYear('scheduled_date', $date->year)
                    ->whereMonth('scheduled_date', $date->month)
                    ->where(function($query) use ($user) {
                        $query->where('presiding_officer', $user->id)
                            ->orWhereJsonContains('lupon_members', $user->id);
                    })
                    ->count(),
            ];
        }

        // Complaint types handled
        $complaintTypes = ComplaintType::whereHas('complaints', function($query) use ($user, $barangay) {
                $query->where('barangay_id', $barangay->id)
                    ->where('assigned_lupon_id', $user->id);
            })
            ->withCount(['complaints as handled_count' => function($query) use ($user, $barangay) {
                $query->where('barangay_id', $barangay->id)
                    ->where('assigned_lupon_id', $user->id);
            }])
            ->orderByDesc('handled_count')
            ->take(5)
            ->get();

        // Action items needing attention
        $actionItems = $this->getActionItems($user, $barangay->id);

        return view('lupon.dashboard', compact(
            'user',
            'barangay',
            'stats',
            'assignedComplaints',
            'upcomingHearings',
            'todaysHearings',
            'recentActivity',
            'performance',
            'monthlyWorkload',
            'complaintTypes',
            'actionItems'
        ));
    }

    private function getResolutionRate($userId, $barangayId)
    {
        $total = Complaint::where('barangay_id', $barangayId)
            ->where('assigned_lupon_id', $userId)
            ->count();
        
        $resolved = Complaint::where('barangay_id', $barangayId)
            ->where('assigned_lupon_id', $userId)
            ->where('workflow_status', 'resolved_by_lupon')
            ->count();
        
        return $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
    }

    private function getAverageResolutionDays($userId, $barangayId)
    {
        $resolvedComplaints = Complaint::where('barangay_id', $barangayId)
            ->where('assigned_lupon_id', $userId)
            ->where('workflow_status', 'resolved_by_lupon')
            ->whereNotNull('lupon_resolved_at')
            ->whereNotNull('assigned_to_lupon_at')
            ->get();

        if ($resolvedComplaints->isEmpty()) return 0;

        $totalDays = 0;
        foreach ($resolvedComplaints as $complaint) {
            $totalDays += $complaint->assigned_to_lupon_at->diffInDays($complaint->lupon_resolved_at);
        }

        return round($totalDays / $resolvedComplaints->count(), 1);
    }

    private function getSuccessfulMediationRate($userId, $barangayId)
    {
        $totalHearings = ComplaintHearing::where('barangay_id', $barangayId)
            ->where('presiding_officer', $userId)
            ->where('status', 'completed')
            ->count();

        $successfulMediations = ComplaintHearing::where('barangay_id', $barangayId)
            ->where('presiding_officer', $userId)
            ->where('status', 'completed')
            ->whereIn('outcome', ['settled', 'mediated'])
            ->count();

        return $totalHearings > 0 ? round(($successfulMediations / $totalHearings) * 100, 1) : 0;
    }

    private function getHearingAttendanceRate($userId, $barangayId)
    {
        $totalScheduled = ComplaintHearing::where('barangay_id', $barangayId)
            ->where(function($query) use ($userId) {
                $query->where('presiding_officer', $userId)
                    ->orWhereJsonContains('lupon_members', $userId);
            })
            ->where('scheduled_date', '<', now())
            ->count();

        $attended = ComplaintHearing::where('barangay_id', $barangayId)
            ->where(function($query) use ($userId) {
                $query->where('presiding_officer', $userId)
                    ->orWhereJsonContains('lupon_members', $userId);
            })
            ->where('scheduled_date', '<', now())
            ->whereIn('status', ['completed', 'ongoing'])
            ->count();

        return $totalScheduled > 0 ? round(($attended / $totalScheduled) * 100, 1) : 100;
    }

    private function getActionItems($user, $barangayId)
    {
        $items = [];

        // Complaints awaiting hearing schedule
        $awaitingHearing = Complaint::where('barangay_id', $barangayId)
            ->where('assigned_lupon_id', $user->id)
            ->where('workflow_status', 'for_lupon')
            ->count();
        
        if ($awaitingHearing > 0) {
            $items[] = [
                'type' => 'warning',
                'title' => 'Schedule Hearings',
                'message' => "{$awaitingHearing} complaints need hearing schedule",
                'action' => route('lupon.complaints.index', ['status' => 'for_lupon']),
                'icon' => 'calendar'
            ];
        }

        // Hearings needing preparation
        $upcomingHearings = ComplaintHearing::where('barangay_id', $barangayId)
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now(), now()->addDays(3)])
            ->where(function($query) use ($user) {
                $query->where('presiding_officer', $user->id)
                    ->orWhereJsonContains('lupon_members', $user->id);
            })
            ->count();
        
        if ($upcomingHearings > 0) {
            $items[] = [
                'type' => 'info',
                'title' => 'Prepare for Hearings',
                'message' => "{$upcomingHearings} hearings scheduled within 3 days",
                'action' => route('lupon.hearings.index', ['upcoming' => true]),
                'icon' => 'clock'
            ];
        }

        // Completed hearings needing decision
        $needDecision = Complaint::where('barangay_id', $barangayId)
            ->where('assigned_lupon_id', $user->id)
            ->whereIn('workflow_status', [
                '1st_hearing_completed', '2nd_hearing_completed', '3rd_hearing_completed'
            ])
            ->count();
        
        if ($needDecision > 0) {
            $items[] = [
                'type' => 'danger',
                'title' => 'Decision Required',
                'message' => "{$needDecision} completed hearings need final decision",
                'action' => route('lupon.complaints.index', ['needs_decision' => true]),
                'icon' => 'alert-triangle'
            ];
        }

        // Hearings pending documentation
        $pendingDocs = ComplaintHearing::where('barangay_id', $barangayId)
            ->where('status', 'completed')
            ->whereNull('minutes')
            ->where('actual_end_time', '<', now()->subHours(24))
            ->where(function($query) use ($user) {
                $query->where('presiding_officer', $user->id)
                    ->orWhereJsonContains('lupon_members', $user->id);
            })
            ->count();
        
        if ($pendingDocs > 0) {
            $items[] = [
                'type' => 'warning',
                'title' => 'Complete Documentation',
                'message' => "{$pendingDocs} completed hearings need minutes",
                'action' => route('lupon.hearings.index', ['pending_docs' => true]),
                'icon' => 'file-text'
            ];
        }

        return $items;
    }

    public function profile()
    {
        $user = Auth::user();
            // code...
        return view('lupon.profile', compact('user'));

        

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