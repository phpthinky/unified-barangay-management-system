<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResidentProfile;
use App\Models\Barangay;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResidentsExport;

class ResidentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display listing of all residents across barangays.
     */
    public function index(Request $request)
    {
        $query = ResidentProfile::with(['user', 'barangay', 'verifier']);

        // Filter by barangay
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        // Filter by verification status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'verified':
                    $query->verified();
                    break;
                case 'pending':
                    $query->unverified();
                    break;
            }
        }

        // Filter by special classifications
        if ($request->filled('classification')) {
            switch ($request->classification) {
                case 'senior':
                    $query->seniorCitizens();
                    break;
                case 'pwd':
                    $query->pwds();
                    break;
                case 'solo_parent':
                    $query->soloParents();
                    break;
                case '4ps':
                    $query->where('is_4ps_beneficiary', true);
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('barangay', function($barangayQuery) use ($search) {
                    $barangayQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('purok_zone', 'like', "%{$search}%");
            });
        }

        // Filter by purok/zone across all barangays
        if ($request->filled('purok')) {
            $query->where('purok_zone', $request->purok);
        }

        $residents = $query->orderBy('created_at', 'desc')
                          ->paginate(25)
                          ->appends($request->query());

        // Get all barangays for filter dropdown
        $barangays = Barangay::orderBy('name')->get();

        // Get unique purok/zones for filter
        $puroks = ResidentProfile::whereNotNull('purok_zone')
                                ->distinct()
                                ->pluck('purok_zone')
                                ->sort();

        // Municipality-wide statistics
        $stats = [
            'total' => ResidentProfile::count(),
            'verified' => ResidentProfile::verified()->count(),
            'pending' => ResidentProfile::unverified()->count(),
            'senior_citizens' => ResidentProfile::seniorCitizens()->count(),
            'pwd' => ResidentProfile::pwds()->count(),
            'solo_parents' => ResidentProfile::soloParents()->count(),
            '4ps_beneficiaries' => ResidentProfile::where('is_4ps_beneficiary', true)->count(),
        ];

        // Barangay-wise statistics
        $barangayStats = Barangay::withCount([
            'residentProfiles',
            'residentProfiles as verified_count' => function($query) {
                $query->where('is_verified', true);
            },
            'residentProfiles as pending_count' => function($query) {
                $query->where('is_verified', false);
            }
        ])->orderBy('name')->get();

        return view('admin.residents.index', compact(
            'residents', 'barangays', 'puroks', 'stats', 'barangayStats'
        ));
    }

    /**
     * Show specific resident details with full system access.
     */
    public function show(ResidentProfile $resident)
    {
        $resident->load([
            'user', 
            'barangay', 
            'verifier', 
            'householdHead', 
            'householdMembers.user'
        ]);

        // Get resident's complete service history across all modules
        $serviceHistory = [
            'documents' => $resident->user->documentRequests()
                                       ->with(['documentType', 'processor.barangay'])
                                       ->latest()
                                       ->take(15)
                                       ->get(),
            
            'complaints' => $resident->user->complaints()
                                        ->with(['complaintType', 'assignedTo', 'barangay'])
                                        ->latest()
                                        ->take(10)
                                        ->get(),
            
            'permits' => $resident->user->businessPermits()
                                     ->with(['businessPermitType', 'processor.barangay'])
                                     ->latest()
                                     ->take(10)
                                     ->get(),
        ];

        // Get verification history if available
        $verificationHistory = [
            'current_status' => $resident->is_verified,
            'verified_at' => $resident->verified_at,
            'verified_by' => $resident->verifier,
            'verification_notes' => $resident->verification_notes,
        ];

        return view('admin.residents.show', compact(
            'resident', 
            'serviceHistory', 
            'verificationHistory'
        ));
    }

    /**
     * Override verification of a resident profile (Municipality Admin power).
     */
    public function verify(Request $request, ResidentProfile $resident)
    {
        $user = Auth::user();

        if ($resident->is_verified) {
            return redirect()->back()->with('warning', 'Resident is already verified.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'override_reason' => 'required|string|max:500'
        ]);

        // Admin override verification with special notes
        $notes = "MUNICIPALITY ADMIN OVERRIDE: " . $request->override_reason;
        if ($request->notes) {
            $notes .= " | Additional Notes: " . $request->notes;
        }

        $resident->verify($user, $notes);

        // Log this administrative action
        activity()
            ->performedOn($resident)
            ->causedBy($user)
            ->withProperties([
                'action' => 'admin_override_verify',
                'reason' => $request->override_reason,
                'barangay_id' => $resident->barangay_id
            ])
            ->log('Municipality admin override verified resident');

        return redirect()->back()->with('success', 'Resident profile verified via administrative override.');
    }

    /**
     * Override unverification of a resident profile (Municipality Admin power).
     */
    public function unverify(Request $request, ResidentProfile $resident)
    {
        $user = Auth::user();

        if (!$resident->is_verified) {
            return redirect()->back()->with('warning', 'Resident is not verified.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
            'override_reason' => 'required|string|max:500'
        ]);

        // Admin override unverification with special notes
        $notes = "MUNICIPALITY ADMIN OVERRIDE: " . $request->override_reason . " | Reason: " . $request->reason;

        $resident->unverify($notes);

        // Log this administrative action
        activity()
            ->performedOn($resident)
            ->causedBy($user)
            ->withProperties([
                'action' => 'admin_override_unverify',
                'reason' => $request->reason,
                'override_reason' => $request->override_reason,
                'barangay_id' => $resident->barangay_id
            ])
            ->log('Municipality admin override unverified resident');

        return redirect()->back()->with('success', 'Resident verification removed via administrative override.');
    }

    /**
     * Show all pending residents across all barangays.
     */
    public function pending(Request $request)
    {
        $query = ResidentProfile::unverified()->with(['user', 'barangay']);

        // Filter by barangay
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('barangay', function($barangayQuery) use ($search) {
                    $barangayQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $pendingResidents = $query->orderBy('created_at', 'asc')
                                 ->paginate(20)
                                 ->appends($request->query());

        // Get all barangays for filter
        $barangays = Barangay::orderBy('name')->get();

        // Statistics by barangay
        $barangayPendingStats = Barangay::withCount([
            'residentProfiles as pending_count' => function($query) {
                $query->where('is_verified', false);
            }
        ])->having('pending_count', '>', 0)
          ->orderBy('pending_count', 'desc')
          ->get();

        return view('admin.residents.pending', compact(
            'pendingResidents', 
            'barangays', 
            'barangayPendingStats'
        ));
    }

    /**
     * Bulk verify residents (Municipality Admin feature).
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'resident_ids' => 'required|array',
            'resident_ids.*' => 'exists:resident_profiles,id',
            'bulk_notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $notes = "MUNICIPALITY ADMIN BULK VERIFICATION";
        if ($request->bulk_notes) {
            $notes .= " | Notes: " . $request->bulk_notes;
        }

        $verifiedCount = 0;
        foreach ($request->resident_ids as $residentId) {
            $resident = ResidentProfile::find($residentId);
            if ($resident && !$resident->is_verified) {
                $resident->verify($user, $notes);
                $verifiedCount++;
            }
        }

        return redirect()->back()->with('success', "Successfully verified {$verifiedCount} residents.");
    }

    /**
     * Export residents to Excel with municipality-wide data.
     */
    public function exportExcel(Request $request)
    {
        $query = ResidentProfile::with(['user', 'barangay']);

        // Apply same filters as index
        if ($request->filled('barangay_id')) {
            $query->where('barangay_id', $request->barangay_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'verified':
                    $query->verified();
                    break;
                case 'pending':
                    $query->unverified();
                    break;
            }
        }

        if ($request->filled('classification')) {
            switch ($request->classification) {
                case 'senior':
                    $query->seniorCitizens();
                    break;
                case 'pwd':
                    $query->pwds();
                    break;
                case 'solo_parent':
                    $query->soloParents();
                    break;
                case '4ps':
                    $query->scope4PsBeneficiaries();
                    break;
            }
        }

        $residents = $query->get();

        $filename = 'municipality_residents_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ResidentsExport($residents, true), $filename);
    }

    /**
     * Generate comprehensive resident statistics for municipality overview.
     */
    public function statistics(Request $request)
    {
        $dateRange = $request->get('range', '30'); // Default 30 days

        $stats = [
            'overview' => [
                'total_residents' => ResidentProfile::count(),
                'verified_residents' => ResidentProfile::verified()->count(),
                'pending_verification' => ResidentProfile::unverified()->count(),
                'verification_rate' => ResidentProfile::count() > 0 
                    ? round((ResidentProfile::verified()->count() / ResidentProfile::count()) * 100, 1)
                    : 0
            ],

            'demographics' => [
                'senior_citizens' => ResidentProfile::seniorCitizens()->count(),
                'pwd' => ResidentProfile::pwds()->count(),
                'solo_parents' => ResidentProfile::soloParents()->count(),
                '4ps_beneficiaries' => ResidentProfile::where('is_4ps_beneficiary', true)->count(),
                'household_heads' => ResidentProfile::householdHeads()->count(),
            ],

            'by_barangay' => Barangay::withCount([
                'residentProfiles',
                'residentProfiles as verified_count' => function($query) {
                    $query->verified();
                },
                'residentProfiles as pending_count' => function($query) {
                    $query->unverified();
                }
            ])->orderBy('resident_profiles_count', 'desc')->get(),

            'recent_registrations' => ResidentProfile::with(['user', 'barangay'])
                ->where('created_at', '>=', now()->subDays($dateRange))
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),

            'recent_verifications' => ResidentProfile::with(['user', 'barangay', 'verifier'])
                ->whereNotNull('verified_at')
                ->where('verified_at', '>=', now()->subDays($dateRange))
                ->orderBy('verified_at', 'desc')
                ->take(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}