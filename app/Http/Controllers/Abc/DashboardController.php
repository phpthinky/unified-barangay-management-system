<?php

namespace App\Http\Controllers\Abc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Barangay;
use App\Models\BarangayInhabitant;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\ComplaintHearing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'abc.president']);
    }

    /**
     * Display ABC President dashboard.
     */
    public function index()
    {
        // Get overview statistics
        $overview = $this->getOverviewStatistics();
        
        // Get barangay performance metrics
        $barangayPerformance = $this->getBarangayPerformance();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get pending items requiring attention
        $pendingItems = $this->getPendingItems();
        
        // Get service statistics
        $serviceStats = $this->getServiceStatistics();
        
        // Get monthly trends (last 6 months)
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Get top performing barangays
        $topPerformers = $this->getTopPerformingBarangays(5);
        
        // Get alerts and notifications
        $alerts = $this->getSystemAlerts();
        
        return view('abc.dashboard', compact(
            'overview',
            'barangayPerformance',
            'recentActivities',
            'pendingItems',
            'serviceStats',
            'monthlyTrends',
            'topPerformers',
            'alerts'
        ));
    }

    /**
     * Get overview statistics.
     */
    private function getOverviewStatistics()
    {
        $activeBarangays = Barangay::where('is_active', true)->count();
        
        // Total residents from both systems
        $totalRbiResidents = BarangayInhabitant::where('is_verified', true)->count();
        $totalOnlineResidents = ResidentProfile::where('is_verified', true)->count();
        $totalResidents = $totalRbiResidents + $totalOnlineResidents;
        
        // Pending verifications
        $pendingRbiVerifications = BarangayInhabitant::where('is_verified', false)->count();
        $pendingOnlineVerifications = ResidentProfile::where('is_verified', false)->count();
        $pendingVerifications = $pendingRbiVerifications + $pendingOnlineVerifications;
        
        // Active officials across all barangays
        $activeOfficials = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['barangay-captain', 'barangay-secretary', 'barangay-staff']);
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->count();
        
        // Service requests (last 30 days)
        $last30Days = Carbon::now()->subDays(30);
        $recentDocuments = DocumentRequest::where('created_at', '>=', $last30Days)->count();
        $recentComplaints = Complaint::where('received_at', '>=', $last30Days)->count();
        $recentPermits = BusinessPermit::where('created_at', '>=', $last30Days)->count();
        $totalServiceRequests = $recentDocuments + $recentComplaints + $recentPermits;
        
        // Pending services
        $pendingDocuments = DocumentRequest::whereIn('status', ['pending', 'processing'])->count();
        $pendingComplaints = Complaint::whereNotIn('workflow_status', [
            'settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed', 'closed'
        ])->count();
        $pendingPermits = BusinessPermit::where('status', 'pending')->count();
        
        return [
            'active_barangays' => $activeBarangays,
            'total_residents' => $totalResidents,
            'rbi_residents' => $totalRbiResidents,
            'online_residents' => $totalOnlineResidents,
            'pending_verifications' => $pendingVerifications,
            'active_officials' => $activeOfficials,
            'total_service_requests' => $totalServiceRequests,
            'recent_documents' => $recentDocuments,
            'recent_complaints' => $recentComplaints,
            'recent_permits' => $recentPermits,
            'pending_documents' => $pendingDocuments,
            'pending_complaints' => $pendingComplaints,
            'pending_permits' => $pendingPermits,
        ];
    }

    /**
     * Get barangay performance metrics.
     */
    private function getBarangayPerformance()
    {
        $barangays = Barangay::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return $barangays->map(function($barangay) {
            // Resident counts
            $totalRbi = $barangay->inhabitants()->count();
            $verifiedRbi = $barangay->verifiedInhabitants()->count();
            $totalOnline = $barangay->residentProfiles()->count();
            $verifiedOnline = $barangay->verifiedResidents()->count();
            
            $totalResidents = $totalRbi + $totalOnline;
            $verifiedResidents = $verifiedRbi + $verifiedOnline;
            
            // Service metrics (last 30 days)
            $last30Days = Carbon::now()->subDays(30);
            
            $documentsProcessed = $barangay->documentRequests()
                ->where('created_at', '>=', $last30Days)
                ->count();
            
            $complaintsReceived = $barangay->complaints()
                ->where('received_at', '>=', $last30Days)
                ->count();
            
            $permitsProcessed = $barangay->businessPermits()
                ->where('created_at', '>=', $last30Days)
                ->count();
            
            // Pending items
            $pendingDocs = $barangay->documentRequests()
                ->whereIn('status', ['pending', 'processing'])
                ->count();
            
            $pendingComplaints = $barangay->complaints()
                ->whereNotIn('workflow_status', [
                    'settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed', 'closed'
                ])
                ->count();
            
            // Calculate performance score
            $verificationRate = $totalResidents > 0 ? ($verifiedResidents / $totalResidents) * 100 : 0;
            $serviceVolume = $documentsProcessed + $complaintsReceived + $permitsProcessed;
            
            return [
                'barangay' => $barangay,
                'total_residents' => $totalResidents,
                'verified_residents' => $verifiedResidents,
                'verification_rate' => round($verificationRate, 1),
                'documents_processed' => $documentsProcessed,
                'complaints_received' => $complaintsReceived,
                'permits_processed' => $permitsProcessed,
                'service_volume' => $serviceVolume,
                'pending_documents' => $pendingDocs,
                'pending_complaints' => $pendingComplaints,
            ];
        });
    }

    /**
     * Get recent activities across all barangays.
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // Recent document requests (last 10)
        $recentDocuments = DocumentRequest::with(['user', 'barangay', 'documentType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($doc) {
                return [
                    'type' => 'document',
                    'icon' => 'bi-file-earmark-text',
                    'color' => 'primary',
                    'title' => $doc->documentType->name,
                    'description' => "Requested by {$doc->user->full_name}",
                    'barangay' => $doc->barangay->name,
                    'status' => $doc->status,
                    'timestamp' => $doc->created_at,
                    'url' => route('barangay.documents.show', [$doc->barangay, $doc]),
                ];
            });
        
        // Recent complaints (last 5)
        $recentComplaints = Complaint::with(['complainant', 'barangay'])
            ->orderBy('received_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($complaint) {
                return [
                    'type' => 'complaint',
                    'icon' => 'bi-exclamation-triangle',
                    'color' => 'warning',
                    'title' => $complaint->subject,
                    'description' => "Filed by {$complaint->complainant->full_name}",
                    'barangay' => $complaint->barangay->name,
                    'status' => $complaint->workflow_status,
                    'timestamp' => $complaint->received_at,
                    'url' => route('barangay.complaints.show', [$complaint->barangay, $complaint]),
                ];
            });
        
        // Recent business permits (last 5)
        $recentPermits = BusinessPermit::with(['applicant', 'barangay'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($permit) {
                return [
                    'type' => 'permit',
                    'icon' => 'bi-shop',
                    'color' => 'success',
                    'title' => $permit->business_name,
                    'description' => "Applied by {$permit->applicant->full_name}",
                    'barangay' => $permit->barangay->name,
                    'status' => $permit->status,
                    'timestamp' => $permit->created_at,
                    'url' => '#',
                ];
            });
        
        return $activities
            ->merge($recentDocuments)
            ->merge($recentComplaints)
            ->merge($recentPermits)
            ->sortByDesc('timestamp')
            ->take(15)
            ->values();
    }

    /**
     * Get pending items requiring attention.
     */
    private function getPendingItems()
    {
        return [
            // Documents pending for more than 3 days
            'overdue_documents' => DocumentRequest::whereIn('status', ['pending', 'processing'])
                ->where('created_at', '<', Carbon::now()->subDays(3))
                ->count(),
            
            // Complaints with no assignment
            'unassigned_complaints' => Complaint::whereNull('assigned_to')
                ->whereNotIn('workflow_status', ['dismissed', 'closed'])
                ->count(),
            
            // Pending verifications
            'pending_verifications' => BarangayInhabitant::where('is_verified', false)->count() +
                                      ResidentProfile::where('is_verified', false)->count(),
            
            // Upcoming hearings (next 7 days)
            'upcoming_hearings' => ComplaintHearing::where('status', 'scheduled')
                ->whereBetween('scheduled_date', [now(), now()->addDays(7)])
                ->count(),
            
            // Expired permits
            'expired_permits' => BusinessPermit::where('status', 'approved')
                ->where('expires_at', '<', now())
                ->count(),
        ];
    }

    /**
     * Get service statistics.
     */
    private function getServiceStatistics()
    {
        $last30Days = Carbon::now()->subDays(30);
        
        // Document statistics
        $totalDocuments = DocumentRequest::where('created_at', '>=', $last30Days)->count();
        $approvedDocuments = DocumentRequest::whereIn('status', ['approved', 'released', 'claimed'])
            ->where('created_at', '>=', $last30Days)
            ->count();
        $documentCompletionRate = $totalDocuments > 0 ? ($approvedDocuments / $totalDocuments) * 100 : 0;
        
        // Complaint statistics
        $totalComplaints = Complaint::where('received_at', '>=', $last30Days)->count();
        $resolvedComplaints = Complaint::whereIn('workflow_status', [
                'settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed'
            ])
            ->where('received_at', '>=', $last30Days)
            ->count();
        $complaintResolutionRate = $totalComplaints > 0 ? ($resolvedComplaints / $totalComplaints) * 100 : 0;
        
        // Permit statistics
        $totalPermits = BusinessPermit::where('created_at', '>=', $last30Days)->count();
        $approvedPermits = BusinessPermit::where('status', 'approved')
            ->where('created_at', '>=', $last30Days)
            ->count();
        $permitApprovalRate = $totalPermits > 0 ? ($approvedPermits / $totalPermits) * 100 : 0;
        
        return [
            'document_completion_rate' => round($documentCompletionRate, 1),
            'complaint_resolution_rate' => round($complaintResolutionRate, 1),
            'permit_approval_rate' => round($permitApprovalRate, 1),
            'total_documents' => $totalDocuments,
            'total_complaints' => $totalComplaints,
            'total_permits' => $totalPermits,
        ];
    }

    /**
     * Get monthly trends for last 6 months.
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $rbiResidents = BarangayInhabitant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $onlineResidents = ResidentProfile::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'residents' => $rbiResidents + $onlineResidents,
                'documents' => DocumentRequest::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'complaints' => Complaint::whereYear('received_at', $date->year)
                    ->whereMonth('received_at', $date->month)
                    ->count(),
                'permits' => BusinessPermit::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        return $trends;
    }

    /**
     * Get top performing barangays.
     */
    private function getTopPerformingBarangays($limit = 5)
    {
        $barangays = Barangay::where('is_active', true)->get();
        
        return $barangays->map(function($barangay) {
            $totalRbi = $barangay->inhabitants()->count();
            $verifiedRbi = $barangay->verifiedInhabitants()->count();
            $totalOnline = $barangay->residentProfiles()->count();
            $verifiedOnline = $barangay->verifiedResidents()->count();
            
            $totalResidents = $totalRbi + $totalOnline;
            $verifiedResidents = $verifiedRbi + $verifiedOnline;
            
            $verificationRate = $totalResidents > 0 ? ($verifiedResidents / $totalResidents) : 0;
            
            // Service volume (last 30 days)
            $last30Days = Carbon::now()->subDays(30);
            $serviceVolume = $barangay->documentRequests()->where('created_at', '>=', $last30Days)->count() +
                           $barangay->complaints()->where('received_at', '>=', $last30Days)->count() +
                           $barangay->businessPermits()->where('created_at', '>=', $last30Days)->count();
            
            // Calculate performance score (weighted)
            $score = ($verificationRate * 40) + (min($serviceVolume / 10, 1) * 60);
            
            return [
                'barangay' => $barangay,
                'score' => round($score, 1),
                'verification_rate' => round($verificationRate * 100, 1),
                'service_volume' => $serviceVolume,
                'verified_residents' => $verifiedResidents,
            ];
        })
        ->sortByDesc('score')
        ->take($limit)
        ->values();
    }

    /**
     * Get system alerts and notifications.
     */
    private function getSystemAlerts()
    {
        $alerts = collect();
        
        // Overdue documents
        $overdueDocuments = DocumentRequest::whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<', Carbon::now()->subDays(5))
            ->count();
        
        if ($overdueDocuments > 0) {
            $alerts->push([
                'type' => 'warning',
                'icon' => 'bi-clock-history',
                'title' => 'Overdue Documents',
                'message' => "{$overdueDocuments} document(s) pending for more than 5 days",
                'url' => route('abc.reports.summary') . '?filter=overdue_documents',
            ]);
        }
        
        // Unassigned complaints
        $unassignedComplaints = Complaint::whereNull('assigned_to')
            ->whereNotIn('workflow_status', ['dismissed', 'closed'])
            ->count();
        
        if ($unassignedComplaints > 0) {
            $alerts->push([
                'type' => 'danger',
                'icon' => 'bi-exclamation-circle',
                'title' => 'Unassigned Complaints',
                'message' => "{$unassignedComplaints} complaint(s) need assignment",
                'url' => route('abc.reports.summary') . '?filter=unassigned_complaints',
            ]);
        }
        
        // Pending verifications
        $pendingVerifications = BarangayInhabitant::where('is_verified', false)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->count();
        
        if ($pendingVerifications > 10) {
            $alerts->push([
                'type' => 'info',
                'icon' => 'bi-person-check',
                'title' => 'Pending Verifications',
                'message' => "{$pendingVerifications} resident verifications pending for more than 7 days",
                'url' => route('abc.reports.summary') . '?filter=pending_verifications',
            ]);
        }
        
        // Upcoming hearings
        $upcomingHearings = ComplaintHearing::where('status', 'scheduled')
            ->whereBetween('scheduled_date', [now(), now()->addDays(3)])
            ->count();
        
        if ($upcomingHearings > 0) {
            $alerts->push([
                'type' => 'primary',
                'icon' => 'bi-calendar-event',
                'title' => 'Upcoming Hearings',
                'message' => "{$upcomingHearings} hearing(s) scheduled in the next 3 days",
                'url' => route('abc.reports.summary') . '?filter=upcoming_hearings',
            ]);
        }
        
        return $alerts;
    }








    public function profile()
    {
        $user = Auth::user();
            // code...
        return view('abc.profile', compact('user'));

        

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