<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\DocumentType;
use App\Models\ComplaintType;
use App\Models\BusinessPermitType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'resident']);
    }

    /**
     * Show the resident dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $residentProfile = $user->residentProfile;
        $barangay = $user->barangay;

        // Check if profile exists
        if (!$residentProfile) {
            return redirect()->route('resident.profile.create')
                           ->with('info', 'Please complete your resident profile to access services.');
        }

        // Personal statistics
        $stats = [
            'document_requests' => DocumentRequest::where('user_id', $user->id)->count(),
            'pending_documents' => DocumentRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved_documents' => DocumentRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'complaints' => Complaint::where('complainant_id', $user->id)->count(),
            'active_complaints' => Complaint::where('complainant_id', $user->id)->pending()->count(),
            'resolved_complaints' => Complaint::where('complainant_id', $user->id)->resolved()->count(),
            'business_permits' => BusinessPermit::where('applicant_id', $user->id)->count(),
            'active_permits' => $this->getActivePermits($user->id),
        ];

        // Recent requests
        $recentActivity = [
            'documents' => DocumentRequest::where('user_id', $user->id)
                                        ->with(['documentType', 'processor'])
                                        ->latest()
                                        ->take(5)
                                        ->get(),
            
            'complaints' => Complaint::where('complainant_id', $user->id)
                                   ->with('complaintType', 'assignedOfficial')
                                   ->latest()
                                   ->take(5)
                                   ->get(),
            
            'permits' => BusinessPermit::where('applicant_id', $user->id)
                                     ->with(['businessPermitType', 'processor'])
                                     ->latest()
                                     ->take(3)
                                     ->get(),
        ];

        // Pending items requiring attention
        $pendingItems = [
            'profile_incomplete' => method_exists($residentProfile, 'isComplete') ? !$residentProfile->isComplete() : false,
            'profile_unverified' => !$residentProfile->is_verified,
            'pending_documents' => DocumentRequest::where('user_id', $user->id)
                                                ->whereIn('status', ['pending', 'processing'])
                                                ->with('documentType')
                                                ->get(),
            'active_complaints' => Complaint::where('complainant_id', $user->id)
                                          ->pending()
                                          ->with('complaintType')
                                          ->get(),
            'expiring_permits' => $this->getExpiringPermits($user->id),
        ];

        // Available services
        $availableServices = [
            'document_types' => DocumentType::where('is_active', true)->orderBy('name')->get(),
            'complaint_types' => ComplaintType::where('is_active', true)->orderBy('name')->get(),
            'permit_types' => $this->getPermitTypes(),
        ];

        // Notifications/Alerts for resident
        $alerts = $this->getResidentAlerts($user, $residentProfile);

        // Monthly activity (last 6 months)
        $monthlyActivity = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $monthlyActivity[] = [
                'month' => $date->format('M Y'),
                'documents' => DocumentRequest::where('user_id', $user->id)
                                            ->whereYear('submitted_at', $date->year)
                                            ->whereMonth('submitted_at', $date->month)
                                            ->count(),
                'complaints' => Complaint::where('complainant_id', $user->id)
                                       ->whereYear('received_at', $date->year)
                                       ->whereMonth('received_at', $date->month)
                                       ->count(),
                'permits' => BusinessPermit::where('applicant_id', $user->id)
                                         ->whereYear('created_at', $date->year)
                                         ->whereMonth('created_at', $date->month)
                                         ->count(),
            ];
        }

        // Quick actions available to resident
        $quickActions = $this->getQuickActions($residentProfile);

        // Get active announcements for this barangay
        $announcements = \App\Models\Announcement::where('barangay_id', $user->barangay_id)
            ->published()
            ->ordered()
            ->limit(3)
            ->get();

        return view('resident.dashboard', compact(
            'user',
            'residentProfile',
            'barangay',
            'stats',
            'recentActivity',
            'pendingItems',
            'availableServices',
            'alerts',
            'monthlyActivity',
            'quickActions',
            'announcements'
        ));
    }

    /**
     * Get active permits count
     */
    private function getActivePermits($userId)
    {
        try {
            return BusinessPermit::where('applicant_id', $userId)
                                ->where('status', 'approved')
                                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get expiring permits
     */
    private function getExpiringPermits($userId)
    {
        try {
            return BusinessPermit::where('applicant_id', $userId)
                                ->where('status', 'approved')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '<=', now()->addDays(30))
                                ->where('expires_at', '>', now())
                                ->with('businessPermitType')
                                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get permit types
     */
    private function getPermitTypes()
    {
        try {
            if (class_exists('App\Models\BusinessPermitType')) {
                return BusinessPermitType::where('is_active', true)->orderBy('name')->get();
            }
        } catch (\Exception $e) {
            // Model doesn't exist
        }
        return collect();
    }

    /**
     * Get alerts specific to this resident.
     */
    private function getResidentAlerts($user, $residentProfile)
    {
        $alerts = [];

        // Profile incomplete
        if (method_exists($residentProfile, 'isComplete') && !$residentProfile->isComplete()) {
            $completionPercentage = method_exists($residentProfile, 'getCompletionPercentageAttribute') 
                ? $residentProfile->completion_percentage 
                : 0;
            
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Complete Your Profile',
                'message' => 'Your profile is ' . $completionPercentage . '% complete. Please provide missing information.',
                'action' => route('resident.profile.edit'),
                'action_text' => 'Complete Profile',
                'icon' => 'user'
            ];
        }

        // Profile not verified
        if (!$residentProfile->is_verified) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Profile Verification Pending',
                'message' => 'Your profile is waiting for verification by barangay staff. Some services may be limited.',
                'action' => route('resident.profile.show'),
                'action_text' => 'View Profile',
                'icon' => 'check-circle'
            ];
        }

        // Overdue document requests
        $overdueDocuments = DocumentRequest::where('user_id', $user->id)
                                         ->where('status', 'pending')
                                         ->where('submitted_at', '<', now()->subDays(7))
                                         ->count();
        if ($overdueDocuments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Delayed Document Requests',
                'message' => "{$overdueDocuments} of your document requests are taking longer than expected.",
                'action' => route('resident.documents.index'),
                'action_text' => 'Check Status',
                'icon' => 'file-text'
            ];
        }

        // Expiring permits
        $expiringPermits = $this->getExpiringPermits($user->id)->count();
        if ($expiringPermits > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Permits Expiring Soon',
                'message' => "{$expiringPermits} of your business permits will expire within 30 days.",
                'action' => '#',
                'action_text' => 'Renew Permits',
                'icon' => 'briefcase'
            ];
        }

        // Active complaints needing follow-up
        $followUpComplaints = Complaint::where('complainant_id', $user->id)
                                    ->where('requires_follow_up', true)
                                    ->where('follow_up_date', '<=', now())
                                    ->count();
        if ($followUpComplaints > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Complaint Follow-up Due',
                'message' => "{$followUpComplaints} of your complaints require follow-up action.",
                'action' => route('resident.complaints.index'),
                'action_text' => 'View Complaints',
                'icon' => 'message-square'
            ];
        }

        // New resident welcome
        if ($residentProfile->created_at && $residentProfile->created_at->diffInDays(now()) <= 7) {
            $alerts[] = [
                'type' => 'success',
                'title' => 'Welcome to UBMS!',
                'message' => 'Welcome to the Unified Barangay Management System. Explore available services below.',
                'action' => '#',
                'action_text' => 'Learn More',
                'icon' => 'star'
            ];
        }

        return $alerts;
    }

    /**
     * Get quick actions available to resident based on their status.
     */
    private function getQuickActions($residentProfile)
    {
        $actions = [];

        // Always available actions
        $actions[] = [
            'title' => 'Request Document',
            'description' => 'Apply for barangay certificates and clearances',
            'action' => route('resident.documents.create'),
            'icon' => 'file-plus',
            'color' => 'primary',
            'available' => $residentProfile->is_verified
        ];

        $actions[] = [
            'title' => 'File Complaint',
            'description' => 'Report issues or disputes in your community',
            'action' => route('resident.complaints.create'),
            'icon' => 'alert-circle',
            'color' => 'warning',
            'available' => $residentProfile->is_verified
        ];

        $actions[] = [
            'title' => 'Apply for Business Permit',
            'description' => 'Start or renew your business permit',
            'action' => '#',
            'icon' => 'briefcase',
            'color' => 'success',
            'available' => $residentProfile->is_verified
        ];

        $actions[] = [
            'title' => 'Update Profile',
            'description' => 'Keep your information current',
            'action' => route('resident.profile.edit'),
            'icon' => 'user',
            'color' => 'info',
            'available' => true
        ];

        // Special actions for senior citizens
        if (isset($residentProfile->is_senior_citizen) && $residentProfile->is_senior_citizen) {
            $actions[] = [
                'title' => 'Senior Citizen Services',
                'description' => 'Access special services for senior citizens',
                'action' => route('resident.documents.create', ['type' => 'senior-citizen-id']),
                'icon' => 'heart',
                'color' => 'purple',
                'available' => $residentProfile->is_verified
            ];
        }

        // Special actions for PWD
        if (isset($residentProfile->is_pwd) && $residentProfile->is_pwd) {
            $actions[] = [
                'title' => 'PWD Services',
                'description' => 'Services for persons with disabilities',
                'action' => route('resident.documents.create', ['type' => 'pwd-id']),
                'icon' => 'accessibility',
                'color' => 'orange',
                'available' => $residentProfile->is_verified
            ];
        }

        return $actions;
    }
}