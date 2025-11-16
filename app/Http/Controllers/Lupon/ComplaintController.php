<?php

namespace App\Http\Controllers\Lupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\ComplaintHearing;

class ComplaintController extends Controller
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

        $query = Complaint::where('barangay_id', $barangay->id)
            ->where('assigned_lupon_id', $user->id)
            ->with(['complainant', 'complaintType', 'barangay', 'hearings']);

        // Filter by workflow status
        if ($request->filled('status')) {
            $query->where('workflow_status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by complaint type
        if ($request->filled('complaint_type_id')) {
            $query->where('complaint_type_id', $request->complaint_type_id);
        }

        // Filter needs decision (completed hearings)
        if ($request->boolean('needs_decision')) {
            $query->whereIn('workflow_status', [
                '1st_hearing_completed', '2nd_hearing_completed', '3rd_hearing_completed'
            ]);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('complaint_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('complainant', function($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $query->orderByRaw("CASE 
                WHEN priority = 'urgent' THEN 1 
                WHEN priority = 'high' THEN 2 
                WHEN priority = 'medium' THEN 3 
                ELSE 4 END")
            ->orderBy('assigned_to_lupon_at', 'desc');

        $complaints = $query->paginate(20)->appends($request->query());

        // Get filter options
        $complaintTypes = ComplaintType::active()->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->count(),
            'for_lupon' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->where('workflow_status', 'for_lupon')
                ->count(),
            'ongoing_hearings' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->whereIn('workflow_status', [
                    '1st_hearing_ongoing', '2nd_hearing_ongoing', '3rd_hearing_ongoing'
                ])
                ->count(),
            'needs_decision' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->whereIn('workflow_status', [
                    '1st_hearing_completed', '2nd_hearing_completed', '3rd_hearing_completed'
                ])
                ->count(),
            'resolved' => Complaint::where('barangay_id', $barangay->id)
                ->where('assigned_lupon_id', $user->id)
                ->where('workflow_status', 'resolved_by_lupon')
                ->count(),
        ];

        return view('lupon.complaints.index', compact(
            'complaints', 'complaintTypes', 'stats', 'barangay'
        ));
    }

    public function show(Complaint $complaint)
    {
        $user = Auth::user();

        // Check if complaint is assigned to this lupon member
        if ($complaint->assigned_lupon_id !== $user->id) {
            abort(403, 'Access denied. This complaint is not assigned to you.');
        }

        $complaint->load([
            'complainant.residentProfile',
            'complaintType',
            'barangay',
            'hearings.presidingOfficer',
            'latestHearing'
        ]);

        // Get next actions available
        $nextActions = $this->getNextActions($complaint, $user);

        return view('lupon.complaints.show', compact('complaint', 'nextActions'));
    }

    /**
     * Schedule first/next hearing
     */
    public function scheduleHearing(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if ($complaint->assigned_lupon_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:now',
            'venue' => 'required|string|max:255',
            'agenda' => 'required|string|max:500',
            'lupon_members' => 'nullable|array',
            'lupon_members.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            // Determine hearing number
            $hearingCount = $complaint->hearings()->count();
            $hearingNumber = ($hearingCount + 1);
            
            if ($hearingNumber > 3) {
                return redirect()->back()->with('error', 'Maximum 3 hearings allowed');
            }

            // Generate hearing reference number
            $hearingRef = $complaint->complaint_number . '-H' . $hearingNumber;

            // Create hearing
            $hearing = ComplaintHearing::create([
                'complaint_id' => $complaint->id,
                'barangay_id' => $complaint->barangay_id,
                'hearing_number' => $hearingRef,
                'hearing_type' => 'lupon',
                'scheduled_date' => $validated['scheduled_date'],
                'venue' => $validated['venue'],
                'agenda' => $validated['agenda'],
                'presiding_officer' => $user->id,
                'lupon_members' => $validated['lupon_members'] ?? [],
                'status' => 'scheduled',
            ]);

            // Update complaint workflow status
            $newStatus = "{$hearingNumber}" . ($hearingNumber === 1 ? 'st' : ($hearingNumber === 2 ? 'nd' : 'rd')) . "_hearing_scheduled";
            
            $complaint->update([
                'workflow_status' => $newStatus,
                'current_hearing_number' => $hearingNumber,
            ]);

            DB::commit();
            
            return redirect()->route('lupon.hearings.show', $hearing)
                ->with('success', 'Hearing #' . $hearingNumber . ' scheduled successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to schedule hearing: ' . $e->getMessage());
        }
    }

    /**
     * Record lupon resolution
     */
    public function recordResolution(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if ($complaint->assigned_lupon_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'lupon_resolution' => 'required|string|max:5000',
            'lupon_resolution_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $complaint->update([
                'workflow_status' => 'resolved_by_lupon',
                'lupon_resolved_at' => now(),
                'lupon_resolution' => $validated['lupon_resolution'],
                'lupon_resolution_notes' => $validated['lupon_resolution_notes'] ?? null,
                'resolved_at' => now(),
                'days_in_process' => now()->diffInDays($complaint->created_at),
            ]);

            DB::commit();
            
            return redirect()->back()->with('success', 'Lupon resolution recorded successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record resolution');
        }
    }

    /**
     * Recommend certificate issuance (case cannot be resolved)
     */
    public function recommendCertificate(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if ($complaint->assigned_lupon_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'recommendation_notes' => 'required|string|max:1000',
        ]);

        $complaint->update([
            'workflow_status' => 'for_certificate',
            'lupon_resolution_notes' => $validated['recommendation_notes'],
        ]);

        return redirect()->back()->with('success', 'Recommended for certificate issuance');
    }

    /**
     * Get available next actions for complaint
     */
    private function getNextActions(Complaint $complaint, $user)
    {
        $actions = [];

        switch ($complaint->workflow_status) {
            case 'for_lupon':
                $actions[] = [
                    'label' => 'Schedule 1st Hearing',
                    'route' => 'lupon.complaints.schedule-hearing',
                    'color' => 'primary'
                ];
                break;

            case '1st_hearing_completed':
            case '2nd_hearing_completed':
                $hearingNum = (int) substr($complaint->workflow_status, 0, 1);
                if ($hearingNum < 3) {
                    $actions[] = [
                        'label' => 'Schedule Next Hearing',
                        'route' => 'lupon.complaints.schedule-hearing',
                        'color' => 'warning'
                    ];
                }
                $actions[] = [
                    'label' => 'Record Resolution',
                    'route' => 'lupon.complaints.record-resolution',
                    'color' => 'success'
                ];
                $actions[] = [
                    'label' => 'Recommend Certificate',
                    'route' => 'lupon.complaints.recommend-certificate',
                    'color' => 'danger'
                ];
                break;

            case '3rd_hearing_completed':
                $actions[] = [
                    'label' => 'Record Resolution',
                    'route' => 'lupon.complaints.record-resolution',
                    'color' => 'success'
                ];
                $actions[] = [
                    'label' => 'Recommend Certificate',
                    'route' => 'lupon.complaints.recommend-certificate',
                    'color' => 'danger'
                ];
                break;
        }

        return $actions;
    }
}