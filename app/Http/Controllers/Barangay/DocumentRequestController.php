<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentRequest;
use App\Models\DocumentType;

class DocumentRequestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'barangay.staff', 'barangay.scope']);
    }

    /**
     * Display listing of document requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        $query = DocumentRequest::byBarangay($barangay->id)
                               ->with(['user', 'documentType', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type_id', $request->document_type);
        }

        // Search by tracking number or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter overdue requests
        if ($request->boolean('overdue')) {
            $query->where('status', 'pending')
                  ->where('submitted_at', '<', now()->subDays(5));
        }

        // Sort by priority (pending first, then by submission date)
        $query->orderByRaw("CASE 
                    WHEN status = 'pending' THEN 1 
                    WHEN status = 'processing' THEN 2 
                    ELSE 3 END")
              ->orderBy('submitted_at', 'desc');

        $requests = $query->paginate(20)->appends($request->query());

        // Get document types for filter
        $documentTypes = DocumentType::active()->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => DocumentRequest::byBarangay($barangay->id)->count(),
            'pending' => DocumentRequest::byBarangay($barangay->id)->pending()->count(),
            'processing' => DocumentRequest::byBarangay($barangay->id)->processing()->count(),
            'approved' => DocumentRequest::byBarangay($barangay->id)->approved()->count(),
            'overdue' => DocumentRequest::byBarangay($barangay->id)
                                       ->where('status', 'pending')
                                       ->where('submitted_at', '<', now()->subDays(5))
                                       ->count(),
        ];

        return view('barangay.documents.index', compact(
            'requests', 'documentTypes', 'barangay', 'stats'
        ));
    }

    /**
     * Show specific document request details.
     */
    public function show(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Check if request belongs to user's barangay
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied to this document request.');
        }

        $documentRequest->load(['user.residentProfile', 'documentType', 'processor']);

        return view('barangay.documents.show', compact('documentRequest'));
    }

    /**
     * Process a document request (mark as processing).
     */
    public function process(Request $request, DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Check access
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Document request cannot be processed in its current status.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $documentRequest->process($user, $request->notes);

        return redirect()->back()->with('success', 'Document request marked as processing.');
    }

    /**
     * Approve a document request.
     */
    public function approve(Request $request, DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Check access
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if (!in_array($documentRequest->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Document request cannot be approved in its current status.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        // Simply approve the request - no PDF generation
        $documentRequest->approve($user, $request->notes);

        return redirect()->back()->with('success', 'Document request approved. Resident can now print the document.');
    }

    /**
     * Print document (Barangay staff only).
     */
    public function print(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Double-check access - only barangay staff can print
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied. Only barangay staff can print documents.');
        }

        if ($documentRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Document must be approved before printing.');
        }

        // Load all necessary relationships
        $documentRequest->load([
            'user.residentProfile', 
            'barangay', 
            'documentType',
            'processor'
        ]);

        // Process the template content with actual data
        $documentContent = $this->processTemplate($documentRequest);

        return view('barangay.documents.printable', compact('documentRequest', 'documentContent'));
    }

    /**
     * Process template and replace variables with actual data.
     */
    private function processTemplate($documentRequest)
    {
        $documentType = $documentRequest->documentType;
        $user = $documentRequest->user;
        $resident = $user->residentProfile;
        $barangay = $documentRequest->barangay;
        
        // Get template (custom or default)
        $template = $documentType->template_content ?? $this->getDefaultTemplate($documentType);
        
        // Prepare all data
        $data = [
            // User info - basic
            'name' => $user->first_name . ' ' . $user->last_name,
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_name' => $user->middle_name ?? '',
            
            // User info - from resident profile
            'address' => $resident->full_address ?? $user->address ?? '',
            'birthday' => $resident->birthday ? $resident->birthday->format('F d, Y') : '',
            'age' => $resident->age ?? '',
            'sex' => $resident->sex ?? '',
            'civil_status' => $resident->civil_status ?? '',
            'place_of_birth' => $resident->place_of_birth ?? '',
            'purok_zone' => $resident->purok_zone ?? '',
            
            // Barangay info
            'barangay' => $barangay->name ?? '',
            'barangay_name' => $barangay->name ?? '',
            'municipality_name' => 'Sablayan',
            'province_name' => 'Occidental Mindoro',
            
            // Date info
            'date' => now()->format('F d, Y'),
            'issue_day' => now()->format('jS'),
            'issue_month' => now()->format('F'),
            'issue_year' => now()->format('Y'),
            
            // Document info
            'tracking_number' => $documentRequest->tracking_number,
            'reference_number' => $documentRequest->tracking_number,
            'document_type' => $documentType->name,
            
            // Officials
            'barangay_captain' => $barangay->captain_name ?? 'BARANGAY CAPTAIN',
            'captain_name' => $barangay->captain_name ?? 'BARANGAY CAPTAIN',
            
            // Purpose
            'purpose' => $documentRequest->purpose ?? '',
            
            // Custom form fields
            ...$this->getFormFieldData($documentRequest)
        ];
        
        // Replace all variables in template
        $content = $template;
        foreach ($data as $key => $value) {
            // Replace multiple formats:
            // @{{ key }} - from template editor
            // {{ key }} - alternative format
            // {key} - old format
            // [KEY] - uppercase old format
            $patterns = [
                '@{{ ' . $key . ' }}',
                '@{{' . $key . '}}',
                '{{ ' . $key . ' }}',
                '{{' . $key . '}}',
                '{' . $key . '}',
                '[' . strtoupper($key) . ']'
            ];
            
            foreach ($patterns as $pattern) {
                $content = str_replace($pattern, $value, $content);
            }
        }
        
        return $content;
    }

    /**
     * Get custom form field data from the document request.
     */
    private function getFormFieldData($documentRequest)
    {
        $formData = [];
        
        if ($documentRequest->form_data) {
            $data = is_string($documentRequest->form_data) 
                ? json_decode($documentRequest->form_data, true) 
                : $documentRequest->form_data;
            
            if (is_array($data)) {
                $formData = $data;
            }
        }
        
        return $formData;
    }

    /**
     * Get default template if no custom template exists.
     */
    private function getDefaultTemplate($documentType)
    {
        return <<<'HTML'
<p><strong>TO WHOM IT MAY CONCERN:</strong></p>

<p style="text-indent: 50px;">
    This is to certify that <strong>[NAME]</strong>, 
    [AGE] years old, [CIVIL_STATUS], 
    born on [BIRTHDAY] at [PLACE_OF_BIRTH], 
    and presently residing at [ADDRESS], 
    Barangay [BARANGAY], Sablayan, Occidental Mindoro, 
    is a bona fide resident of this barangay.
</p>

<p style="text-indent: 50px;">
    Based on the records of this Barangay, the above-named person 
    has no derogatory record and is known to be of good moral character.
</p>

<p style="text-indent: 50px;">
    This certification is issued upon request of the above-named person 
    for <strong>[PURPOSE]</strong> purposes and whatever legal purposes 
    it may serve.
</p>

<p style="text-indent: 50px;">
    Issued this [DATE] at Barangay [BARANGAY], 
    Municipality of Sablayan, Province of Occidental Mindoro, Philippines.
</p>
HTML;
    }

    /**
     * View document before printing (Barangay staff only).
     */
    public function view(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        $documentRequest->load(['user.residentProfile', 'barangay', 'documentType']);

        return view('barangay.documents.view', compact('documentRequest'));
    }

    /**
     * Reject a document request.
     */
    public function reject(Request $request, DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Check access
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if (!in_array($documentRequest->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Document request cannot be rejected in its current status.');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $documentRequest->reject($user, $request->reason);

        return redirect()->back()->with('success', 'Document request rejected.');
    }

    /**
     * Generate PDF for approved document.
     */
    public function generatePdf(DocumentRequest $documentRequest)
    {
        $user = Auth::user();
        
        // Check access
        if ($documentRequest->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if ($documentRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'PDF can only be generated for approved requests.');
        }

        $this->generateDocumentPdf($documentRequest);

        return redirect()->back()->with('success', 'PDF regenerated successfully.');
    }

    private function generateDocumentPdf(DocumentRequest $documentRequest)
    {
        // This method can be removed if you're not using PDF generation anymore
        // Or kept for future use
    }
}