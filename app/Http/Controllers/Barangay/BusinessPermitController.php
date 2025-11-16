<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessPermit;
use App\Models\BusinessPermitType;
use App\Models\Barangay;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BusinessPermitsExport;

class BusinessPermitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'barangay.scope']);
    }

    /**
     * Display a listing of business permits for the barangay.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assigned to this user.');
        }

        $query = BusinessPermit::with(['businessPermitType', 'applicant', 'processor'])
                               ->where('barangay_id', $barangay->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by permit type
        if ($request->filled('permit_type_id')) {
            $query->where('business_permit_type_id', $request->permit_type_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function($applicantQuery) use ($search) {
                      $applicantQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $permits = $query->orderBy('created_at', 'desc')
                        ->paginate(15)
                        ->appends($request->query());

        // Get permit types for filter
        $permitTypes = BusinessPermitType::active()->orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => BusinessPermit::where('barangay_id', $barangay->id)->count(),
            'pending' => BusinessPermit::where('barangay_id', $barangay->id)->where('status', 'pending')->count(),
            'approved' => BusinessPermit::where('barangay_id', $barangay->id)->where('status', 'approved')->count(),
            'rejected' => BusinessPermit::where('barangay_id', $barangay->id)->where('status', 'rejected')->count(),
            'expired' => BusinessPermit::where('barangay_id', $barangay->id)->where('status', 'approved')->where('expires_at', '<=', now())->count(),
        ];

        return view('barangay.business-permits.index', compact('permits', 'permitTypes', 'stats', 'barangay'));
    }

    /**
     * Show the form for creating a new business permit (if needed).
     */
    public function create()
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assigned to this user.');
        }

        $permitTypes = BusinessPermitType::active()->orderBy('name')->get();

        return view('barangay.business-permits.create', compact('permitTypes', 'barangay'));
    }

    /**
     * Display the specified business permit.
     */
    public function show(BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        $businessPermit->load(['businessPermitType', 'applicant', 'processor', 'approver', 'barangay']);

        // Activity log (if you have one)
        $activities = [];

        return view('barangay.business-permits.show', compact('businessPermit', 'activities'));
    }

    /**
     * Show the form for processing a business permit.
     */
    public function process(BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        // Check if permit is in pending status
        if ($businessPermit->status !== 'pending') {
            return redirect()->route('barangay.business-permits.show', $businessPermit)
                           ->with('error', 'Only pending permits can be processed.');
        }

        $businessPermit->load(['businessPermitType', 'applicant', 'barangay']);

        return view('barangay.business-permits.process', compact('businessPermit'));
    }

    /**
     * Approve a business permit.
     */
    public function approve(Request $request, BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        // Check permissions
        if (!$user->can('approve-business-permits')) {
            abort(403, 'You do not have permission to approve permits.');
        }

        // Check if permit is in pending status
        if ($businessPermit->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending permits can be approved.');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
            'permit_number' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date|after:today',
        ]);

        // Calculate expiration date if not provided
        $expiresAt = $request->expires_at;
        if (!$expiresAt) {
            $expiresAt = now()->addMonths($businessPermit->businessPermitType->validity_months);
        }

        // Generate permit number if not provided
        $permitNumber = $request->permit_number;
        if (!$permitNumber) {
            $permitNumber = 'BP-' . $businessPermit->barangay->slug . '-' . date('Y') . '-' . str_pad($businessPermit->id, 5, '0', STR_PAD_LEFT);
        }

        // Generate QR code
        $qrCode = 'BP-' . uniqid() . '-' . time();
        $qrCodePath = 'qrcodes/permits/' . $qrCode . '.png';
        
        // Create directory if not exists
        if (!file_exists(public_path('uploads/qrcodes/permits'))) {
            mkdir(public_path('uploads/qrcodes/permits'), 0777, true);
        }

        // Generate and save QR code
        QrCode::format('png')
              ->size(300)
              ->generate(route('public.verify-document', $qrCode), public_path('uploads/' . $qrCodePath));

        // Update permit
        $businessPermit->update([
            'status' => 'approved',
            'permit_number' => $permitNumber,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'expires_at' => $expiresAt,
            'qr_code' => $qrCode,
            'qr_code_path' => $qrCodePath,
            'remarks' => $request->remarks,
            'processed_by' => $user->id,
            'processed_at' => now(),
        ]);

        // Generate PDF
        $this->generatePermitPDF($businessPermit);

        return redirect()->route('barangay.business-permits.show', $businessPermit)
                       ->with('success', 'Business permit approved successfully.');
    }

    /**
     * Reject a business permit.
     */
    public function reject(Request $request, BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        // Check if permit is in pending status
        if ($businessPermit->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending permits can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $businessPermit->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'processed_by' => $user->id,
            'processed_at' => now(),
        ]);

        return redirect()->route('barangay.business-permits.show', $businessPermit)
                       ->with('success', 'Business permit rejected.');
    }

    /**
     * Generate PDF for approved permit.
     */
    protected function generatePermitPDF(BusinessPermit $businessPermit)
    {
        $businessPermit->load(['businessPermitType', 'applicant', 'barangay']);

        $pdf = PDF::loadView('pdf.business-permit', [
            'permit' => $businessPermit
        ]);

        $fileName = 'business-permit-' . $businessPermit->permit_number . '.pdf';
        $filePath = 'permits/' . $fileName;

        // Create directory if not exists
        if (!file_exists(public_path('uploads/permits'))) {
            mkdir(public_path('uploads/permits'), 0777, true);
        }

        $pdf->save(public_path('uploads/' . $filePath));

        $businessPermit->update([
            'pdf_path' => $filePath
        ]);

        return $filePath;
    }

    /**
     * Download permit PDF.
     */
    public function downloadPDF(BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        if (!$businessPermit->pdf_path || !file_exists(public_path('uploads/' . $businessPermit->pdf_path))) {
            // Regenerate PDF if not exists
            $this->generatePermitPDF($businessPermit);
        }

        return response()->download(
            public_path('uploads/' . $businessPermit->pdf_path),
            'business-permit-' . $businessPermit->permit_number . '.pdf'
        );
    }

    /**
     * Preview permit PDF.
     */
    public function previewPDF(BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        $businessPermit->load(['businessPermitType', 'applicant', 'barangay']);

        $pdf = PDF::loadView('pdf.business-permit', [
            'permit' => $businessPermit
        ]);

        return $pdf->stream('business-permit-' . $businessPermit->permit_number . '.pdf');
    }

    /**
     * Export permits to Excel.
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            abort(403, 'No barangay assigned to this user.');
        }

        $query = BusinessPermit::with(['businessPermitType', 'applicant', 'processor'])
                               ->where('barangay_id', $barangay->id);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('permit_type_id')) {
            $query->where('business_permit_type_id', $request->permit_type_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $permits = $query->get();

        $filename = 'business-permits-' . $barangay->slug . '-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new BusinessPermitsExport($permits), $filename);
    }

    /**
     * Mark permit for inspection.
     */
    public function markForInspection(Request $request, BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        $request->validate([
            'inspection_date' => 'required|date|after_or_equal:today',
            'inspection_notes' => 'nullable|string|max:500',
        ]);

        $businessPermit->update([
            'requires_inspection' => true,
            'inspection_date' => $request->inspection_date,
            'inspection_notes' => $request->inspection_notes,
        ]);

        return redirect()->back()
                       ->with('success', 'Permit marked for inspection.');
    }

    /**
     * Complete inspection.
     */
    public function completeInspection(Request $request, BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        $request->validate([
            'inspection_result' => 'required|in:passed,failed',
            'inspection_remarks' => 'nullable|string|max:1000',
        ]);

        $businessPermit->update([
            'inspection_completed' => true,
            'inspection_completed_at' => now(),
            'inspection_result' => $request->inspection_result,
            'inspection_remarks' => $request->inspection_remarks,
            'inspected_by' => $user->id,
        ]);

        return redirect()->back()
                       ->with('success', 'Inspection completed successfully.');
    }

    /**
     * Renew a business permit.
     */
    public function renew(BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        // Check if permit is approved
        if ($businessPermit->status !== 'approved') {
            return redirect()->back()
                           ->with('error', 'Only approved permits can be renewed.');
        }

        // Create new permit based on old one
        $newPermit = $businessPermit->replicate();
        $newPermit->status = 'pending';
        $newPermit->permit_number = null;
        $newPermit->approved_by = null;
        $newPermit->approved_at = null;
        $newPermit->expires_at = null;
        $newPermit->qr_code = null;
        $newPermit->qr_code_path = null;
        $newPermit->pdf_path = null;
        $newPermit->is_renewal = true;
        $newPermit->previous_permit_id = $businessPermit->id;
        $newPermit->created_at = now();
        $newPermit->save();

        return redirect()->route('barangay.business-permits.show', $newPermit)
                       ->with('success', 'Permit renewal created successfully.');
    }

    /**
     * Update permit details.
     */
    public function update(Request $request, BusinessPermit $businessPermit)
    {
        $user = Auth::user();

        // Check if permit belongs to user's barangay
        if ($businessPermit->barangay_id !== $user->barangay_id) {
            abort(403, 'Unauthorized access to this permit.');
        }

        // Only allow updating pending permits
        if ($businessPermit->status !== 'pending') {
            return redirect()->back()
                           ->with('error', 'Only pending permits can be updated.');
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_address' => 'required|string|max:500',
            'owner_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'business_type' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $businessPermit->update($request->only([
            'business_name',
            'business_address',
            'owner_name',
            'contact_number',
            'email',
            'business_type',
            'remarks',
        ]));

        return redirect()->route('barangay.business-permits.show', $businessPermit)
                       ->with('success', 'Permit updated successfully.');
    }
}