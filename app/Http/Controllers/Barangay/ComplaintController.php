<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\User;

class ComplaintController extends Controller
{
    /**
     * Display complaints list
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        $query = Complaint::where('barangay_id', $user->barangay_id)
                         ->with(['complainant', 'complaintType', 'assignedOfficial']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assignment
        if ($request->filled('assigned')) {
            if ($request->assigned === 'unassigned') {
                $query->unassigned();
            } elseif ($request->assigned === 'assigned_to_me') {
                $query->assignedTo($user->id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $complaints = $query->orderBy('received_at', 'desc')
                           ->paginate(15)
                           ->appends($request->query());

        // Statistics
        $stats = [
            'total' => Complaint::where('barangay_id', $user->barangay_id)->count(),
            'pending' => Complaint::where('barangay_id', $user->barangay_id)->pending()->count(),
            'high_priority' => Complaint::where('barangay_id', $user->barangay_id)->highPriority()->count(),
            'unassigned' => Complaint::where('barangay_id', $user->barangay_id)->unassigned()->count(),
            'assigned_to_me' => Complaint::where('barangay_id', $user->barangay_id)->assignedTo($user->id)->count(),
        ];

        return view('barangay.complaints.index', compact('complaints', 'stats', 'barangay'));
    }

    /**
     * Show specific complaint
     */
    public function show(Complaint $complaint)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        // Check if complaint belongs to this barangay
        if ($complaint->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $complaint->load([
            'complainant',
            'complaintType',
            'barangay',
            'assignedOfficial',
            'resolver'
        ]);

        // Get residents for linking unregistered respondents
        $residents = User::where('barangay_id', $user->barangay_id)
                        ->whereHas('roles', function($q) {
                            $q->where('name', 'resident');
                        })
                        ->orderBy('name')
                        ->get();

        return view('barangay.complaints.show', compact('complaint', 'residents', 'barangay'));
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Check access
        if ($complaint->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'status' => 'required|in:received,assigned,in_process,mediation,hearing_scheduled,resolved,closed,dismissed',
            'notes' => 'nullable|string',
        ]);

        $complaint->status = $validated['status'];

        // Update timestamps based on status
        if ($validated['status'] === 'resolved' && !$complaint->resolved_at) {
            $complaint->resolved_at = now();
            $complaint->resolved_by = $user->id;
        }

        if ($validated['status'] === 'closed' && !$complaint->closed_at) {
            $complaint->closed_at = now();
        }

        // Add notes to resolution details
        if ($validated['notes']) {
            $existingNotes = $complaint->resolution_details ?? '';
            $newNote = "[" . now()->format('Y-m-d H:i') . " by {$user->name}] " . $validated['notes'];
            $complaint->resolution_details = $existingNotes ? $existingNotes . "\n\n" . $newNote : $newNote;
        }

        $complaint->save();

        return back()->with('success', 'Complaint status updated successfully.');
    }

    /**
     * Assign complaint to official
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Check access
        if ($complaint->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'assigned_role' => 'required|in:captain,secretary,lupon,staff',
            'assignment_notes' => 'nullable|string',
        ]);

        $complaint->update([
            'assigned_to' => $validated['assigned_to'],
            'assigned_role' => $validated['assigned_role'],
            'assignment_notes' => $validated['assignment_notes'],
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        // TODO: Send notification to assigned user

        return back()->with('success', 'Complaint assigned successfully.');
    }

    /**
     * Link unregistered respondent to registered user
     */
    public function linkRespondent(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Check access
        if ($complaint->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'respondent_index' => 'required|integer|min:0',
            'respondent_id' => 'required|exists:users,id',
        ]);

        $complaint->linkRespondentToUser(
            $validated['respondent_index'],
            $validated['respondent_id']
        );

        return back()->with('success', 'Respondent linked to registered resident successfully.');
    }

    /**
     * Get officials by role (for AJAX)
     */
    public function getOfficialsByRole($role)
    {
        $user = Auth::user();

        $roleMapping = [
            'captain' => 'barangay-captain',
            'secretary' => 'barangay-secretary',
            'lupon' => 'lupon',
            'staff' => 'barangay-staff',
        ];

        $roleName = $roleMapping[$role] ?? $role;

        $officials = User::where('barangay_id', $user->barangay_id)
                        ->whereHas('roles', function($q) use ($roleName) {
                            $q->where('name', $roleName);
                        })
                        ->select('id', 'name', 'position_title')
                        ->get();

        return response()->json($officials);
    }

    /**
     * Resolve complaint
     */
    public function resolve(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Check access
        if ($complaint->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'resolution_type' => 'required|in:settled,dismissed,referred,mediated',
            'resolution_details' => 'required|string',
        ]);

        $complaint->update([
            'status' => 'resolved',
            'resolution_type' => $validated['resolution_type'],
            'resolution_details' => $validated['resolution_details'],
            'resolved_at' => now(),
            'resolved_by' => $user->id,
        ]);

        return back()->with('success', 'Complaint marked as resolved.');
    }
}