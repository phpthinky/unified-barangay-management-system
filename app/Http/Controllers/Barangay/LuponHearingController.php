<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintHearing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LuponHearingController extends Controller
{
    /**
     * Schedule hearing from workflow
     */
    public function schedule(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);
        
        $validated = $request->validate([
            'scheduled_date' => 'required|date|after:today',
            'venue' => 'required|string|max:255',
            'presiding_officer' => 'required|exists:users,id',
            'lupon_members' => 'required|array|min:2',
            'lupon_members.*' => 'exists:users,id',
            'agenda' => 'nullable|string|max:1000',
        ]);
        
        DB::beginTransaction();
        try {
            // Determine hearing number
            $hearingNumber = $complaint->current_hearing_number + 1;
            
            if ($hearingNumber > 3) {
                return redirect()->back()->with('error', 'Maximum 3 hearings allowed. Please issue certificate.');
            }
            
            // Create hearing record
            $hearing = ComplaintHearing::create([
                'complaint_id' => $complaint->id,
                'barangay_id' => $complaint->barangay_id,
                'hearing_number' => $hearingNumber,
                'hearing_type' => 'lupon',
                'scheduled_date' => $validated['scheduled_date'],
                'venue' => $validated['venue'],
                'presiding_officer' => $validated['presiding_officer'],
                'lupon_members' => $validated['lupon_members'],
                'agenda' => $validated['agenda'] ?? null,
                'status' => 'scheduled',
            ]);
            
            // Update complaint workflow
            $statusMap = [
                1 => '1st_hearing_scheduled',
                2 => '2nd_hearing_scheduled',
                3 => '3rd_hearing_scheduled',
            ];
            
            $complaint->update([
                'workflow_status' => $statusMap[$hearingNumber],
                'current_hearing_number' => $hearingNumber,
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', "Hearing #{$hearingNumber} scheduled successfully");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to schedule hearing: ' . $e->getMessage());
        }
    }
    
    /**
     * Start hearing
     */
    public function start(Request $request, ComplaintHearing $hearing)
    {
        $this->authorize('update', $hearing->complaint);
        
        $statusMap = [
            1 => '1st_hearing_ongoing',
            2 => '2nd_hearing_ongoing',
            3 => '3rd_hearing_ongoing',
        ];
        
        DB::beginTransaction();
        try {
            $hearing->update([
                'status' => 'ongoing',
                'actual_start_time' => now(),
            ]);
            
            $hearing->complaint->update([
                'workflow_status' => $statusMap[$hearing->hearing_number],
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Hearing started');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to start hearing');
        }
    }
    
    /**
     * Complete hearing
     */
    public function complete(Request $request, ComplaintHearing $hearing)
    {
        $this->authorize('update', $hearing->complaint);
        
        $validated = $request->validate([
            'outcome' => 'required|in:resolved,needs_next_hearing,refer_to_certificate',
            'minutes' => 'required|string',
            'resolution' => 'nullable|string',
            'agreements_reached' => 'nullable|array',
            'attendees' => 'nullable|array',
            'absent_parties' => 'nullable|array',
        ]);
        
        DB::beginTransaction();
        try {
            $statusMap = [
                1 => '1st_hearing_completed',
                2 => '2nd_hearing_completed',
                3 => '3rd_hearing_completed',
            ];
            
            $hearing->update([
                'status' => 'completed',
                'actual_end_time' => now(),
                'outcome' => $validated['outcome'],
                'minutes' => $validated['minutes'],
                'resolution' => $validated['resolution'] ?? null,
                'agreements_reached' => $validated['agreements_reached'] ?? null,
                'attendees' => $validated['attendees'] ?? null,
                'absent_parties' => $validated['absent_parties'] ?? null,
            ]);
            
            $complaint = $hearing->complaint;
            $complaint->update([
                'workflow_status' => $statusMap[$hearing->hearing_number],
                'total_hearings_conducted' => $complaint->total_hearings_conducted + 1,
            ]);
            
            // Handle outcome
            if ($validated['outcome'] === 'resolved') {
                $complaint->update([
                    'workflow_status' => 'resolved_by_lupon',
                    'status' => 'resolved',
                    'lupon_resolved_at' => now(),
                    'lupon_resolution' => $validated['resolution'],
                    'resolved_at' => now(),
                    'closed_at' => now(),
                    'days_in_process' => now()->diffInDays($complaint->created_at),
                ]);
            } elseif ($validated['outcome'] === 'needs_next_hearing') {
                if ($hearing->hearing_number >= 3) {
                    $complaint->update(['workflow_status' => 'for_certificate']);
                } else {
                    $complaint->update(['workflow_status' => 'for_lupon']);
                }
            } elseif ($validated['outcome'] === 'refer_to_certificate') {
                $complaint->update(['workflow_status' => 'for_certificate']);
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Hearing completed successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete hearing');
        }
    }
    
    /**
     * View hearing details
     */
    public function show(ComplaintHearing $hearing)
    {
        $this->authorize('view', $hearing->complaint);
        
        $hearing->load(['complaint', 'presidingOfficer']);
        
        return view('barangay.complaints-workflow.hearing-show', compact('hearing'));
    }
}