<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResidentProfile;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResidentsExport;

use Illuminate\Support\Facades\DB;


class ResidentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'barangay.staff', 'barangay.scope']);
    }

    /**
     * Display listing of residents.
     */
    
public function index(Request $request)
    {
        $barangayId = auth()->user()->barangay_id;
        
        // Start query
        $query = ResidentProfile::with(['user', 'barangay', 'rbiInhabitant'])
            ->where('barangay_id', $barangayId);

        // Filter by RBI status
        if ($request->rbi_status === 'linked') {
            $query->whereNotNull('rbi_inhabitant_id');
        } elseif ($request->rbi_status === 'not_linked') {
            $query->whereNull('rbi_inhabitant_id');
        }

        // Filter by email verification status
        if ($request->email_status === 'verified') {
            $query->whereHas('user', function ($q) {
                $q->whereNotNull('email_verified_at');
            });
        } elseif ($request->email_status === 'pending') {
            $query->whereHas('user', function ($q) {
                $q->whereNull('email_verified_at');
            });
        }

        // Filter by residential status
        if ($request->residential_status) {
            $query->where('residency_type', $request->residential_status);
        }

        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('purok_zone', 'like', "%{$search}%");
            });
        }

        // Get unique puroks for filter dropdown
        $puroks = ResidentProfile::where('barangay_id', $barangayId)
            ->whereNotNull('purok_zone')
            ->distinct()
            ->pluck('purok_zone')
            ->sort();

        // Paginate results
        $residents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => ResidentProfile::where('barangay_id', $barangayId)->count(),
            'email_verified' => ResidentProfile::where('barangay_id', $barangayId)
                ->whereHas('user', function ($q) {
                    $q->whereNotNull('email_verified_at');
                })->count(),
            'rbi_linked' => ResidentProfile::where('barangay_id', $barangayId)
                ->whereNotNull('rbi_inhabitant_id')->count(),
            'pending' => ResidentProfile::where('barangay_id', $barangayId)
                ->where('is_verified', false)->count(),
        ];

        return view('barangay.residents.index', compact('residents', 'stats', 'puroks'));
    }

    /**
     * Display pending accounts
     */
    public function pending(Request $request)
    {
        $barangayId = auth()->user()->barangay_id;
        
        $query = ResidentProfile::with(['user', 'barangay', 'rbiInhabitant'])
            ->where('barangay_id', $barangayId)
            ->where('is_verified', false);

        // Apply search if any
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $residents = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('barangay.residents.pending', compact('residents'));
    }

    /**
     * Show specific resident details.
     */
    public function show(ResidentProfile $resident)
    {
        $user = Auth::user();
            $debugInfo = [
        'residency_since' => $resident->residency_since,
        'residency_since_formatted' => $resident->residency_since ? $resident->residency_since->format('Y-m-d H:i:s') : null,
        'now' => now(),
        'now_formatted' => now()->format('Y-m-d H:i:s'),
        'timezone' => config('app.timezone'),
    ];
    
    // Log it or dd() to see what's happening
    \Log::debug('Residency Debug', $debugInfo);
        // Check if resident belongs to user's barangay
        if ($resident->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied to this resident record.');
        }

        $resident->load(['user', 'barangay', 'verifier', 'householdHead', 'householdMembers']);

        // Get resident's service history
        $serviceHistory = [
            'documents' => $resident->user->documentRequests()
                                       ->with(['documentType', 'processor'])
                                       ->latest()
                                       ->take(10)
                                       ->get(),
            
            'complaints' => $resident->user->complaints()
                                        ->with(['complaintType', 'assignedOfficial'])
                                        ->latest()
                                        ->take(5)
                                        ->get(),
            
            'permits' => $resident->user->businessPermits()
                                     ->with(['businessPermitType', 'processor'])
                                     ->latest()
                                     ->take(5)
                                     ->get(),
        ];

        return view('barangay.residents.show', compact('resident', 'serviceHistory'));
    }

    /**
     * Verify a resident profile.
     */
    public function verify(Request $request, ResidentProfile $resident)
    {
        $user = Auth::user();
        
        // Check access
        if ($resident->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if ($resident->is_verified) {
            return redirect()->back()->with('warning', 'Resident is already verified.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $resident->verify($user, $request->notes);

        return redirect()->back()->with('success', 'Resident profile verified successfully.');
    }

    /**
     * Unverify a resident profile.
     */
    public function unverify(Request $request, ResidentProfile $resident)
    {
        $user = Auth::user();
        
        // Check access
        if ($resident->barangay_id !== $user->barangay_id) {
            abort(403, 'Access denied.');
        }

        if (!$resident->is_verified) {
            return redirect()->back()->with('warning', 'Resident is not verified.');
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $resident->unverify($request->reason);

        return redirect()->back()->with('success', 'Resident verification removed.');
    }

    /**
     * Show pending residents for verification.
     */

    /**
     * Export residents to Excel.
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        $query = ResidentProfile::byBarangay($barangay->id)->with('user');

        // Apply same filters as index
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

        $filename = 'residents_' . $barangay->slug . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ResidentsExport($residents), $filename);
    }

// FILE: app/Http/Controllers/Barangay/ResidentController.php - ADD this method after verify()

/**
 * Re-verify a resident (re-check eligibility)
 */
public function reverify(Request $request, ResidentProfile $resident)
{
    $user = Auth::user();
    
    // Check access
    if ($resident->barangay_id !== $user->barangay_id) {
        abort(403, 'Access denied.');
    }

    $request->validate([
        'reason' => 'required|string|max:500'
    ]);

    DB::beginTransaction();
    try {
        $residentUser = $resident->user;
        $errors = [];
        $warnings = [];
        $changes = [];
        
        // ============================================
        // CHECK 1: RBI RECORD EXISTS
        // ============================================
        if (!$resident->rbi_inhabitant_id) {
            // Try to find and auto-link RBI record
            $rbiRecord = \App\Models\BarangayInhabitant::where('barangay_id', $resident->barangay_id)
                ->where('status', 'active')
                ->whereNull('user_id')
                ->where(function($query) use ($residentUser) {
                    $query->where('first_name', 'LIKE', '%' . $residentUser->first_name . '%')
                          ->where('last_name', 'LIKE', '%' . $residentUser->last_name . '%');
                })
                ->where('date_of_birth', $residentUser->birth_date)
                ->first();

            if ($rbiRecord) {
                // ✅ FOUND - Auto-link it
                $rbiRecord->update(['user_id' => $residentUser->id]);
                $resident->rbi_inhabitant_id = $rbiRecord->id;
                $resident->residency_since = $rbiRecord->residency_since ?? $rbiRecord->registered_at;
                $resident->save();
                
                $changes[] = "✓ RBI record found and linked (ID: {$rbiRecord->id})";
            } else {
                $errors[] = "❌ NOT IN RBI REGISTRY";
            }
        } else {
            $rbiRecord = $resident->rbiInhabitant;
            
            // Check if RBI record is still active
            if ($rbiRecord->status !== 'active') {
                $errors[] = "❌ RBI RECORD IS INACTIVE - Status: {$rbiRecord->status}";
            } else {
                $warnings[] = "✓ RBI record still active (ID: {$rbiRecord->id})";
            }
        }

        // ============================================
        // CHECK 2: 6-MONTH RESIDENCY REQUIREMENT
        // ============================================
        if ($resident->rbi_inhabitant_id) {
            $residencySince = $resident->residency_since;
            
            if ($residencySince) {
                $monthsResided = now()->diffInMonths($residencySince);
                
                if ($monthsResided < 6) {
                    $remainingMonths = 6 - $monthsResided;
                    $eligibleDate = \Carbon\Carbon::parse($residencySince)->addMonths(6)->format('F d, Y');
                    
                    $errors[] = "❌ RESIDENCY REQUIREMENT NOT MET - {$monthsResided} month(s). Eligible on: {$eligibleDate}";
                } else {
                    $warnings[] = "✓ Residency requirement met ({$monthsResided} months)";
                }
            } else {
                $errors[] = "❌ RESIDENCY DATE MISSING";
            }
        }

        // ============================================
        // CHECK 3: PENDING COMPLAINTS (Placeholder)
        // ============================================
        // TODO: Add later
        // $pendingComplaints = $residentUser->complaints()->whereIn('status', ['filed', 'under_review'])->count();
        // if ($pendingComplaints > 0) {
        //     $errors[] = "❌ HAS {$pendingComplaints} PENDING COMPLAINT(S)";
        // }

        // ============================================
        // UPDATE VERIFICATION STATUS
        // ============================================
        $reverifyNote = "\n\n[" . now()->format('Y-m-d H:i') . "] RE-VERIFIED by " . $user->name;
        $reverifyNote .= "\nReason: " . $request->reason;
        
        if (!empty($errors)) {
            // Failed re-verification - mark as unverified
            $resident->is_verified = false;
            $resident->verified_by = null;
            $resident->verified_at = null;
            $resident->verification_status = 'failed_reverification';
            
            $reverifyNote .= "\n\n❌ RE-VERIFICATION FAILED:";
            $reverifyNote .= "\n" . implode("\n", $errors);
            
            if (!empty($warnings)) {
                $reverifyNote .= "\n\nSystem Notes:\n" . implode("\n", $warnings);
            }
            if (!empty($changes)) {
                $reverifyNote .= "\n\nChanges Made:\n" . implode("\n", $changes);
            }
            
            $resident->verification_notes = $resident->verification_notes . $reverifyNote;
            $resident->save();

            DB::commit();

            return redirect()->back()->with('warning', "⚠️ Re-verification completed. Account is now UNVERIFIED due to:\n\n" . implode("\n", $errors));
            
        } else {
            // Passed re-verification - mark as verified
            $resident->is_verified = true;
            $resident->verified_by = $user->id;
            $resident->verified_at = now();
            $resident->verification_status = 'verified';
            
            $reverifyNote .= "\n\n✅ RE-VERIFICATION PASSED";
            
            if (!empty($warnings)) {
                $reverifyNote .= "\n" . implode("\n", $warnings);
            }
            if (!empty($changes)) {
                $reverifyNote .= "\n" . implode("\n", $changes);
            }
            
            $resident->verification_notes = $resident->verification_notes . $reverifyNote;
            $resident->save();

            DB::commit();

            $successMessage = "✅ Re-verification successful! Account is now VERIFIED.";
            if (!empty($changes)) {
                $successMessage .= "\n\n" . implode("\n", $changes);
            }

            return redirect()->back()->with('success', $successMessage);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Resident Re-verification Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Re-verification failed: ' . $e->getMessage());
    }
}




// Add this method to App\Http\Controllers\Barangay\ResidentController
// Replace linkRbi method in App\Http\Controllers\Barangay\ResidentController

public function linkRbi(Request $request, ResidentProfile $resident)
{
    $request->validate([
        'rbi_inhabitant_id' => 'required|exists:barangay_inhabitants,id',
        'notes' => 'nullable|string|max:1000',
    ]);

    $rbi = \App\Models\BarangayInhabitant::findOrFail($request->rbi_inhabitant_id);

    // Check if RBI is already linked
    if ($rbi->user_id) {
        return back()->with('error', 'This RBI record is already linked to another account.');
    }

    // Check if same barangay
    if ($rbi->barangay_id !== $resident->barangay_id) {
        return back()->with('error', 'RBI record must be from the same barangay.');
    }

    \DB::beginTransaction();
    try {
        // Link RBI to user
        $rbi->user_id = $resident->user_id;
        $rbi->account_linked = true;
        $rbi->account_linked_at = now();
        $rbi->save();

        // Update resident profile
        $resident->rbi_inhabitant_id = $rbi->id;
        $resident->verification_status = 'verified';
        $resident->residency_since = $rbi->residency_since ?? $rbi->registered_at;
        
        $linkNote = '[' . now()->format('Y-m-d H:i') . '] Manually linked to RBI by ' . auth()->user()->name;
        if ($request->notes) {
            $linkNote .= ': ' . $request->notes;
        }
        
        $resident->verification_notes = $resident->verification_notes 
            ? $resident->verification_notes . "\n\n" . $linkNote 
            : $linkNote;
        
        $resident->save();

        \DB::commit();

        return back()->with('success', 'Account successfully linked to RBI record!');
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Failed to link RBI: ' . $e->getMessage());
        return back()->with('error', 'Failed to link: ' . $e->getMessage());
    }
}













///end of class
}