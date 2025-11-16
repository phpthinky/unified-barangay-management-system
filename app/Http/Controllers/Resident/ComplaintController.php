<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\User;
use App\Models\Barangay;

class ComplaintController extends Controller
{
    /**
     * Display listing of user's complaints.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Complaint::where('complainant_id', $user->id)
                         ->with(['complaintType', 'barangay', 'assignedOfficial', 'hearings']);

        // Filter by workflow status
        if ($request->filled('status')) {
            $query->where('workflow_status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $complaints = $query->orderBy('created_at', 'desc')
                           ->paginate(10)
                           ->appends($request->query());

        // Statistics
        $stats = [
            'total' => Complaint::where('complainant_id', $user->id)->count(),
            'pending' => Complaint::where('complainant_id', $user->id)
                ->whereNotIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'dismissed', 'certificate_issued', 'closed'])
                ->count(),
            'resolved' => Complaint::where('complainant_id', $user->id)
                ->whereIn('workflow_status', ['settled_by_captain', 'resolved_by_lupon', 'closed'])
                ->count(),
        ];

        return view('resident.complaints.index', compact('complaints', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get complaint types
        $complaintTypes = ComplaintType::where('is_active', true)
                                      ->orderBy('name')
                                      ->get();

        return view('resident.complaints.create', compact('complaintTypes'));
    }

    /**
     * Store complaint
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate
        $validated = $request->validate([
            'complaint_type_id' => 'required|exists:complaint_types,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'incident_date' => 'nullable|date',
            'incident_location' => 'nullable|string|max:255',
            
            // Respondent info
            'respondent_type' => 'required|in:named,unknown',
            'respondent_name' => 'required_if:respondent_type,named|nullable|string|max:255',
            'respondent_alias' => 'nullable|string|max:255',
            'respondent_address' => 'nullable|string',
            'respondent_contact' => 'nullable|string|max:100',
            'respondent_description' => 'required_if:respondent_type,unknown|nullable|string',
            
            'evidence_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Generate complaint number
        $year = now()->year;
        $barangayCode = str_pad($user->barangay_id, 2, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(6));
        $complaintNumber = "CMP-{$year}-{$barangayCode}-{$random}";

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/complaints'), $filename);
                $uploadedFiles[] = $filename;
            }
        }

        // Prepare respondent data
        $respondents = [];
        
        if ($validated['respondent_type'] === 'named') {
            $respondents[] = [
                'type' => 'named',
                'name' => $validated['respondent_name'],
                'alias' => $validated['respondent_alias'] ?? '',
                'address' => $validated['respondent_address'] ?? '',
                'contact' => $validated['respondent_contact'] ?? '',
            ];
        } else {
            $respondents[] = [
                'type' => 'unknown',
                'name' => 'Unknown',
                'description' => $validated['respondent_description'],
            ];
        }

        // Determine priority based on complaint type
        $complaintType = ComplaintType::find($validated['complaint_type_id']);
        $priority = $this->determinePriority($complaintType);

        // Create complaint with workflow status
        $complaint = Complaint::create([
            'complaint_number' => $complaintNumber,
            'complainant_id' => $user->id,
            'barangay_id' => $user->barangay_id,
            'complaint_type_id' => $validated['complaint_type_id'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'incident_date' => $validated['incident_date'] ?? null,
            'incident_location' => $validated['incident_location'] ?? null,
            'uploaded_files' => $uploadedFiles,
            'respondents' => $respondents,
            'status' => 'received',
            'workflow_status' => 'pending_review',
            'priority' => $priority,
            'received_at' => now(),
        ]);

        return redirect()->route('resident.complaints.show', $complaint)
                        ->with('success', 'Complaint filed successfully! Tracking Number: ' . $complaintNumber);
    }

    /**
     * Show specific complaint
     */
    public function show(Complaint $complaint)
    {
        $user = Auth::user();

        // Check if complaint belongs to user
        if ($complaint->complainant_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $complaint->load(['complaintType', 'barangay', 'assignedOfficial', 'resolver', 'hearings']);

        return view('resident.complaints.show', compact('complaint'));
    }

    /**
     * Upload additional evidence
     */
    public function uploadEvidence(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        // Check ownership
        if ($complaint->complainant_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'evidence_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $uploadedFiles = $complaint->uploaded_files ?? [];

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/complaints'), $filename);
                $uploadedFiles[] = $filename;
            }
        }

        $complaint->update(['uploaded_files' => $uploadedFiles]);

        return redirect()->back()->with('success', 'Evidence uploaded successfully!');
    }

    /**
     * Determine priority based on complaint type
     */
    private function determinePriority($complaintType)
    {
        $highPriorityTypes = ['assault', 'theft', 'fighting', 'threats', 'property-damage'];
        
        if (in_array($complaintType->slug, $highPriorityTypes)) {
            return 'high';
        }

        return 'medium';
    }
}