<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\ComplaintHearing;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the barangay dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')
                           ->with('error', 'No barangay assigned to your account.');
        }

        // Barangay statistics
        $stats = [
            'total_residents'     => ResidentProfile::byBarangay($barangay->id)->count(),
            'verified_residents'  => ResidentProfile::byBarangay($barangay->id)->verified()->count(),
            'pending_residents'   => ResidentProfile::byBarangay($barangay->id)->unverified()->count(),
            'document_requests'   => DocumentRequest::byBarangay($barangay->id)->count(),
            'pending_documents'   => DocumentRequest::byBarangay($barangay->id)->where('status', 'pending')->count(),
            'active_complaints'   => Complaint::where('barangay_id', $barangay->id)->pending()->count(),
            'complaints'          => Complaint::where('barangay_id', $barangay->id)->count(),
            'business_permits'    => BusinessPermit::byBarangay($barangay->id)->count(),
            'active_permits'      => BusinessPermit::byBarangay($barangay->id)->where('status', 'approved')->count(),
        ];

        // Tasks requiring attention
        $pendingTasks = [
            'pending_residents' => ResidentProfile::byBarangay($barangay->id)
                                                ->unverified()
                                                ->with('user')
                                                ->latest()
                                                ->take(5)
                                                ->get(),

            'pending_documents' => DocumentRequest::byBarangay($barangay->id)
                                                ->where('status', 'pending')
                                                ->with(['user', 'documentType'])
                                                ->latest()
                                                ->take(5)
                                                ->get(),

            'urgent_complaints' => Complaint::where('barangay_id', $barangay->id)
                                          ->where('priority', 'urgent')
                                          ->pending()
                                          ->with('complainant', 'complaintType')
                                          ->latest()
                                          ->take(5)
                                          ->get(),

            'assigned_complaints' => Complaint::where('barangay_id', $barangay->id)
                                            ->assignedTo($user->id)
                                            ->pending()
                                            ->with('complainant', 'complaintType')
                                            ->latest()
                                            ->take(5)
                                            ->get(),
        ];

        // Upcoming hearings (if ComplaintHearing exists)
        $upcomingHearings = [];
        if (class_exists('App\Models\ComplaintHearing')) {
            try {
                $upcomingHearings = ComplaintHearing::where('barangay_id', $barangay->id)
                                              ->where('status', 'scheduled')
                                              ->where('scheduled_date', '>=', now())
                                              ->where('scheduled_date', '<=', now()->addDays(7))
                                              ->with(['complaint.complainant', 'complaint.complaintType', 'presidingOfficer'])
                                              ->orderBy('scheduled_date')
                                              ->take(5)
                                              ->get();
            } catch (\Exception $e) {
                // Skip if table doesn't exist yet
            }
        }

        // Monthly activity
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);

            $monthlyData[] = [
                'month'      => $date->format('M Y'),
                'residents'  => ResidentProfile::byBarangay($barangay->id)
                                              ->whereYear('created_at', $date->year)
                                              ->whereMonth('created_at', $date->month)
                                              ->count(),
                'documents'  => DocumentRequest::byBarangay($barangay->id)
                                              ->whereYear('submitted_at', $date->year)
                                              ->whereMonth('submitted_at', $date->month)
                                              ->count(),
                'complaints' => Complaint::where('barangay_id', $barangay->id)
                                         ->whereYear('received_at', $date->year)
                                         ->whereMonth('received_at', $date->month)
                                         ->count(),
                'permits'    => BusinessPermit::byBarangay($barangay->id)
                                              ->whereYear('created_at', $date->year)
                                              ->whereMonth('created_at', $date->month)
                                              ->count(),
            ];
        }

        // Recent activity
        $recentActivity = [
            'documents' => DocumentRequest::byBarangay($barangay->id)
                                        ->with(['user', 'documentType', 'processor'])
                                        ->latest()
                                        ->take(3)
                                        ->get(),

            'complaints' => Complaint::where('barangay_id', $barangay->id)
                                    ->with('complainant', 'complaintType', 'assignedOfficial')
                                    ->latest()
                                    ->take(3)
                                    ->get(),

            'permits' => BusinessPermit::byBarangay($barangay->id)
                                      ->with(['applicant', 'businessPermitType', 'processor'])
                                      ->latest()
                                      ->take(3)
                                      ->get(),
        ];

        // Performance metrics
        $performance = [
            'document_processing_avg'  => $this->getAverageProcessingDays('documents', $barangay->id),
            'complaint_resolution_avg' => $this->getAverageProcessingDays('complaints', $barangay->id),
            'permit_processing_avg'    => $this->getAverageProcessingDays('permits', $barangay->id),
        ];

        // Alerts
        $alerts = $this->getBarangayAlerts($barangay->id);

        // User-specific stats
        $userStats = [
            'documents_processed' => DocumentRequest::where('processed_by', $user->id)->count(),
            'complaints_handled'  => Complaint::assignedTo($user->id)->count(),
            'permits_processed'   => BusinessPermit::where('processed_by', $user->id)->count(),
        ];

        return view('barangay.dashboard', compact(
            'barangay',
            'stats',
            'pendingTasks',
            'upcomingHearings',
            'monthlyData',
            'recentActivity',
            'performance',
            'alerts',
            'userStats'
        ));
    }

    /**
     * Get barangay-specific alerts.
     */
    private function getBarangayAlerts($barangayId)
    {
        $alerts = [];

        // Pending residents
        $pendingResidents = ResidentProfile::byBarangay($barangayId)->unverified()->count();
        if ($pendingResidents > 0) {
            $alerts[] = [
                'type'    => 'warning',
                'title'   => 'Pending Verifications',
                'message' => "{$pendingResidents} residents need verification",
                'action'  => route('barangay.residents.index', ['status' => 'pending']),
                'icon'    => 'users',
            ];
        }

        // Overdue documents
        $overdueDocuments = DocumentRequest::byBarangay($barangayId)
                                         ->where('status', 'pending')
                                         ->where('submitted_at', '<', now()->subDays(5))
                                         ->count();
        if ($overdueDocuments > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'title'   => 'Overdue Documents',
                'message' => "{$overdueDocuments} requests are overdue",
                'action'  => route('barangay.documents.index', ['overdue' => true]),
                'icon'    => 'file-text',
            ];
        }

        // Urgent complaints
        $urgentComplaints = Complaint::where('barangay_id', $barangayId)
                                  ->where('priority', 'urgent')
                                  ->pending()
                                  ->count();
        if ($urgentComplaints > 0) {
            $alerts[] = [
                'type'    => 'danger',
                'title'   => 'Urgent Complaints',
                'message' => "{$urgentComplaints} urgent complaints need attention",
                'action'  => route('barangay.complaints.index', ['priority' => 'urgent']),
                'icon'    => 'alert-triangle',
            ];
        }

        // Upcoming hearings (if model exists)
        if (class_exists('App\Models\ComplaintHearing')) {
            try {
                $upcomingHearings = ComplaintHearing::where('barangay_id', $barangayId)
                                                  ->where('status', 'scheduled')
                                                  ->whereBetween('scheduled_date', [now(), now()->addDays(3)])
                                                  ->count();
                if ($upcomingHearings > 0) {
                    $alerts[] = [
                        'type'    => 'info',
                        'title'   => 'Upcoming Hearings',
                        'message' => "{$upcomingHearings} hearings in the next 3 days",
                        'action'  => '#',
                        'icon'    => 'calendar',
                    ];
                }
            } catch (\Exception $e) {
                // Skip if table doesn't exist
            }
        }

        // Expiring permits (check if scope exists)
        try {
            $expiringPermits = BusinessPermit::byBarangay($barangayId)
                                           ->where('status', 'approved')
                                           ->whereNotNull('expires_at')
                                           ->where('expires_at', '<=', now()->addDays(30))
                                           ->where('expires_at', '>', now())
                                           ->count();
            if ($expiringPermits > 0) {
                $alerts[] = [
                    'type'    => 'warning',
                    'title'   => 'Expiring Permits',
                    'message' => "{$expiringPermits} permits expire within 30 days",
                    'action'  => route('barangay.permits.index', ['expiring' => true]),
                    'icon'    => 'briefcase',
                ];
            }
        } catch (\Exception $e) {
            // Skip if scope doesn't exist
        }

        return $alerts;
    }

    /**
     * Get average processing days.
     */
    private function getAverageProcessingDays($type, $barangayId)
    {
        switch ($type) {
            case 'documents':
                $completed = DocumentRequest::byBarangay($barangayId)
                                          ->whereNotNull('approved_at')
                                          ->where('approved_at', '>=', now()->subMonths(3))
                                          ->get();

                if ($completed->isEmpty()) return 0;

                $totalDays = 0;
                foreach ($completed as $request) {
                    $totalDays += $request->submitted_at->diffInDays($request->approved_at);
                }
                return round($totalDays / $completed->count(), 1);

            case 'complaints':
                $resolved = Complaint::where('barangay_id', $barangayId)
                                   ->whereNotNull('resolved_at')
                                   ->where('resolved_at', '>=', now()->subMonths(3))
                                   ->get();

                if ($resolved->isEmpty()) return 0;

                $totalDays = 0;
                foreach ($resolved as $complaint) {
                    $totalDays += $complaint->received_at->diffInDays($complaint->resolved_at);
                }
                return round($totalDays / $resolved->count(), 1);

            case 'permits':
                $approved = BusinessPermit::byBarangay($barangayId)
                                        ->whereNotNull('approved_at')
                                        ->where('approved_at', '>=', now()->subMonths(3))
                                        ->get();

                if ($approved->isEmpty()) return 0;

                $totalDays = 0;
                foreach ($approved as $permit) {
                    if ($permit->created_at && $permit->approved_at) {
                        $totalDays += $permit->created_at->diffInDays($permit->approved_at);
                    }
                }
                return $approved->count() > 0 ? round($totalDays / $approved->count(), 1) : 0;

            default:
                return 0;
        }
    }



    public function profile()
    {
        $user = Auth::user();
            // code...
        return view('barangay.profile', compact('user'));

        

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

        return redirect()->route('barangay.profile')->with('success', 'Password changed successfully.');
    }
}