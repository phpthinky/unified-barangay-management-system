<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\BarangayInhabitant;

class DocumentRequestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'resident']);
    }

    /**
     * Display listing of user's document requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DocumentRequest::where('user_id', $user->id)
                               ->with(['documentType', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Search by tracking number
        if ($request->filled('search')) {
            $query->where('tracking_number', 'like', '%' . $request->search . '%');
        }

        $requests = $query->orderBy('submitted_at', 'desc')
                         ->paginate(10)
                         ->appends($request->query());

        // Statistics
        $stats = [
            'total' => DocumentRequest::where('user_id', $user->id)->count(),
            'pending' => DocumentRequest::where('user_id', $user->id)->pending()->count(),
            'processing' => DocumentRequest::where('user_id', $user->id)->processing()->count(),
            'approved' => DocumentRequest::where('user_id', $user->id)->approved()->count(),
            'rejected' => DocumentRequest::where('user_id', $user->id)->rejected()->count(),
        ];

        return view('resident.documents.index', compact('requests', 'stats'));
    }
    
    /**
     * Show document request form
     */
    public function create($documentTypeSlug = null)
    {
        $user = Auth::user();
        
        // Get inhabitant record
        $inhabitant = BarangayInhabitant::where('user_id', $user->id)
                                       ->where('barangay_id', $user->barangay_id)
                                       ->first();
        
        if (!$inhabitant) {
            return redirect()->route('resident.documents.index')
                           ->with('error', 'You must be registered in the Barangay Registry first. Please visit the barangay hall.');
        }
        
        // Get all active document types
        $documentTypes = DocumentType::where('is_active', true)
                                    ->orderBy('name')
                                    ->get();
        
        // If a specific document type is selected
        $selectedType = null;
        if ($documentTypeSlug) {
            $selectedType = DocumentType::where('slug', $documentTypeSlug)
                                       ->where('is_active', true)
                                       ->first();
            
            if ($selectedType) {
                // Check eligibility for this specific document
                $eligibility = $inhabitant->canRequestDocument($documentTypeSlug);
                
                if (!$eligibility['eligible']) {
                    return redirect()->route('resident.documents.index')
                               ->with('error', 'You are not eligible to request this document.')
                               ->with('reasons', $eligibility['reasons']);
                }
            }
        }
        
        // Pass the inhabitant as residentProfile for the view
        $residentProfile = $inhabitant;
        
        return view('resident.documents.create', compact('documentTypes', 'selectedType', 'residentProfile', 'inhabitant'));
    }

    /**
     * Store document request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'purpose' => 'required|string|max:500',
            'copies_requested' => 'required|integer|min:1|max:10',
            'form_data' => 'nullable|array',
            'supporting_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        // Get document type
        $documentType = DocumentType::findOrFail($validated['document_type_id']);
        
        // Get inhabitant record
        $inhabitant = BarangayInhabitant::where('user_id', $user->id)
                                       ->where('barangay_id', $user->barangay_id)
                                       ->first();
        
        if (!$inhabitant) {
            return back()->with('error', 'You must be registered in the Barangay Registry first.');
        }
        
        // Check eligibility before saving
        $eligibility = $inhabitant->canRequestDocument($documentType->slug);
        
        if (!$eligibility['eligible']) {
            return back()->with('error', 'You are not eligible to request this document.')
                        ->with('reasons', $eligibility['reasons'])
                        ->withInput();
        }
        
        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('supporting_files')) {
            foreach ($request->file('supporting_files') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/documents'), $filename);
                $uploadedFiles[] = $filename;
            }
        }
        
        // Calculate amount
        $amountPaid = $documentType->fee * $validated['copies_requested'];
        
        // Prepare form data - merge resident info with form_data
        $formData = array_merge([
            'resident_name' => $inhabitant->full_name,
            'resident_address' => $inhabitant->full_address,
            'resident_age' => $inhabitant->age,
            'resident_birth_date' => $inhabitant->date_of_birth ? $inhabitant->date_of_birth->format('Y-m-d') : null,
            'resident_birth_place' => $inhabitant->place_of_birth,
            'resident_civil_status' => $inhabitant->civil_status,
            'resident_sex' => $inhabitant->sex,
            'resident_citizenship' => $inhabitant->citizenship,
            'barangay_name' => $user->barangay->name,
        ], $validated['form_data'] ?? []);
        
        // Create document request - let model auto-generate tracking_number
        $documentRequest = DocumentRequest::create([
            'user_id' => $user->id,
            'barangay_id' => $user->barangay_id,
            'document_type_id' => $documentType->id,
            'purpose' => $validated['purpose'],
            'copies_requested' => $validated['copies_requested'],
            'form_data' => $formData,
            'uploaded_files' => $uploadedFiles,
            'status' => 'pending',
            'amount_paid' => $amountPaid,
            'payment_method' => 'cash',
            'payment_date' => now(),
        ]);
        
        return redirect()->route('resident.documents.show', $documentRequest->id)
                        ->with('success', 'Document request submitted successfully! Tracking Number: ' . $documentRequest->tracking_number);
    }

    /**
     * Show specific document request.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $documentRequest = DocumentRequest::with(['documentType', 'processor', 'barangay'])
                                         ->findOrFail($id);

        // Check if request belongs to user
        if ($documentRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        return view('resident.documents.show', compact('documentRequest'));
    }

    /**
     * Download approved document.
     */
    public function download($id)
    {
        $user = Auth::user();
        
        $documentRequest = DocumentRequest::findOrFail($id);

        // Check if request belongs to user
        if ($documentRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Check if document can be downloaded
        if (!$documentRequest->canBeDownloaded()) {
            return redirect()->back()->with('error', 'Document is not available for download.');
        }

        $filePath = public_path('uploads/documents/' . $documentRequest->generated_file);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Document file not found.');
        }

        return response()->download($filePath, 
            $documentRequest->documentType->name . '_' . $documentRequest->tracking_number . '.pdf'
        );
    }
    
    /**
     * Cancel a pending document request
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        $documentRequest = DocumentRequest::findOrFail($id);

        // Check if request belongs to user
        if ($documentRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Only allow cancellation of pending requests
        if ($documentRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        $documentRequest->update([
            'status' => 'cancelled',
            'processing_notes' => 'Cancelled by requester on ' . now()->format('Y-m-d H:i:s'),
        ]);

        return redirect()->route('resident.documents.index')
                        ->with('success', 'Document request cancelled successfully.');
    }
}