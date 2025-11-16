<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintWorkflowController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display complaints dashboard with workflow stages
     */
public function index(Request $request)
{
    $barangay = auth()->user()->barangay;
    
    $stats = [
        'pending_review' => Complaint::byBarangay($barangay->id)->where('workflow_status', 'pending_review')->count(),
        'for_captain_review' => Complaint::byBarangay($barangay->id)->where('workflow_status', 'for_captain_review')->count(),
        'summons_issued' => Complaint::byBarangay($barangay->id)->whereIn('workflow_status', ['1st_summons_issued', '2nd_summons_issued', '3rd_summons_issued'])->count(),
        'captain_mediation' => Complaint::byBarangay($barangay->id)->where('workflow_status', 'captain_mediation')->count(),
        'lupon_hearing' => Complaint::byBarangay($barangay->id)->whereIn('workflow_status', ['1st_hearing_scheduled', '1st_hearing_ongoing', '2nd_hearing_scheduled', '2nd_hearing_ongoing', '3rd_hearing_scheduled', '3rd_hearing_ongoing'])->count(),
        'settled' => Complaint::byBarangay($barangay->id)->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon'])->count(),
    ];
    
    // Build query with proper filtering
    $query = Complaint::byBarangay($barangay->id)
        ->with(['complainant', 'complaintType', 'assignedOfficial']);
    
    // Apply status filter if present
    if ($request->has('status') && $request->status) {
        $status = $request->status;
        
        // Handle grouped statuses
        if ($status === 'summons') {
            $query->whereIn('workflow_status', ['1st_summons_issued', '2nd_summons_issued', '3rd_summons_issued']);
        } elseif ($status === 'hearing') {
            $query->whereIn('workflow_status', ['1st_hearing_scheduled', '1st_hearing_ongoing', '2nd_hearing_scheduled', '2nd_hearing_ongoing', '3rd_hearing_scheduled', '3rd_hearing_ongoing']);
        } else {
            $query->where('workflow_status', $status);
        }
    }
    
    $complaints = $query->latest()->paginate(20);
    
    return view('barangay.complaints.workflow.index', compact('complaints', 'stats'));
}
    
    /**
     * Show complaint details with workflow timeline
     */
    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        
        $complaint->load(['complainant', 'complaintType', 'assignedOfficial', 'hearings']);
        
        $timeline = $this->buildTimeline($complaint);
        $nextActions = $this->getNextActions($complaint);
        
        return view('barangay.complaints.workflow.show', compact('complaint', 'timeline', 'nextActions'));
    }
    
    /**
     * SECRETARY: Review complaint and prepare for captain
     */
    /**
/**
     * SECRETARY: Review complaint and prepare for captain (WITH RESPONDENT VERIFICATION)
     */
    public function secretaryReview(Request $request, Complaint $complaint)
    {
        // Check barangay access
        if (auth()->user()->barangay_id !== $complaint->barangay_id) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'recommendation' => 'required|in:proceed,needs_info,dismiss',
            'respondents' => 'required|array',
            'respondents.*.verification_status' => 'required|in:verified,partial,unverified,not_found',
            'respondents.*.verified_name' => 'nullable|string|max:255',
            'respondents.*.verified_address' => 'nullable|string',
            'respondents.*.verified_contact' => 'nullable|string|max:100',
            'respondents.*.linked_user_id' => 'nullable|exists:users,id',
            'respondents.*.linked_rbi_id' => 'nullable|exists:barangay_inhabitants,id',
            'respondents.*.identified_name' => 'nullable|string|max:255',
            'respondents.*.additional_info' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update respondent information with verification
            $currentRespondents = $complaint->respondents;
            foreach ($validated['respondents'] as $index => $respondentData) {
                if (isset($currentRespondents[$index])) {
                    // Merge verification data
                    $currentRespondents[$index]['verification_status'] = $respondentData['verification_status'];
                    $currentRespondents[$index]['verified_by_secretary'] = true;
                    $currentRespondents[$index]['verified_at'] = now()->toDateTimeString();
                    
                    // Add verified details
                    if (!empty($respondentData['verified_name'])) {
                        $currentRespondents[$index]['verified_name'] = $respondentData['verified_name'];
                    }
                    if (!empty($respondentData['verified_address'])) {
                        $currentRespondents[$index]['verified_address'] = $respondentData['verified_address'];
                    }
                    if (!empty($respondentData['verified_contact'])) {
                        $currentRespondents[$index]['verified_contact'] = $respondentData['verified_contact'];
                    }
                    
                    // Link to RBI or User if found
                    if (!empty($respondentData['linked_user_id'])) {
                        $currentRespondents[$index]['linked_user_id'] = $respondentData['linked_user_id'];
                    }
                    if (!empty($respondentData['linked_rbi_id'])) {
                        $currentRespondents[$index]['linked_rbi_id'] = $respondentData['linked_rbi_id'];
                    }
                    
                    // For unknown respondents - identified info
                    if (!empty($respondentData['identified_name'])) {
                        $currentRespondents[$index]['identified_name'] = $respondentData['identified_name'];
                    }
                    if (!empty($respondentData['additional_info'])) {
                        $currentRespondents[$index]['additional_info'] = $respondentData['additional_info'];
                    }
                }
            }
            
            // Update complaint
            $complaint->update([
                'workflow_status' => 'for_captain_review',
                'secretary_reviewed_at' => now(),
                'secretary_reviewed_by' => auth()->id(),
                'secretary_notes' => $validated['notes'] ?? null,
                'respondents' => $currentRespondents,
            ]);
            
            // Add metadata about recommendation
            if ($validated['recommendation'] === 'dismiss') {
                $complaint->update([
                    'secretary_notes' => ($validated['notes'] ?? '') . "\n\n[Secretary Recommendation: DISMISS CASE]"
                ]);
            } elseif ($validated['recommendation'] === 'needs_info') {
                $complaint->update([
                    'secretary_notes' => ($validated['notes'] ?? '') . "\n\n[Secretary Recommendation: REQUEST MORE INFORMATION]"
                ]);
            }
            
            DB::commit();
            
            \Log::info('Secretary review completed', [
                'complaint_id' => $complaint->id,
                'secretary_id' => auth()->id(),
                'recommendation' => $validated['recommendation']
            ]);
            
            return redirect()->route('barangay.complaints-workflow.show', $complaint)
                ->with('success', 'Complaint reviewed and prepared for Captain');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Secretary review failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }
    
    /**
     * Print complaint report for captain review
     */
    public function printReport(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        
        $complaint->load(['complainant', 'complaintType', 'barangay']);
        
        return view('barangay.complaints.workflow.print-report', compact('complaint'));
    }
    
    /**
     * CAPTAIN: Approve or dismiss complaint
     */
    public function captainDecision(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'decision' => 'required|in:approve,dismiss',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            if ($validated['decision'] === 'approve') {
                $complaint->update([
                    'workflow_status' => 'approved',
                    'captain_approved_at' => now(),
                    'captain_approved_by' => auth()->id(),
                    'captain_notes' => $validated['notes'] ?? null,
                ]);
                $message = 'Complaint approved. Ready to issue summons.';
            } else {
                $complaint->update([
                    'workflow_status' => 'dismissed',
                    'status' => 'closed',
                    'captain_approved_at' => now(),
                    'captain_approved_by' => auth()->id(),
                    'captain_notes' => $validated['notes'] ?? null,
                    'closed_at' => now(),
                ]);
                $message = 'Complaint dismissed.';
            }
            
            DB::commit();
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process decision');
        }
    }
    
    /**
     * STAFF: Issue summons (1st, 2nd, or 3rd)
     */
    public function issueSummons(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'return_date' => 'required|date|after:today',
        ]);
        
        $currentAttempt = $complaint->summons_attempt + 1;
        
        if ($currentAttempt > 3) {
            return redirect()->back()->with('error', 'Maximum 3 summons attempts reached');
        }
        
        $statusMap = [1 => '1st_summons_issued', 2 => '2nd_summons_issued', 3 => '3rd_summons_issued'];
        
        $complaint->update([
            'workflow_status' => $statusMap[$currentAttempt],
            'summons_attempt' => $currentAttempt,
            "summons_{$currentAttempt}_issued_date" => now(),
            "summons_{$currentAttempt}_return_date" => $validated['return_date'],
        ]);
        
        return redirect()->back()->with('success', "Summons #{$currentAttempt} issued successfully");
    }
    
    /**
     * STAFF: Record respondent appearance
     */
    public function recordAppearance(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'appeared' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validated['appeared']) {
            $complaint->update([
                'workflow_status' => 'respondent_appeared',
                'respondent_appeared_at' => now(),
                'appearance_notes' => $validated['notes'] ?? null,
            ]);
            $message = 'Respondent appearance recorded. Ready for mediation.';
        } else {
            if ($complaint->summons_attempt >= 3) {
                $complaint->update([
                    'workflow_status' => 'summons_failed',
                    'summons_all_failed' => true,
                ]);
                $message = 'All summons attempts failed. Ready to issue certificate.';
            } else {
                $message = 'No-show recorded. Issue next summons.';
            }
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * CAPTAIN: Start mediation (15-day period)
     */
    public function startMediation(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $deadline = now()->addDays(15);
        
        $complaint->update([
            'workflow_status' => 'captain_mediation',
            'captain_mediation_start' => now(),
            'captain_mediation_deadline' => $deadline,
        ]);
        
        return redirect()->back()->with('success', 'Captain mediation started. 15-day deadline: ' . $deadline->format('M d, Y'));
    }
    
    /**
     * CAPTAIN: Record settlement
     */
    public function recordSettlement(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'settlement_terms' => 'required|string|max:5000',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            $complaint->update([
                'workflow_status' => 'settled_by_captain',
                'status' => 'resolved',
                'settled_by_captain_at' => now(),
                'settlement_terms' => $validated['settlement_terms'],
                'captain_mediation_notes' => $validated['notes'] ?? null,
                'resolved_at' => now(),
                'closed_at' => now(),
                'days_in_process' => now()->diffInDays($complaint->created_at),
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Settlement recorded. Case resolved!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record settlement');
        }
    }
    
    /**
     * CAPTAIN: Assign to Lupon (mediation failed)
     */
    public function assignToLupon(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'lupon_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $complaint->update([
            'workflow_status' => 'for_lupon',
            'assigned_to_lupon_at' => now(),
            'assigned_lupon_id' => $validated['lupon_id'],
            'lupon_assignment_notes' => $validated['notes'] ?? null,
        ]);
        
        return redirect()->back()->with('success', 'Complaint assigned to Lupon for hearing');
    }
    
    /**
     * STAFF: Issue Certificate to File Action
     */
    public function issueCertificate(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'referred_to' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $certificateNumber = 'CTFA-' . $complaint->barangay_id . '-' . now()->format('Y') . '-' . str_pad($complaint->id, 5, '0', STR_PAD_LEFT);
        
        DB::beginTransaction();
        try {
            $complaint->update([
                'workflow_status' => 'certificate_issued',
                'status' => 'closed',
                'certificate_issued_at' => now(),
                'certificate_number' => $certificateNumber,
                'referred_to' => $validated['referred_to'],
                'certificate_notes' => $validated['notes'] ?? null,
                'closed_at' => now(),
                'days_in_process' => now()->diffInDays($complaint->created_at),
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Certificate issued: ' . $certificateNumber);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to issue certificate');
        }
    }
    
    /**
     * Build timeline array for display
     */
    private function buildTimeline(Complaint $complaint)
    {
        $timeline = [];
        
        $timeline[] = ['date' => $complaint->created_at, 'icon' => '📄', 'title' => 'Complaint Filed', 'desc' => 'By: ' . $complaint->complainant->name];
        
        if ($complaint->secretary_reviewed_at) {
            $timeline[] = ['date' => $complaint->secretary_reviewed_at, 'icon' => '👁️', 'title' => 'Reviewed by Secretary', 'desc' => User::find($complaint->secretary_reviewed_by)->name ?? 'Secretary'];
        }
        
        if ($complaint->captain_approved_at) {
            $icon = $complaint->workflow_status === 'dismissed' ? '❌' : '✅';
            $title = $complaint->workflow_status === 'dismissed' ? 'Dismissed by Captain' : 'Approved by Captain';
            $timeline[] = ['date' => $complaint->captain_approved_at, 'icon' => $icon, 'title' => $title, 'desc' => User::find($complaint->captain_approved_by)->name ?? 'Captain'];
        }
        
        for ($i = 1; $i <= 3; $i++) {
            if ($complaint->{"summons_{$i}_issued_date"}) {
                $timeline[] = ['date' => $complaint->{"summons_{$i}_issued_date"}, 'icon' => '📨', 'title' => "Summons #{$i} Issued", 'desc' => 'Return date: ' . $complaint->{"summons_{$i}_return_date"}?->format('M d, Y')];
            }
        }
        
        if ($complaint->respondent_appeared_at) {
            $timeline[] = ['date' => $complaint->respondent_appeared_at, 'icon' => '✅', 'title' => 'Respondent Appeared', 'desc' => ''];
        }
        
        if ($complaint->captain_mediation_start) {
            $timeline[] = ['date' => $complaint->captain_mediation_start, 'icon' => '🤝', 'title' => 'Captain Mediation Started', 'desc' => 'Deadline: ' . $complaint->captain_mediation_deadline?->format('M d, Y')];
        }
        
        if ($complaint->settled_by_captain_at) {
            $timeline[] = ['date' => $complaint->settled_by_captain_at, 'icon' => '✅', 'title' => 'Settlement Reached', 'desc' => 'Case RESOLVED'];
        }
        
        if ($complaint->assigned_to_lupon_at) {
            $timeline[] = ['date' => $complaint->assigned_to_lupon_at, 'icon' => '⚖️', 'title' => 'Assigned to Lupon', 'desc' => User::find($complaint->assigned_lupon_id)->name ?? ''];
        }
        
        if ($complaint->certificate_issued_at) {
            $timeline[] = ['date' => $complaint->certificate_issued_at, 'icon' => '📜', 'title' => 'Certificate Issued', 'desc' => $complaint->certificate_number];
        }
        
        return collect($timeline)->sortBy('date');
    }
    
    /**
     * Get available next actions based on workflow status
     */
    private function getNextActions(Complaint $complaint)
    {
        $user = auth()->user();
        $actions = [];
        
        switch ($complaint->workflow_status) {
            case 'pending_review':
                if ($user->hasRole(['barangay-secretary', 'barangay-staff'])) {
                    $actions[] = ['label' => 'Prepare for Captain Review', 'route' => 'barangay.complaints-workflow.secretary-review', 'color' => 'primary'];
                }
                break;
                
            case 'for_captain_review':
                if ($user->isBarangayCaptain()) {
                    $actions[] = ['label' => 'Approve', 'route' => 'barangay.complaints-workflow.captain-decision', 'color' => 'success'];
                    $actions[] = ['label' => 'Dismiss', 'route' => 'barangay.complaints-workflow.captain-decision', 'color' => 'danger'];
                }
                break;
                
            case 'approved':
                if ($user->isBarangayStaff()) {
                    $actions[] = ['label' => 'Issue 1st Summons', 'route' => 'barangay.complaints-workflow.issue-summons', 'color' => 'primary'];
                }
                break;
                
            case '1st_summons_issued':
            case '2nd_summons_issued':
            case '3rd_summons_issued':
                if ($user->isBarangayStaff()) {
                    $actions[] = ['label' => 'Respondent Appeared', 'route' => 'barangay.complaints-workflow.record-appearance', 'color' => 'success'];
                    if ($complaint->summons_attempt < 3) {
                        $actions[] = ['label' => 'No Show - Issue Next Summons', 'route' => 'barangay.complaints-workflow.issue-summons', 'color' => 'warning'];
                    } else {
                        $actions[] = ['label' => 'All Summons Failed', 'route' => 'barangay.complaints-workflow.record-appearance', 'color' => 'danger'];
                    }
                }
                break;
                
            case 'respondent_appeared':
                if ($user->isBarangayCaptain()) {
                    $actions[] = ['label' => 'Start Captain Mediation', 'route' => 'barangay.complaints-workflow.start-mediation', 'color' => 'primary'];
                }
                break;
                
            case 'captain_mediation':
                if ($user->isBarangayCaptain()) {
                    $actions[] = ['label' => 'Record Settlement', 'route' => 'barangay.complaints-workflow.record-settlement', 'color' => 'success'];
                    $actions[] = ['label' => 'Assign to Lupon', 'route' => 'barangay.complaints-workflow.assign-lupon', 'color' => 'warning'];
                }
                break;
                
            case 'summons_failed':
            /*
            case 'for_lupon':
                if ($user->isBarangayStaff()) {
                    $actions[] = ['label' => 'Issue Certificate to File Action', 'route' => 'barangay.complaints-workflow.issue-certificate', 'color' => 'danger'];
                }
                break;*/
                // Add to getNextActions() method

                case 'for_lupon':
                    if ($user->hasRole(['barangay-captain', 'barangay-secretary'])) {
                        $actions[] = ['label' => 'Schedule Hearing', 'route' => 'barangay.complaints-workflow.schedule-hearing', 'color' => 'primary'];
                    }
                    break;

                case '1st_hearing_scheduled':
                case '2nd_hearing_scheduled':
                case '3rd_hearing_scheduled':
                    if ($user->hasAnyRole(['barangay-captain', 'lupon'])) {
                        $actions[] = ['label' => 'Start Hearing', 'route' => 'barangay.complaints-workflow.hearing-start', 'color' => 'success'];
                    }
                    break;

                case '1st_hearing_ongoing':
                case '2nd_hearing_ongoing':
                case '3rd_hearing_ongoing':
                    if ($user->hasAnyRole(['barangay-captain', 'lupon'])) {
                        $actions[] = ['label' => 'Complete Hearing', 'route' => 'barangay.complaints-workflow.hearing-complete', 'color' => 'primary'];
                    }
                    break;

                case '1st_hearing_completed':
                case '2nd_hearing_completed':
                    if ($user->hasRole(['barangay-captain', 'barangay-secretary'])) {
                        $actions[] = ['label' => 'Schedule Next Hearing', 'route' => 'barangay.complaints-workflow.schedule-hearing', 'color' => 'warning'];
                        $actions[] = ['label' => 'Issue Certificate', 'route' => 'barangay.complaints-workflow.issue-certificate', 'color' => 'danger'];
                    }
                    break;

                case '3rd_hearing_completed':
                    if ($user->isBarangayStaff()) {
                        $actions[] = ['label' => 'Issue Certificate', 'route' => 'barangay.complaints-workflow.issue-certificate', 'color' => 'danger'];
                    }
                    break;
        }
        
        return $actions;
    }
}