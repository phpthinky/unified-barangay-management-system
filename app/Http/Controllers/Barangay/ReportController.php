<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\Complaint;
use App\Models\BusinessPermit;
use App\Models\ComplaintHearing;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show reports dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        // Quick stats for report dashboard
        $stats = [
            'total_residents' => ResidentProfile::byBarangay($barangay->id)->count(),
            'total_documents' => DocumentRequest::byBarangay($barangay->id)->count(),
            'total_complaints' => Complaint::byBarangay($barangay->id)->count(),
            'total_permits' => BusinessPermit::byBarangay($barangay->id)->count(),
        ];

        return view('barangay.reports.index', compact('barangay', 'stats'));
    }

    /**
     * Residents Report
     */
    public function residents(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        $query = ResidentProfile::byBarangay($barangay->id)->with('user');

        // Filters
        if ($request->filled('status')) {
            if ($request->status == 'verified') {
                $query->verified();
            } elseif ($request->status == 'unverified') {
                $query->unverified();
            }
        }

        if ($request->filled('purok')) {
            $query->where('purok_zone', $request->purok);
        }

        if ($request->filled('civil_status')) {
            $query->where('civil_status', $request->civil_status);
        }

        if ($request->filled('gender')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        if ($request->filled('age_from') || $request->filled('age_to')) {
            $query->whereHas('user', function($q) use ($request) {
                if ($request->filled('age_from')) {
                    $q->whereNotNull('birth_date')
                      ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$request->age_from]);
                }
                if ($request->filled('age_to')) {
                    $q->whereNotNull('birth_date')
                      ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$request->age_to]);
                }
            });
        }

        // Special classifications filters
        if ($request->filled('is_pwd')) {
            $query->where('is_pwd', true);
        }
        if ($request->filled('is_senior_citizen')) {
            $query->where('is_senior_citizen', true);
        }
        if ($request->filled('is_solo_parent')) {
            $query->where('is_solo_parent', true);
        }
        if ($request->filled('is_4ps')) {
            $query->where('is_4ps_beneficiary', true);
        }

        // Statistics
        $stats = [
            'total' => ResidentProfile::byBarangay($barangay->id)->count(),
            'verified' => ResidentProfile::byBarangay($barangay->id)->verified()->count(),
            'unverified' => ResidentProfile::byBarangay($barangay->id)->unverified()->count(),
            'male' => ResidentProfile::byBarangay($barangay->id)->whereHas('user', function($q) {
                $q->where('gender', 'male');
            })->count(),
            'female' => ResidentProfile::byBarangay($barangay->id)->whereHas('user', function($q) {
                $q->where('gender', 'female');
            })->count(),
            'pwd' => ResidentProfile::byBarangay($barangay->id)->where('is_pwd', true)->count(),
            'senior' => ResidentProfile::byBarangay($barangay->id)->where('is_senior_citizen', true)->count(),
            'solo_parent' => ResidentProfile::byBarangay($barangay->id)->where('is_solo_parent', true)->count(),
            '4ps' => ResidentProfile::byBarangay($barangay->id)->where('is_4ps_beneficiary', true)->count(),
        ];

        // Get unique puroks
        $puroks = ResidentProfile::byBarangay($barangay->id)
                                ->select('purok_zone')
                                ->distinct()
                                ->orderBy('purok_zone')
                                ->pluck('purok_zone');

        // Check if print view is requested
        if ($request->has('print')) {
            $residents = $query->get(); // Get all for printing
            return view('barangay.reports.print.residents', compact('barangay', 'residents', 'stats'));
        }

        $residents = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('barangay.reports.residents', compact('barangay', 'residents', 'stats', 'puroks'));
    }

    /**
     * Documents Report
     */
    public function documents(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        $query = DocumentRequest::byBarangay($barangay->id)
                                ->with(['user', 'documentType', 'processor']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Document type filter
        if ($request->filled('document_type')) {
            $query->where('document_type_id', $request->document_type);
        }

        // Statistics
        $stats = [
            'total' => DocumentRequest::byBarangay($barangay->id)->count(),
            'pending' => DocumentRequest::byBarangay($barangay->id)->where('status', 'pending')->count(),
            'processing' => DocumentRequest::byBarangay($barangay->id)->where('status', 'processing')->count(),
            'approved' => DocumentRequest::byBarangay($barangay->id)->where('status', 'approved')->count(),
            'released' => DocumentRequest::byBarangay($barangay->id)->where('status', 'released')->count(),
            'rejected' => DocumentRequest::byBarangay($barangay->id)->where('status', 'rejected')->count(),
        ];

        // Revenue calculation
        $totalRevenue = DocumentRequest::byBarangay($barangay->id)
                                      ->whereIn('status', ['approved', 'released'])
                                      ->sum('amount_paid');

        $stats['total_revenue'] = $totalRevenue;

        // Document types
        $documentTypes = \App\Models\DocumentType::active()->orderBy('name')->get();

        // Check if print view is requested
        if ($request->has('print')) {
            $documents = $query->get();
            return view('barangay.reports.print.documents', compact('barangay', 'documents', 'stats'));
        }

        $documents = $query->orderBy('submitted_at', 'desc')->paginate(50);

        return view('barangay.reports.documents', compact('barangay', 'documents', 'stats', 'documentTypes'));
    }

    /**
     * Complaints Report
     */
    public function complaints(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        $query = Complaint::byBarangay($barangay->id)
                         ->with(['complainant', 'complaintType', 'assignedTo', 'resolver']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('received_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('received_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Complaint type filter
        if ($request->filled('complaint_type')) {
            $query->where('complaint_type_id', $request->complaint_type);
        }

        // Statistics
        $stats = [
            'total' => Complaint::byBarangay($barangay->id)->count(),
            'received' => Complaint::byBarangay($barangay->id)->where('status', 'received')->count(),
            'assigned' => Complaint::byBarangay($barangay->id)->where('status', 'assigned')->count(),
            'in_process' => Complaint::byBarangay($barangay->id)->where('status', 'in_process')->count(),
            'mediation' => Complaint::byBarangay($barangay->id)->where('status', 'mediation')->count(),
            'resolved' => Complaint::byBarangay($barangay->id)->where('status', 'resolved')->count(),
            'urgent' => Complaint::byBarangay($barangay->id)->where('priority', 'urgent')->count(),
        ];

        // Complaint types
        $complaintTypes = \App\Models\ComplaintType::active()->orderBy('name')->get();

        // Check if print view is requested
        if ($request->has('print')) {
            $complaints = $query->get();
            return view('barangay.reports.print.complaints', compact('barangay', 'complaints', 'stats'));
        }

        $complaints = $query->orderBy('received_at', 'desc')->paginate(50);

        return view('barangay.reports.complaints', compact('barangay', 'complaints', 'stats', 'complaintTypes'));
    }

    /**
     * Business Permits Report
     */
    public function permits(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        $query = BusinessPermit::byBarangay($barangay->id)
                              ->with(['applicant', 'businessPermitType', 'processor']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Business type filter
        if ($request->filled('business_type')) {
            $query->where('business_permit_type_id', $request->business_type);
        }

        // Statistics
        $stats = [
            'total' => BusinessPermit::byBarangay($barangay->id)->count(),
            'pending' => BusinessPermit::byBarangay($barangay->id)->where('status', 'pending')->count(),
            'approved' => BusinessPermit::byBarangay($barangay->id)->where('status', 'approved')->count(),
            'rejected' => BusinessPermit::byBarangay($barangay->id)->where('status', 'rejected')->count(),
            'expired' => BusinessPermit::byBarangay($barangay->id)->where('status', 'expired')->count(),
        ];

        // Revenue calculation
        $totalRevenue = BusinessPermit::byBarangay($barangay->id)
                                     ->where('status', 'approved')
                                     ->sum('total_fees');

        $stats['total_revenue'] = $totalRevenue;

        // Business permit types
        $businessTypes = \App\Models\BusinessPermitType::active()->orderBy('name')->get();

        // Check if print view is requested
        if ($request->has('print')) {
            $permits = $query->get();
            return view('barangay.reports.print.permits', compact('barangay', 'permits', 'stats'));
        }

        $permits = $query->orderBy('submitted_at', 'desc')->paginate(50);

        return view('barangay.reports.permits', compact('barangay', 'permits', 'stats', 'businessTypes'));
    }

    /**
     * Monthly Summary Report
     */
    public function monthlySummary(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        // Default to current month
        $month = $request->filled('month') ? $request->month : now()->format('Y-m');
        $date = Carbon::parse($month . '-01');

        // Residents data
        $residentsData = [
            'new_registrations' => ResidentProfile::byBarangay($barangay->id)
                                                 ->whereYear('created_at', $date->year)
                                                 ->whereMonth('created_at', $date->month)
                                                 ->count(),
            'verified' => ResidentProfile::byBarangay($barangay->id)
                                        ->whereYear('verified_at', $date->year)
                                        ->whereMonth('verified_at', $date->month)
                                        ->count(),
        ];

        // Documents data
        $documentsData = [
            'total_requests' => DocumentRequest::byBarangay($barangay->id)
                                              ->whereYear('submitted_at', $date->year)
                                              ->whereMonth('submitted_at', $date->month)
                                              ->count(),
            'approved' => DocumentRequest::byBarangay($barangay->id)
                                        ->whereYear('approved_at', $date->year)
                                        ->whereMonth('approved_at', $date->month)
                                        ->whereIn('status', ['approved', 'released'])
                                        ->count(),
            'revenue' => DocumentRequest::byBarangay($barangay->id)
                                       ->whereYear('submitted_at', $date->year)
                                       ->whereMonth('submitted_at', $date->month)
                                       ->whereIn('status', ['approved', 'released'])
                                       ->sum('amount_paid'),
        ];

        // Complaints data
        $complaintsData = [
            'total_filed' => Complaint::byBarangay($barangay->id)
                                     ->whereYear('received_at', $date->year)
                                     ->whereMonth('received_at', $date->month)
                                     ->count(),
            'resolved' => Complaint::byBarangay($barangay->id)
                                  ->whereYear('resolved_at', $date->year)
                                  ->whereMonth('resolved_at', $date->month)
                                  ->count(),
            'hearings_held' => ComplaintHearing::where('barangay_id', $barangay->id)
                                              ->whereYear('scheduled_date', $date->year)
                                              ->whereMonth('scheduled_date', $date->month)
                                              ->where('status', 'completed')
                                              ->count(),
        ];

        // Permits data
        $permitsData = [
            'total_applications' => BusinessPermit::byBarangay($barangay->id)
                                                 ->whereYear('submitted_at', $date->year)
                                                 ->whereMonth('submitted_at', $date->month)
                                                 ->count(),
            'approved' => BusinessPermit::byBarangay($barangay->id)
                                       ->whereYear('approved_at', $date->year)
                                       ->whereMonth('approved_at', $date->month)
                                       ->count(),
            'revenue' => BusinessPermit::byBarangay($barangay->id)
                                      ->whereYear('submitted_at', $date->year)
                                      ->whereMonth('submitted_at', $date->month)
                                      ->where('status', 'approved')
                                      ->sum('total_fees'),
        ];

        // Total revenue
        $totalRevenue = $documentsData['revenue'] + $permitsData['revenue'];

        // Check if print view is requested
        if ($request->has('print')) {
            return view('barangay.reports.print.monthly-summary', compact(
                'barangay',
                'date',
                'residentsData',
                'documentsData',
                'complaintsData',
                'permitsData',
                'totalRevenue'
            ));
        }

        return view('barangay.reports.monthly-summary', compact(
            'barangay',
            'date',
            'residentsData',
            'documentsData',
            'complaintsData',
            'permitsData',
            'totalRevenue'
        ));
    }
}