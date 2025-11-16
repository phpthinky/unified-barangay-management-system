<?php

namespace App\Http\Controllers\Lupon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ComplaintHearing;
use App\Models\Complaint;

class HearingController extends Controller
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

        $query = ComplaintHearing::where('barangay_id', $barangay->id)
            ->where(function($q) use ($user) {
                $q->where('presiding_officer', $user->id)
                    ->orWhereJsonContains('lupon_members', $user->id);
            })
            ->with(['complaint.complainant', 'complaint.complaintType', 'presidingOfficer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by hearing type
        if ($request->filled('hearing_type')) {
            $query->where('hearing_type', $request->hearing_type);
        }

        // Filter upcoming
        if ($request->boolean('upcoming')) {
            $query->where('status', 'scheduled')
                ->whereBetween('scheduled_date', [now(), now()->addDays(7)]);
        }

        // Filter pending docs
        if ($request->boolean('pending_docs')) {
            $query->where('status', 'completed')
                ->whereNull('minutes')
                ->where('actual_end_time', '<', now()->subHours(24));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hearing_number', 'like', "%{$search}%")
                    ->orWhereHas('complaint', function($complaintQuery) use ($search) {
                        $complaintQuery->where('complaint_number', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $query->orderBy('scheduled_date', 'desc');

        $hearings = $query->paginate(20)->appends($request->query());

        // Statistics
        $stats = [
            'total' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where(function($q) use ($user) {
                    $q->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->count(),
            'scheduled' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('status', 'scheduled')
                ->where(function($q) use ($user) {
                    $q->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->count(),
            'today' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->whereDate('scheduled_date', today())
                ->where(function($q) use ($user) {
                    $q->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->count(),
            'completed' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('status', 'completed')
                ->where(function($q) use ($user) {
                    $q->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->count(),
            'pending_docs' => ComplaintHearing::where('barangay_id', $barangay->id)
                ->where('status', 'completed')
                ->whereNull('minutes')
                ->where(function($q) use ($user) {
                    $q->where('presiding_officer', $user->id)
                        ->orWhereJsonContains('lupon_members', $user->id);
                })
                ->count(),
        ];

        return view('lupon.hearings.index', compact('hearings', 'stats', 'barangay'));
    }

    public function show(ComplaintHearing $hearing)
    {
        $user = Auth::user();

        // Check if user is part of this hearing
        if ($hearing->presiding_officer !== $user->id && 
            !in_array($user->id, $hearing->lupon_members ?? [])) {
            abort(403, 'Access denied. You are not part of this hearing.');
        }

        $hearing->load([
            'complaint.complainant',
            'complaint.complaintType',
            'presidingOfficer',
            'barangay'
        ]);

        return view('lupon.hearings.show', compact('hearing'));
    }

    /**
     * Start a hearing
     */
    public function start(Request $request, ComplaintHearing $hearing)
    {
        $user = Auth::user();

        // Only presiding officer can start
        if ($hearing->presiding_officer !== $user->id) {
            abort(403, 'Only the presiding officer can start the hearing.');
        }

        if ($hearing->status !== 'scheduled') {
            return redirect()->back()->with('error', 'Hearing cannot be started in its current status.');
        }

        $validated = $request->validate([
            'attendees' => 'required|array',
            'attendees.*' => 'string|max:255',
            'absent_parties' => 'nullable|array',
            'absent_parties.*' => 'string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $hearing->update([
                'status' => 'ongoing',
                'actual_start_time' => now(),
                'attendees' => $validated['attendees'],
                'absent_parties' => $validated['absent_parties'] ?? [],
            ]);

            // Update complaint workflow status
            $hearingNum = $hearing->complaint->current_hearing_number;
            $suffix = $hearingNum === 1 ? 'st' : ($hearingNum === 2 ? 'nd' : 'rd');
            $hearing->complaint->update([
                'workflow_status' => "{$hearingNum}{$suffix}_hearing_ongoing"
            ]);

            DB::commit();
            
            return redirect()->back()->with('success', 'Hearing started successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to start hearing');
        }
    }

    /**
     * Complete a hearing
     */
    public function complete(Request $request, ComplaintHearing $hearing)
    {
        $user = Auth::user();

        // Only presiding officer can complete
        if ($hearing->presiding_officer !== $user->id) {
            abort(403, 'Only the presiding officer can complete the hearing.');
        }

        if ($hearing->status !== 'ongoing') {
            return redirect()->back()->with('error', 'Hearing must be ongoing to complete.');
        }

        $validated = $request->validate([
            'minutes' => 'required|string|max:5000',
            'outcome' => 'required|in:settled,mediated,postponed,no_settlement,needs_next_hearing',
            'resolution' => 'nullable|string|max:2000',
            'agreements_reached' => 'nullable|array',
            'agreements_reached.*' => 'string|max:500',
            'next_steps' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $hearing->update([
                'status' => 'completed',
                'actual_end_time' => now(),
                'minutes' => $validated['minutes'],
                'outcome' => $validated['outcome'],
                'resolution' => $validated['resolution'],
                'agreements_reached' => $validated['agreements_reached'],
                'next_steps' => $validated['next_steps'],
            ]);

            // Update complaint workflow status
            $complaint = $hearing->complaint;
            $hearingNum = $complaint->current_hearing_number;
            $suffix = $hearingNum === 1 ? 'st' : ($hearingNum === 2 ? 'nd' : 'rd');
            
            if (in_array($validated['outcome'], ['settled', 'mediated'])) {
                // Case resolved
                $complaint->update([
                    'workflow_status' => 'resolved_by_lupon',
                    'lupon_resolved_at' => now(),
                    'lupon_resolution' => $validated['resolution'],
                    'resolved_at' => now(),
                    'days_in_process' => now()->diffInDays($complaint->created_at),
                ]);
            } else {
                // Hearing completed, awaiting decision
                $complaint->update([
                    'workflow_status' => "{$hearingNum}{$suffix}_hearing_completed",
                    'total_hearings_conducted' => ($complaint->total_hearings_conducted ?? 0) + 1,
                ]);
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'Hearing completed successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete hearing');
        }
    }

    /**
     * Upload hearing minutes/documents
     */
    public function uploadMinutes(Request $request, ComplaintHearing $hearing)
    {
        $user = Auth::user();

        // Check if user is part of this hearing
        if ($hearing->presiding_officer !== $user->id && 
            !in_array($user->id, $hearing->lupon_members ?? [])) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,doc,docx|max:5120'
        ]);

        $uploadedDocuments = $hearing->uploaded_documents ?? [];

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = 'hearing_' . $hearing->hearing_number . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/hearings'), $filename);
                
                $uploadedDocuments[] = [
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'uploaded_by' => $user->name,
                    'uploaded_at' => now()->toDateTimeString()
                ];
            }

            $hearing->update(['uploaded_documents' => $uploadedDocuments]);
        }

        return redirect()->back()->with('success', 'Documents uploaded successfully');
    }

    /**
     * Postpone hearing
     */
    public function postpone(Request $request, ComplaintHearing $hearing)
    {
        $user = Auth::user();

        // Only presiding officer can postpone
        if ($hearing->presiding_officer !== $user->id) {
            abort(403, 'Only the presiding officer can postpone the hearing.');
        }

        if (!in_array($hearing->status, ['scheduled', 'ongoing'])) {
            return redirect()->back()->with('error', 'Hearing cannot be postponed in its current status.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'new_date' => 'required|date|after:now'
        ]);

        $hearing->update([
            'status' => 'postponed',
            'scheduled_date' => $validated['new_date'],
            'next_steps' => "POSTPONED: " . $validated['reason']
        ]);

        return redirect()->back()->with('success', 'Hearing postponed successfully');
    }
}