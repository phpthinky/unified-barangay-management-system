<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = DocumentType::withCount('documentRequests');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('format')) {
            $query->where('document_format', $request->format);
        }

        $documentTypes = $query->ordered()->paginate(15)->appends($request->query());

        $categories = DocumentType::distinct()->pluck('category')->filter();
        
        $formats = ['certificate', 'id_card', 'half_sheet', 'legal', 'custom'];

        return view('admin.document-types.index', compact('documentTypes', 'categories', 'formats'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types',
            'slug' => 'nullable|string|max:255|unique:document_types',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'document_format' => 'required|in:certificate,id_card,half_sheet,legal,custom',
            'format_notes' => 'nullable|string|max:500',
            'enable_printing' => 'boolean',
            'fee' => 'required|numeric|min:0|max:99999.99',
            'processing_days' => 'required|integer|min:1|max:365',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'form_fields' => 'nullable|array',
            'template_content' => 'nullable|string',
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'string|max:100',
            'requires_verification' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle checkboxes (if not checked, they won't be in request)
        $validated['enable_printing'] = $request->has('enable_printing');
        $validated['requires_verification'] = $request->has('requires_verification');
        $validated['is_active'] = $request->has('is_active');

        // Process form_fields
        if ($request->has('form_fields')) {
            $processedFields = [];
            foreach ($request->form_fields as $field) {
                $processedField = [
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => isset($field['required']) && $field['required'] == '1',
                ];
                
                // Handle select options
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    // Split by comma and trim
                    $options = array_map('trim', explode(',', $field['options']));
                    $processedField['options'] = $options;
                }
                
                $processedFields[] = $processedField;
            }
            $validated['form_fields'] = $processedFields;
        }

        DocumentType::create($validated);

        return redirect()->route('barangay.document-types.index')
                       ->with('success', 'Document type created successfully.');
    }

    public function show(DocumentType $documentType)
    {
        $documentType->load('documentRequests.user');
        
        $stats = [
            'total_requests' => $documentType->documentRequests()->count(),
            'pending_requests' => $documentType->documentRequests()->where('status', 'pending')->count(),
            'approved_requests' => $documentType->documentRequests()->where('status', 'approved')->count(),
            'rejected_requests' => $documentType->documentRequests()->where('status', 'rejected')->count(),
            'total_revenue' => $documentType->documentRequests()->sum('amount_paid'),
        ];

        $recentRequests = $documentType->documentRequests()
                                    ->with(['user', 'barangay'])
                                    ->latest()
                                    ->take(10)
                                    ->get();

        $user = Auth::user();
        $barangay = $user->barangay;
        if (empty($barangay->logo)) {
            // code...
            $barangay->logo = 'images/barangay-seal.png';
        }else{
            $barangay->logo = 'uploads/logos/'.$barangay->logo;

        }
        return view('admin.document-types.show', compact('documentType', 'stats', 'recentRequests','barangay'));
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id,
            'slug' => 'nullable|string|max:255|unique:document_types,slug,' . $documentType->id,
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'document_format' => 'required|in:certificate,id_card,half_sheet,legal,custom',
            'format_notes' => 'nullable|string|max:500',
            'enable_printing' => 'boolean',
            'fee' => 'required|numeric|min:0|max:99999.99',
            'processing_days' => 'required|integer|min:1|max:365',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'form_fields' => 'nullable|array',
            'template_content' => 'nullable|string',
            'template_fields' => 'nullable|array',
            'template_fields.*' => 'string|max:100',
            'requires_verification' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle checkboxes
        $validated['enable_printing'] = $request->has('enable_printing');
        $validated['requires_verification'] = $request->has('requires_verification');
        $validated['is_active'] = $request->has('is_active');

        // Process form_fields
        if ($request->has('form_fields')) {
            $processedFields = [];
            foreach ($request->form_fields as $field) {
                $processedField = [
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => isset($field['required']) && $field['required'] == '1',
                ];
                
                // Handle select options
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    // Split by comma and trim
                    $options = array_map('trim', explode(',', $field['options']));
                    $processedField['options'] = $options;
                }
                
                $processedFields[] = $processedField;
            }
            $validated['form_fields'] = $processedFields;
        }

        $documentType->update($validated);

        return redirect()->route('barangay.document-types.show', $documentType)
                       ->with('success', 'Document type updated successfully.');
    }

    /**
     * Show the template editor for a document type.
     */
    public function editTemplate(DocumentType $documentType)
    {
        return view('admin.document-types.template', compact('documentType'));
    }

    /**
     * Update the template for a document type.
     */
    public function updateTemplate(Request $request, DocumentType $documentType)
    {
        $validated = $request->validate([
            'template_content' => 'nullable|string|max:100000', // Increased max length for HTML content
        ]);

        // Clean up the HTML content if needed
        $templateContent = $validated['template_content'] ?? null;

        // If template is empty or just whitespace (but preserve formatting tags), set to null
        // Only check if content has actual text, not just Quill's default empty <p><br></p>
        if ($templateContent) {
            $cleanText = trim(strip_tags($templateContent));
            // If completely empty or only has default Quill placeholder
            if ($cleanText === '' && !preg_match('/\[([A-Z_]+)\]/', $templateContent)) {
                $templateContent = null;
            }
        }

        $documentType->update([
            'template_content' => $templateContent
        ]);

        return redirect()->route('barangay.document-types.template', $documentType)
                       ->with('success', 'Template updated successfully.');
    }

    /**
     * Toggle document type status (active/inactive).
     */
    public function toggleStatus(DocumentType $documentType)
    {
        $documentType->update([
            'is_active' => !$documentType->is_active
        ]);

        $status = $documentType->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                       ->with('success', "Document type {$status} successfully.");
    }

    /**
     * Toggle printing enabled/disabled.
     */
    public function togglePrinting(DocumentType $documentType)
    {
        $documentType->update([
            'enable_printing' => !$documentType->enable_printing
        ]);

        $status = $documentType->enable_printing ? 'enabled' : 'disabled';
        
        return redirect()->back()
                       ->with('success', "Printing {$status} successfully for this document type.");
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documentRequests()->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete document type with existing requests.');
        }

        $documentType->delete();

        return redirect()->route('barangay.document-types.index')
                       ->with('success', 'Document type deleted successfully.');
    }
}