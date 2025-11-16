<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\User;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);

    }

    /**
     * Show the municipality admin dashboard.
     */
    public function index(Request $request)
    {

        $user = Auth::user();

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
        // Overall system statistics
        $totalStats = [
            'barangays' => Barangay::active()->count(),
            'residents' => ResidentProfile::where('is_verified', true)->count(),
            'pending_residents' => ResidentProfile::where('is_verified', false)->count(),
            'document_requests' => DocumentRequest::count(),
            'pending_documents' => DocumentRequest::where('status', 'pending')->count(),
            'complaints' => Complaint::count(),
            'active_complaints' => Complaint::whereIn('status', ['received', 'assigned', 'in_process', 'mediation', 'hearing_scheduled'])->count(),
            'business_permits' => BusinessPermit::count(),
            'active_permits' => BusinessPermit::where('status', 'approved')->where('expires_at', '>', now())->count(),
            'expired_permits' => BusinessPermit::where('expires_at', '<=', now())->count(),
        ];

        // Recent activity
        $recentDocuments = DocumentRequest::with(['user', 'barangay', 'documentType'])
                                         ->latest()
                                         ->take(5)
                                         ->get();

        $recentComplaints = Complaint::with(['complainant', 'barangay', 'complaintType'])
                                   ->latest()
                                   ->take(5)
                                   ->get();

        $recentPermits = BusinessPermit::with(['applicant', 'barangay', 'businessPermitType'])
                                     ->latest()
                                     ->take(5)
                                     ->get();

        // Barangay performance summary
        $barangayStats = Barangay::active()
                               ->withCount([
                                   'residentProfiles as total_residents',
                                   'verifiedResidents as verified_residents',
                                   'pendingResidents as pending_residents',
                                   'documentRequests as total_documents',
                                   'complaints as total_complaints',
                                   'businessPermits as total_permits'
                               ])
                               ->get();

        // Terms expiring soon
        $expiringTerms = Term::expiringSoon(30)
                           ->with(['user', 'barangay'])
                           ->get();

        // Monthly statistics for charts (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'documents' => DocumentRequest::whereYear('submitted_at', $date->year)
                                            ->whereMonth('submitted_at', $date->month)
                                            ->count(),
                'complaints' => Complaint::whereYear('received_at', $date->year)
                                       ->whereMonth('received_at', $date->month)
                                       ->count(),
                'permits' => BusinessPermit::whereYear('submitted_at', $date->year)
                                         ->whereMonth('submitted_at', $date->month)
                                         ->count(),
                'residents' => ResidentProfile::whereYear('created_at', $date->year)
                                            ->whereMonth('created_at', $date->month)
                                            ->count(),
            ];
        }

        // System alerts
        $alerts = $this->getSystemAlerts();

        // Processing performance (average days)
        $processingStats = [
            'document_avg_days' => $this->getAverageProcessingDays('documents'),
            'complaint_avg_days' => $this->getAverageProcessingDays('complaints'),
            'permit_avg_days' => $this->getAverageProcessingDays('permits'),
        ];

        return view('admin.dashboard', compact(
            'totalStats',
            'recentDocuments',
            'recentComplaints',
            'recentPermits',
            'barangayStats',
            'expiringTerms',
            'monthlyData',
            'alerts',
            'processingStats'
        ));
    }

    /**
     * Get system alerts for admin attention.
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // Pending residents waiting for verification
        $pendingCount = ResidentProfile::where('is_verified', false)->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pending Resident Verifications',
                'message' => "{$pendingCount} residents are waiting for verification.",
                'action' => route('admin.residents.index', ['status' => 'pending']),
                'action_text' => 'Review Residents'
            ];
        }

        // Overdue document requests
        $overdueDocuments = DocumentRequest::where('status', 'pending')
                                         ->where('submitted_at', '<', now()->subDays(7))
                                         ->count();
        if ($overdueDocuments > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Overdue Document Requests',
                'message' => "{$overdueDocuments} document requests are overdue (>7 days).",
                'action' => route('admin.reports.documents', ['overdue' => true]),
                'action_text' => 'View Overdue'
            ];
        }

        // Terms expiring soon
        $expiringTerms = Term::expiringSoon(30)->count();
        if ($expiringTerms > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Terms Expiring Soon',
                'message' => "{$expiringTerms} official terms will expire within 30 days.",
                'action' => route('admin.terms.index', ['expiring' => true]),
                'action_text' => 'Manage Terms'
            ];
        }

        // Permits expiring soon
        $expiringPermits = BusinessPermit::expiringSoon(30)->count();
        if ($expiringPermits > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Business Permits Expiring',
                'message' => "{$expiringPermits} business permits will expire within 30 days.",
                'action' => route('admin.reports.permits', ['expiring' => true]),
                'action_text' => 'View Expiring'
            ];
        }

        // High priority complaints
        $urgentComplaints = Complaint::where('priority', 'urgent')
                                  ->whereIn('status', ['received', 'assigned', 'in_process'])
                                  ->count();
        if ($urgentComplaints > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Urgent Complaints',
                'message' => "{$urgentComplaints} urgent complaints require immediate attention.",
                'action' => route('admin.reports.complaints', ['priority' => 'urgent']),
                'action_text' => 'View Urgent'
            ];
        }

        return $alerts;
    }

    /**
     * Get average processing days for different request types.
     */
    private function getAverageProcessingDays($type)
    {
        switch ($type) {
            case 'documents':
                $completed = DocumentRequest::whereNotNull('approved_at')->get();
                if ($completed->isEmpty()) return 0;
                
                $totalDays = 0;
                foreach ($completed as $request) {
                    $totalDays += $request->submitted_at->diffInDays($request->approved_at);
                }
                return round($totalDays / $completed->count(), 1);

            case 'complaints':
                $resolved = Complaint::whereNotNull('resolved_at')->get();
                if ($resolved->isEmpty()) return 0;
                
                $totalDays = 0;
                foreach ($resolved as $complaint) {
                    $totalDays += $complaint->received_at->diffInDays($complaint->resolved_at);
                }
                return round($totalDays / $resolved->count(), 1);

            case 'permits':
                $approved = BusinessPermit::whereNotNull('approved_at')->get();
                if ($approved->isEmpty()) return 0;
                
                $totalDays = 0;
                foreach ($approved as $permit) {
                    $totalDays += $permit->submitted_at->diffInDays($permit->approved_at);
                }
                return round($totalDays / $approved->count(), 1);

            default:
                return 0;
        }
    }
}