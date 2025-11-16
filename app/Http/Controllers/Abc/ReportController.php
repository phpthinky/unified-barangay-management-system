<?php

namespace App\Http\Controllers\Abc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\BarangayInhabitant;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'abc.president']);
    }

    /**
     * Display reports index page.
     */
    public function index()
    {
        $barangays = Barangay::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return view('abc.reports.index', compact('barangays'));
    }

    /**
     * Show executive summary report.
     */
    public function summary(Request $request)
    {
        $dateRange = $this->getDateRange($request->get('period', 'last_month'));
        
        $overview = $this->getOverviewStats($dateRange);
        $performance = $this->getPerformanceMetrics($dateRange);
        $serviceQuality = $this->getServiceQualityMetrics($dateRange);
        $topPerformers = $this->getTopPerformingBarangays($dateRange);
        $monthlyTrends = $this->getMonthlyTrends(6);

        return view('abc.reports.summary', compact(
            'overview',
            'performance',
            'serviceQuality',
            'topPerformers',
            'monthlyTrends',
            'dateRange'
        ));
    }

    /**
     * Show barangay-specific report.
     */
    public function barangayReport(Request $request, Barangay $barangay)
    {
        $dateRange = $this->getDateRange($request->get('period', 'last_month'));
        
        $barangayData = $this->getBarangayReportData($barangay, $dateRange);
        $comparisonData = $this->getBarangayComparisonData($barangay, $dateRange);
        $serviceMetrics = $this->getBarangayServiceMetrics($barangay, $dateRange);
        $monthlyTrends = $this->getBarangayMonthlyTrends($barangay, 6);

        return view('abc.reports.barangay', compact(
            'barangay',
            'barangayData',
            'comparisonData',
            'serviceMetrics',
            'monthlyTrends',
            'dateRange'
        ));
    }

    /**
     * Get date range based on period selection.
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'last_week':
                return [
                    'start' => Carbon::now()->subWeek()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last Week'
                ];
            
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last Month'
                ];
            
            case 'last_quarter':
                return [
                    'start' => Carbon::now()->subMonths(3)->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last Quarter'
                ];
            
            case 'last_year':
                return [
                    'start' => Carbon::now()->subYear()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last Year'
                ];
            
            default:
                return [
                    'start' => Carbon::now()->subMonth()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'label' => 'Last Month'
                ];
        }
    }

    /**
     * Get overview statistics.
     */
    private function getOverviewStats($dateRange)
    {
        // Count RBI inhabitants and online profiles created in date range
        $rbiCount = BarangayInhabitant::where('is_verified', true)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
            
        $onlineCount = ResidentProfile::where('is_verified', true)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        
        return [
            'total_barangays' => Barangay::where('is_active', true)->count(),
            'total_residents' => $rbiCount + $onlineCount,
            'rbi_residents' => $rbiCount,
            'online_residents' => $onlineCount,
            'total_officials' => User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['barangay-captain', 'barangay-secretary', 'barangay-staff', 'lupon-chairman', 'lupon-member']);
                })
                ->where('is_active', true)
                ->where('is_archived', false)
                ->count(),
            'total_services' => DocumentRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count() +
                              Complaint::whereBetween('received_at', [$dateRange['start'], $dateRange['end']])->count() +
                              BusinessPermit::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'date_range' => $dateRange['label']
        ];
    }

    /**
     * Get performance metrics.
     */
    private function getPerformanceMetrics($dateRange)
    {
        return [
            'document_completion_rate' => $this->getCompletionRate('documents', $dateRange),
            'complaint_resolution_rate' => $this->getCompletionRate('complaints', $dateRange),
            'permit_approval_rate' => $this->getCompletionRate('permits', $dateRange),
            'avg_processing_days' => $this->getOverallProcessingDays($dateRange),
        ];
    }

    /**
     * Get service quality metrics.
     */
    private function getServiceQualityMetrics($dateRange)
    {
        return [
            'on_time_completion' => $this->getOnTimeCompletionRate($dateRange),
            'citizen_satisfaction' => $this->getCitizenSatisfactionRate($dateRange),
            'digital_adoption' => $this->getDigitalAdoptionRate($dateRange),
            'staff_efficiency' => $this->getStaffEfficiencyScore($dateRange),
        ];
    }

    /**
     * Get completion rate for service types.
     */
    private function getCompletionRate($type, $dateRange)
    {
        switch ($type) {
            case 'documents':
                $total = DocumentRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
                $completed = DocumentRequest::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('status', ['released', 'claimed'])
                    ->count();
                break;
            
            case 'complaints':
                $total = Complaint::whereBetween('received_at', [$dateRange['start'], $dateRange['end']])->count();
                // Resolved workflow statuses: settled by captain, resolved by lupon, certificate issued
                $completed = Complaint::whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed', 'closed'])
                    ->count();
                break;
            
            case 'permits':
                $total = BusinessPermit::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
                $completed = BusinessPermit::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'approved')
                    ->count();
                break;
            
            default:
                return 0;
        }

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    /**
     * Get overall average processing days.
     */
    private function getOverallProcessingDays($dateRange)
    {
        // Documents: created_at to released_at
        $documentAvg = DocumentRequest::whereNotNull('released_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->avg(function($request) {
                return $request->created_at->diffInDays($request->released_at);
            });

        // Complaints: received_at to resolved date (settled or resolved)
        $complaintAvg = Complaint::whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'certificate_issued'])
            ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->filter(function($complaint) {
                return $complaint->settled_by_captain_at || $complaint->lupon_resolved_at || $complaint->certificate_issued_at;
            })
            ->avg(function($complaint) {
                $endDate = $complaint->settled_by_captain_at ?? 
                          $complaint->lupon_resolved_at ?? 
                          $complaint->certificate_issued_at ?? 
                          now();
                return $complaint->received_at->diffInDays($endDate);
            });

        // Permits: created_at to approved_at
        $permitAvg = BusinessPermit::whereNotNull('approved_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->avg(function($permit) {
                return $permit->created_at->diffInDays($permit->approved_at);
            });

        $averages = array_filter([$documentAvg, $complaintAvg, $permitAvg]);
        return count($averages) > 0 ? round(array_sum($averages) / count($averages), 1) : 0;
    }

    /**
     * Get on-time completion rate.
     */
    private function getOnTimeCompletionRate($dateRange)
    {
        $totalCompleted = DocumentRequest::whereNotNull('released_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
        
        if ($totalCompleted == 0) return 0;

        $onTimeCompleted = DocumentRequest::whereNotNull('released_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->filter(function($request) {
                $processingDays = $request->created_at->diffInDays($request->released_at);
                $expectedDays = $request->documentType->processing_days ?? 5;
                return $processingDays <= $expectedDays;
            })
            ->count();

        return round(($onTimeCompleted / $totalCompleted) * 100, 1);
    }

    /**
     * Get citizen satisfaction rate.
     */
    private function getCitizenSatisfactionRate($dateRange)
    {
        $complaintResolution = $this->getCompletionRate('complaints', $dateRange);
        $documentCompletion = $this->getCompletionRate('documents', $dateRange);
        $permitCompletion = $this->getCompletionRate('permits', $dateRange);
        
        return round(($complaintResolution + $documentCompletion + $permitCompletion) / 3, 1);
    }

    /**
     * Get digital adoption rate.
     */
    private function getDigitalAdoptionRate($dateRange)
    {
        // Total verified residents from both systems
        $totalRbiVerified = BarangayInhabitant::where('is_verified', true)->count();
        $totalOnlineVerified = ResidentProfile::where('is_verified', true)->count();
        $totalResidents = $totalRbiVerified + $totalOnlineVerified;
        
        if ($totalResidents == 0) return 0;

        // Active online users (those who logged in during date range)
        $activeUsers = User::whereHas('residentProfile', function($query) {
                $query->where('is_verified', true);
            })
            ->where('last_login_at', '>=', $dateRange['start'])
            ->count();

        return round(($activeUsers / $totalResidents) * 100, 1);
    }

    /**
     * Get staff efficiency score.
     */
    private function getStaffEfficiencyScore($dateRange)
    {
        $staff = User::whereHas('roles', function($query) {
                $query->whereIn('name', ['barangay-captain', 'barangay-secretary', 'barangay-staff']);
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->get();

        if ($staff->isEmpty()) return 0;

        $totalScore = 0;
        $staffCount = 0;

        foreach ($staff as $staffMember) {
            // Documents processed
            $documentsProcessed = DocumentRequest::where('processed_by', $staffMember->id)
                ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
                ->whereIn('status', ['approved', 'released', 'claimed'])
                ->count();
            
            // Complaints handled (assigned or resolved)
            $complaintsHandled = Complaint::where('assigned_to', $staffMember->id)
                ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                ->count();
            
            // Captain-specific: resolved by captain
            $captainResolutions = Complaint::where('captain_approved_by', $staffMember->id)
                ->whereBetween('captain_approved_at', [$dateRange['start'], $dateRange['end']])
                ->count();
            
            // Permits processed
            $permitsProcessed = BusinessPermit::where('processed_by', $staffMember->id)
                ->whereBetween('updated_at', [$dateRange['start'], $dateRange['end']])
                ->where('status', 'approved')
                ->count();

            $totalProcessed = $documentsProcessed + $complaintsHandled + $captainResolutions + $permitsProcessed;
            
            $score = min(100, $totalProcessed * 10);
            $totalScore += $score;
            $staffCount++;
        }

        return $staffCount > 0 ? round($totalScore / $staffCount, 1) : 0;
    }

    /**
     * Get top performing barangays.
     */
    private function getTopPerformingBarangays($dateRange)
    {
        return Barangay::where('is_active', true)
            ->get()
            ->map(function($barangay) use ($dateRange) {
                $score = 0;
                
                // Resident verification rate (30% weight)
                $totalRbiResidents = $barangay->inhabitants()->count();
                $verifiedRbiResidents = $barangay->verifiedInhabitants()->count();
                $totalOnlineResidents = $barangay->residentProfiles()->count();
                $verifiedOnlineResidents = $barangay->verifiedResidents()->count();
                
                $totalResidents = $totalRbiResidents + $totalOnlineResidents;
                $verifiedResidents = $verifiedRbiResidents + $verifiedOnlineResidents;
                
                $verificationRate = $totalResidents > 0 ? ($verifiedResidents / $totalResidents) : 0;
                $score += $verificationRate * 30;
                
                // Service completion rate (40% weight)
                $totalServices = $barangay->documentRequests()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count() + 
                    $barangay->complaints()
                    ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                    ->count() + 
                    $barangay->businessPermits()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count();
                
                $completedServices = $barangay->documentRequests()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('status', ['released', 'claimed'])
                    ->count() +
                    $barangay->complaints()
                    ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed'])
                    ->count() +
                    $barangay->businessPermits()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'approved')
                    ->count();
                
                $completionRate = $totalServices > 0 ? ($completedServices / $totalServices) : 0;
                $score += $completionRate * 40;
                
                // Processing speed (30% weight)
                $avgProcessingDays = $this->getBarangayProcessingDays($barangay->id, $dateRange);
                $speedScore = $avgProcessingDays > 0 ? max(0, (10 - $avgProcessingDays) / 10) : 1;
                $score += $speedScore * 30;
                
                return [
                    'barangay' => $barangay,
                    'score' => round($score, 1),
                    'verification_rate' => round($verificationRate * 100, 1),
                    'completion_rate' => round($completionRate * 100, 1),
                    'avg_processing_days' => $avgProcessingDays,
                ];
            })
            ->sortByDesc('score')
            ->take(5)
            ->values();
    }

    /**
     * Get barangay processing days.
     */
    private function getBarangayProcessingDays($barangayId, $dateRange)
    {
        // Documents
        $documentAvg = DocumentRequest::where('barangay_id', $barangayId)
            ->whereNotNull('released_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->avg(function($request) {
                return $request->created_at->diffInDays($request->released_at);
            });

        // Complaints
        $complaintAvg = Complaint::where('barangay_id', $barangayId)
            ->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'certificate_issued'])
            ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->filter(function($complaint) {
                return $complaint->settled_by_captain_at || $complaint->lupon_resolved_at || $complaint->certificate_issued_at;
            })
            ->avg(function($complaint) {
                $endDate = $complaint->settled_by_captain_at ?? 
                          $complaint->lupon_resolved_at ?? 
                          $complaint->certificate_issued_at ?? 
                          now();
                return $complaint->received_at->diffInDays($endDate);
            });

        // Permits
        $permitAvg = BusinessPermit::where('barangay_id', $barangayId)
            ->whereNotNull('approved_at')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->avg(function($permit) {
                return $permit->created_at->diffInDays($permit->approved_at);
            });

        $averages = array_filter([$documentAvg, $complaintAvg, $permitAvg]);
        return count($averages) > 0 ? round(array_sum($averages) / count($averages), 1) : 0;
    }

    /**
     * Get monthly trends data.
     */
    private function getMonthlyTrends($months = 6)
    {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // Combine RBI and online registrations
            $rbiResidents = BarangayInhabitant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $onlineResidents = ResidentProfile::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'new_residents' => $rbiResidents + $onlineResidents,
                'rbi_residents' => $rbiResidents,
                'online_residents' => $onlineResidents,
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
     * Get barangay report data.
     */
    private function getBarangayReportData($barangay, $dateRange)
    {
        // Combine RBI and online profiles
        $totalRbiResidents = $barangay->inhabitants()->count();
        $verifiedRbiResidents = $barangay->verifiedInhabitants()->count();
        $totalOnlineResidents = $barangay->residentProfiles()->count();
        $verifiedOnlineResidents = $barangay->verifiedResidents()->count();
        
        return [
            'total_residents' => $totalRbiResidents + $totalOnlineResidents,
            'verified_residents' => $verifiedRbiResidents + $verifiedOnlineResidents,
            'pending_residents' => ($totalRbiResidents - $verifiedRbiResidents) + ($totalOnlineResidents - $verifiedOnlineResidents),
            'rbi_total' => $totalRbiResidents,
            'rbi_verified' => $verifiedRbiResidents,
            'online_total' => $totalOnlineResidents,
            'online_verified' => $verifiedOnlineResidents,
            'documents_processed' => $barangay->documentRequests()
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'complaints_received' => $barangay->complaints()
                ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'permits_processed' => $barangay->businessPermits()
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'date_range' => $dateRange['label']
        ];
    }

    /**
     * Get barangay comparison data.
     */
    private function getBarangayComparisonData($barangay, $dateRange)
    {
        $allBarangays = Barangay::where('is_active', true)->get();
        
        $currentBarangayData = $this->getBarangayReportData($barangay, $dateRange);
        $averageData = $this->calculateAverageBarangayData($allBarangays, $dateRange);
        
        return [
            'current' => $currentBarangayData,
            'average' => $averageData,
            'rank' => $this->getBarangayRank($barangay, $allBarangays, $dateRange)
        ];
    }

    /**
     * Calculate average barangay data.
     */
    private function calculateAverageBarangayData($barangays, $dateRange)
    {
        $totalBarangays = $barangays->count();
        if ($totalBarangays == 0) return [];

        $averages = [
            'total_residents' => 0,
            'verified_residents' => 0,
            'documents_processed' => 0,
            'complaints_received' => 0,
            'permits_processed' => 0,
        ];

        foreach ($barangays as $barangay) {
            $data = $this->getBarangayReportData($barangay, $dateRange);
            foreach ($averages as $key => $value) {
                $averages[$key] += $data[$key];
            }
        }

        foreach ($averages as $key => $value) {
            $averages[$key] = round($value / $totalBarangays, 1);
        }

        return $averages;
    }

    /**
     * Get barangay rank.
     */
    private function getBarangayRank($barangay, $allBarangays, $dateRange)
    {
        $scores = [];
        
        foreach ($allBarangays as $brgy) {
            $data = $this->getBarangayReportData($brgy, $dateRange);
            $score = $data['verified_residents'] + $data['documents_processed'] + $data['permits_processed'];
            $scores[$brgy->id] = $score;
        }

        arsort($scores);
        $rank = array_search($barangay->id, array_keys($scores)) + 1;
        
        return [
            'current' => $rank,
            'total' => count($scores)
        ];
    }

    /**
     * Get barangay service metrics.
     */
    private function getBarangayServiceMetrics($barangay, $dateRange)
    {
        return [
            'document_completion_rate' => $this->getBarangayCompletionRate($barangay, 'documents', $dateRange),
            'complaint_resolution_rate' => $this->getBarangayCompletionRate($barangay, 'complaints', $dateRange),
            'permit_approval_rate' => $this->getBarangayCompletionRate($barangay, 'permits', $dateRange),
            'avg_processing_days' => $this->getBarangayProcessingDays($barangay->id, $dateRange),
        ];
    }

    /**
     * Get barangay completion rate.
     */
    private function getBarangayCompletionRate($barangay, $type, $dateRange)
    {
        switch ($type) {
            case 'documents':
                $total = $barangay->documentRequests()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count();
                $completed = $barangay->documentRequests()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('status', ['released', 'claimed'])
                    ->count();
                break;
            
            case 'complaints':
                $total = $barangay->complaints()
                    ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                    ->count();
                $completed = $barangay->complaints()
                    ->whereBetween('received_at', [$dateRange['start'], $dateRange['end']])
                    ->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'certificate_issued', 'dismissed'])
                    ->count();
                break;
            
            case 'permits':
                $total = $barangay->businessPermits()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->count();
                $completed = $barangay->businessPermits()
                    ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                    ->where('status', 'approved')
                    ->count();
                break;
            
            default:
                return 0;
        }

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    /**
     * Get barangay monthly trends.
     */
    private function getBarangayMonthlyTrends($barangay, $months = 6)
    {
        $trends = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'documents' => $barangay->documentRequests()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'complaints' => $barangay->complaints()
                    ->whereYear('received_at', $date->year)
                    ->whereMonth('received_at', $date->month)
                    ->count(),
                'permits' => $barangay->businessPermits()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return $trends;
    }



    //export
    /**
 * Export reports in various formats.
 */
public function export(Request $request)
{
    $request->validate([
        'report_type' => 'required|in:summary,barangay,performance',
        'format' => 'required|in:pdf,excel,csv',
        'date_range' => 'required|in:last_week,last_month,last_quarter,last_year',
        'barangays' => 'nullable|array',
        'barangays.*' => 'exists:barangays,id',
    ]);

    $dateRange = $this->getDateRange($request->date_range);
    $timestamp = now()->format('Y-m-d_H-i-s');

    try {
        switch ($request->report_type) {
            case 'summary':
                return $this->exportSummaryReport($request->format, $dateRange, $timestamp);
            
            case 'barangay':
                if (!$request->barangays || empty($request->barangays)) {
                    return back()->with('error', 'Please select at least one barangay.');
                }
                return $this->exportBarangayReports($request->format, $request->barangays, $dateRange, $timestamp);
            
            case 'performance':
                return $this->exportPerformanceReport($request->format, $dateRange, $timestamp);
            
            default:
                return back()->with('error', 'Invalid report type.');
        }
    } catch (\Exception $e) {
        \Log::error('Report export failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to generate report. Please try again.');
    }
}

/**
 * Export summary report.
 */
private function exportSummaryReport($format, $dateRange, $timestamp)
{
    $data = [
        'overview' => $this->getOverviewStats($dateRange),
        'performance' => $this->getPerformanceMetrics($dateRange),
        'serviceQuality' => $this->getServiceQualityMetrics($dateRange),
        'topPerformers' => $this->getTopPerformingBarangays($dateRange),
        'monthlyTrends' => $this->getMonthlyTrends(6),
        'dateRange' => $dateRange,
        'generated_at' => now(),
    ];

    $filename = "abc_summary_report_{$timestamp}";

    switch ($format) {
        case 'pdf':
            return $this->generateSummaryPDF($data, $filename);
        
        case 'excel':
        case 'csv':
            return $this->generateSummaryExcel($data, $filename, $format);
        
        default:
            return back()->with('error', 'Invalid export format.');
    }
}

/**
 * Export barangay reports.
 */
private function exportBarangayReports($format, $barangayIds, $dateRange, $timestamp)
{
    $barangays = Barangay::whereIn('id', $barangayIds)->get();
    $reports = [];

    foreach ($barangays as $barangay) {
        $reports[] = [
            'barangay' => $barangay,
            'data' => $this->getBarangayReportData($barangay, $dateRange),
            'metrics' => $this->getBarangayServiceMetrics($barangay, $dateRange),
            'comparison' => $this->getBarangayComparisonData($barangay, $dateRange),
        ];
    }

    $data = [
        'reports' => $reports,
        'dateRange' => $dateRange,
        'generated_at' => now(),
    ];

    $filename = "abc_barangay_reports_{$timestamp}";

    switch ($format) {
        case 'pdf':
            return $this->generateBarangayPDF($data, $filename);
        
        case 'excel':
        case 'csv':
            return $this->generateBarangayExcel($data, $filename, $format);
        
        default:
            return back()->with('error', 'Invalid export format.');
    }
}

/**
 * Export performance report.
 */
private function exportPerformanceReport($format, $dateRange, $timestamp)
{
    $data = [
        'performance' => $this->getPerformanceMetrics($dateRange),
        'serviceQuality' => $this->getServiceQualityMetrics($dateRange),
        'barangayComparison' => Barangay::where('is_active', true)->get()->map(function($barangay) use ($dateRange) {
            return [
                'barangay' => $barangay,
                'metrics' => $this->getBarangayServiceMetrics($barangay, $dateRange),
            ];
        }),
        'dateRange' => $dateRange,
        'generated_at' => now(),
    ];

    $filename = "abc_performance_report_{$timestamp}";

    switch ($format) {
        case 'pdf':
            return $this->generatePerformancePDF($data, $filename);
        
        case 'excel':
        case 'csv':
            return $this->generatePerformanceExcel($data, $filename, $format);
        
        default:
            return back()->with('error', 'Invalid export format.');
    }
}

/**
 * Generate Summary PDF.
 */
private function generateSummaryPDF($data, $filename)
{
    // Simple HTML-based PDF generation
    $html = view('abc.exports.summary-pdf', $data)->render();
    
    // Use DomPDF if you have it installed
    if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download($filename . '.pdf');
    }
    
    // Fallback: Return as HTML
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
}

/**
 * Generate Summary Excel/CSV.
 */
private function generateSummaryExcel($data, $filename, $format)
{
    $headers = [
        'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
        'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $format . '"',
    ];

    $callback = function() use ($data) {
        $file = fopen('php://output', 'w');
        
        // Overview Section
        fputcsv($file, ['ABC EXECUTIVE SUMMARY REPORT']);
        fputcsv($file, ['Generated:', $data['generated_at']->format('F d, Y h:i A')]);
        fputcsv($file, ['Period:', $data['dateRange']['label']]);
        fputcsv($file, []);
        
        fputcsv($file, ['OVERVIEW STATISTICS']);
        fputcsv($file, ['Total Barangays', $data['overview']['total_barangays']]);
        fputcsv($file, ['New Residents (RBI)', $data['overview']['rbi_residents']]);
        fputcsv($file, ['New Residents (Online)', $data['overview']['online_residents']]);
        fputcsv($file, ['Total New Residents', $data['overview']['total_residents']]);
        fputcsv($file, ['Active Officials', $data['overview']['total_officials']]);
        fputcsv($file, ['Total Services', $data['overview']['total_services']]);
        fputcsv($file, []);
        
        // Performance Metrics
        fputcsv($file, ['PERFORMANCE METRICS']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Document Completion Rate', $data['performance']['document_completion_rate'] . '%']);
        fputcsv($file, ['Complaint Resolution Rate', $data['performance']['complaint_resolution_rate'] . '%']);
        fputcsv($file, ['Permit Approval Rate', $data['performance']['permit_approval_rate'] . '%']);
        fputcsv($file, ['Avg Processing Days', $data['performance']['avg_processing_days'] . ' days']);
        fputcsv($file, []);
        
        // Service Quality
        fputcsv($file, ['SERVICE QUALITY METRICS']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['On-Time Completion', $data['serviceQuality']['on_time_completion'] . '%']);
        fputcsv($file, ['Citizen Satisfaction', $data['serviceQuality']['citizen_satisfaction'] . '%']);
        fputcsv($file, ['Digital Adoption', $data['serviceQuality']['digital_adoption'] . '%']);
        fputcsv($file, ['Staff Efficiency', $data['serviceQuality']['staff_efficiency'] . '%']);
        fputcsv($file, []);
        
        // Top Performers
        fputcsv($file, ['TOP PERFORMING BARANGAYS']);
        fputcsv($file, ['Rank', 'Barangay', 'Score', 'Verification Rate', 'Completion Rate', 'Avg Processing Days']);
        foreach ($data['topPerformers'] as $index => $performer) {
            fputcsv($file, [
                $index + 1,
                $performer['barangay']->name,
                $performer['score'],
                $performer['verification_rate'] . '%',
                $performer['completion_rate'] . '%',
                $performer['avg_processing_days'] . ' days',
            ]);
        }
        fputcsv($file, []);
        
        // Monthly Trends
        fputcsv($file, ['MONTHLY TRENDS']);
        fputcsv($file, ['Month', 'New Residents', 'Documents', 'Complaints', 'Permits']);
        foreach ($data['monthlyTrends'] as $trend) {
            fputcsv($file, [
                $trend['month'],
                $trend['new_residents'],
                $trend['documents'],
                $trend['complaints'],
                $trend['permits'],
            ]);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Generate Barangay PDF.
 */
private function generateBarangayPDF($data, $filename)
{
    $html = view('abc.exports.barangay-pdf', $data)->render();
    
    if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download($filename . '.pdf');
    }
    
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
}

/**
 * Generate Barangay Excel/CSV.
 */
private function generateBarangayExcel($data, $filename, $format)
{
    $headers = [
        'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
        'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $format . '"',
    ];

    $callback = function() use ($data) {
        $file = fopen('php://output', 'w');
        
        fputcsv($file, ['BARANGAY REPORTS']);
        fputcsv($file, ['Generated:', $data['generated_at']->format('F d, Y h:i A')]);
        fputcsv($file, ['Period:', $data['dateRange']['label']]);
        fputcsv($file, []);
        
        foreach ($data['reports'] as $report) {
            $barangay = $report['barangay'];
            $barangayData = $report['data'];
            $metrics = $report['metrics'];
            
            fputcsv($file, ['BARANGAY: ' . strtoupper($barangay->name)]);
            fputcsv($file, []);
            
            fputcsv($file, ['RESIDENT STATISTICS']);
            fputcsv($file, ['Total Residents', $barangayData['total_residents']]);
            fputcsv($file, ['RBI Total', $barangayData['rbi_total']]);
            fputcsv($file, ['RBI Verified', $barangayData['rbi_verified']]);
            fputcsv($file, ['Online Total', $barangayData['online_total']]);
            fputcsv($file, ['Online Verified', $barangayData['online_verified']]);
            fputcsv($file, ['Pending Verification', $barangayData['pending_residents']]);
            fputcsv($file, []);
            
            fputcsv($file, ['SERVICE STATISTICS']);
            fputcsv($file, ['Documents Processed', $barangayData['documents_processed']]);
            fputcsv($file, ['Complaints Received', $barangayData['complaints_received']]);
            fputcsv($file, ['Permits Processed', $barangayData['permits_processed']]);
            fputcsv($file, []);
            
            fputcsv($file, ['SERVICE METRICS']);
            fputcsv($file, ['Document Completion Rate', $metrics['document_completion_rate'] . '%']);
            fputcsv($file, ['Complaint Resolution Rate', $metrics['complaint_resolution_rate'] . '%']);
            fputcsv($file, ['Permit Approval Rate', $metrics['permit_approval_rate'] . '%']);
            fputcsv($file, ['Avg Processing Days', $metrics['avg_processing_days'] . ' days']);
            fputcsv($file, []);
            fputcsv($file, ['---']);
            fputcsv($file, []);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Generate Performance PDF.
 */
private function generatePerformancePDF($data, $filename)
{
    $html = view('abc.exports.performance-pdf', $data)->render();
    
    if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download($filename . '.pdf');
    }
    
    return response($html)
        ->header('Content-Type', 'text/html')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
}

/**
 * Generate Performance Excel/CSV.
 */
private function generatePerformanceExcel($data, $filename, $format)
{
    $headers = [
        'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel',
        'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $format . '"',
    ];

    $callback = function() use ($data) {
        $file = fopen('php://output', 'w');
        
        fputcsv($file, ['PERFORMANCE COMPARISON REPORT']);
        fputcsv($file, ['Generated:', $data['generated_at']->format('F d, Y h:i A')]);
        fputcsv($file, ['Period:', $data['dateRange']['label']]);
        fputcsv($file, []);
        
        fputcsv($file, ['OVERALL PERFORMANCE']);
        fputcsv($file, ['Document Completion Rate', $data['performance']['document_completion_rate'] . '%']);
        fputcsv($file, ['Complaint Resolution Rate', $data['performance']['complaint_resolution_rate'] . '%']);
        fputcsv($file, ['Permit Approval Rate', $data['performance']['permit_approval_rate'] . '%']);
        fputcsv($file, []);
        
        fputcsv($file, ['BARANGAY COMPARISON']);
        fputcsv($file, ['Barangay', 'Document Completion', 'Complaint Resolution', 'Permit Approval', 'Avg Processing Days']);
        
        foreach ($data['barangayComparison'] as $comparison) {
            fputcsv($file, [
                $comparison['barangay']->name,
                $comparison['metrics']['document_completion_rate'] . '%',
                $comparison['metrics']['complaint_resolution_rate'] . '%',
                $comparison['metrics']['permit_approval_rate'] . '%',
                $comparison['metrics']['avg_processing_days'] . ' days',
            ]);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}