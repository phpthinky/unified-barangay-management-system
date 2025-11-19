<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\SiteSettings;
use App\Models\User;
use App\Models\ResidentProfile;
use App\Models\DocumentRequest;
use App\Models\BusinessPermit;
use App\Models\BarangayInhabitant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

use App\Traits\HasEmailVerification;


class PublicController extends Controller
{
    // ... your existing methods (index, about, barangays, etc.)

   

    /**
     * Show the main landing page.
     */
    public function index()
    {
        $settings = SiteSettings::first();
        $barangays = Barangay::active()->orderBy('name')->get();
        
        $stats = [
            'total_barangays' => Barangay::active()->count(),
            'total_residents' => ResidentProfile::where('is_verified', true)->count(),
            'documents_issued' => DocumentRequest::where('status', 'approved')->count(),
            'permits_issued' => BusinessPermit::where('status', 'approved')->count(),
        ];

        return view('public.index', compact('settings', 'barangays', 'stats'));
    }

    /**
     * Show about page.
     */
    public function about()
    {
        $settings = SiteSettings::first();
        return view('public.about', compact('settings'));
    }

    /**
     * Show all barangays.
     */
    public function barangays()
    {
        $barangays = Barangay::active()->orderBy('name')->get();
        return view('public.barangays', compact('barangays'));
    }

    /**
     * Show available services.
     */
    public function services()
    {
        $settings = SiteSettings::first();
        return view('public.services', compact('settings'));
    }

    /**
     * Show specific barangay home page with officials hierarchy
     */
    public function barangayHome(Barangay $barangay)
    {
        if (!$barangay->is_active) {
            abort(404, 'Barangay not found or inactive.');
        }

        // Use correct relationship names based on your actual model relationships
        $stats = [
            'total_residents' => ResidentProfile::where('barangay_id', $barangay->id)
                            ->where('is_verified', true)
                            ->count(),
            'total_households' => 0, // Adjust based on your actual Household model
            'documents_issued' => DocumentRequest::where('barangay_id', $barangay->id)
                            ->where('status', 'approved')
                            ->count(),
            'active_permits' => BusinessPermit::where('barangay_id', $barangay->id)
                            ->where('status', 'approved')
                            ->count(),
        ];

        // Get barangay officials from organizational chart (new system)
        $officials = \App\Models\BarangayOfficial::where('barangay_id', $barangay->id)
            ->active()
            ->ordered()
            ->get();

        // Get active announcements for public
        $announcements = \App\Models\Announcement::where('barangay_id', $barangay->id)
            ->published()
            ->where('show_on_public', true)
            ->ordered()
            ->limit(5)
            ->get();

        return view('public.barangay.home', compact(
            'barangay',
            'stats',
            'officials',
            'announcements'
        ));
    }

    /**
     * Show barangay officials page with organized chart
     */
    public function barangayOfficials(Barangay $barangay)
    {
        if (!$barangay->is_active) {
            abort(404);
        }

        // Get officials organized by hierarchy
        $captain = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->where('name', 'barangay-captain');
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->first();

        $councilors = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->where('name', 'barangay-councilor');
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->orderBy('councilor_number')
            ->get();

        $secretary = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->where('name', 'barangay-secretary');
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->first();

        $treasurer = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->where('name', 'barangay-treasurer');
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->first();

        $staff = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['barangay-staff', 'barangay-secretary', 'barangay-treasurer']);
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->whereDoesntHave('roles', function($query) {
                $query->whereIn('name', ['barangay-captain', 'barangay-councilor', 'lupon-member']);
            })
            ->get();

        $luponMembers = $barangay->users()
            ->whereHas('roles', function($query) {
                $query->where('name', 'lupon-member');
            })
            ->where('is_active', true)
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        return view('public.barangay.officials', compact(
            'barangay', 
            'captain',
            'councilors',
            'secretary',
            'treasurer',
            'staff',
            'luponMembers'
        ));
    }


    /**
     * Get redirect with appropriate message based on verification status
     */
    private function getRedirectWithMessage($verificationStatus, $rbiInhabitantId)
    {
        $messages = [
            'pending_verification' => [
                'type' => 'success',
                'message' => $rbiInhabitantId 
                    ? 'Registration successful! Your RBI record was found and linked. Please wait for staff verification.'
                    : 'Registration successful! Please wait for barangay staff to verify your account.',
            ],
            'pending_rbi_match' => [
                'type' => 'info',
                'message' => 'Registration successful! We found possible RBI matches. Barangay staff will verify and link your account.',
            ],
            'rbi_not_found' => [
                'type' => 'warning',
                'message' => 'Registration successful! However, we could not find your RBI record. Please visit the barangay hall to register in RBI.',
            ],
            'not_in_rbi' => [
                'type' => 'info',
                'message' => 'Registration successful! You need to register at the barangay hall (RBI) before you can request documents. Visit the barangay office.',
            ],
        ];

        $messageData = $messages[$verificationStatus] ?? [
            'type' => 'success',
            'message' => 'Registration successful! Please wait for verification.',
        ];

        return redirect()->route('resident.dashboard')
            ->with($messageData['type'], $messageData['message']);
    }
    /**
     * Show barangay services.
     */
    public function barangayServices(Barangay $barangay)
    {
        if (!$barangay->is_active) {
            abort(404);
        }

        return view('public.barangay.services', compact('barangay'));
    }

    /**
     * Show barangay contact information.
     */
    public function barangayContact(Barangay $barangay)
    {
        if (!$barangay->is_active) {
            abort(404);
        }

        return view('public.barangay.contact', compact('barangay'));
    }

    /**
     * Track document request.
     */
    public function trackRequest($tracking_number)
    {
        $documentRequest = DocumentRequest::with(['documentType', 'user', 'barangay', 'processor'])
            ->where('tracking_number', $tracking_number)
            ->first();

        $isMobile = preg_match("/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i", request()->userAgent());
        $isQrAccess = request()->has('qr') || $isMobile;

        if (!$documentRequest) {
            return view('public.track-request', [
                'found' => false,
                'tracking_number' => $tracking_number,
                'isQrAccess' => $isQrAccess,
                'isMobile' => $isMobile
            ]);
        }

        return view('public.track-request', [
            'found' => true,
            'documentRequest' => $documentRequest,
            'isQrAccess' => $isQrAccess,
            'isMobile' => $isMobile
        ]);
    }

    /**
     * Verify document using QR code.
     */
    public function verifyDocument($qrCode)
    {
        // Try to find document request
        $documentRequest = DocumentRequest::where('qr_code', $qrCode)
                                        ->where('status', 'approved')
                                        ->first();
        
        if ($documentRequest) {
            return view('public.verify-document', [
                'document' => $documentRequest,
                'type' => 'document',
                'valid' => true
            ]);
        }

        // Try to find business permit
        $businessPermit = BusinessPermit::where('qr_code', $qrCode)
                                      ->where('status', 'approved')
                                      ->first();
        
        if ($businessPermit) {
            return view('public.verify-document', [
                'document' => $businessPermit,
                'type' => 'permit',
                'valid' => true
            ]);
        }

        return view('public.verify-document', [
            'document' => null,
            'valid' => false,
            'qrCode' => $qrCode
        ]);
    }

    /**
     * Show demo account page.
     */
    public function demoAccount()
    {
        return view('public.demo');
    }
    
    
    
    
    
    
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/
/*-------------------------------------------------------------------------------------------------------------------*/










    public function barangayRegister(Barangay $barangay)
    {
        if (!$barangay->is_active) {
            return redirect()->route('public.barangays')
                ->with('error', 'This barangay is currently not accepting registrations.');
        }

        session()->forget(['rbi_check', 'rbi_search']);

        return view('public.barangay.register.index', compact('barangay'));
    }

    public function registerContinue(Request $request, Barangay $barangay)
    {
        $request->validate([
            'has_rbi' => 'required|in:yes,no',
        ]);

        if ($request->has_rbi === 'yes') {
            return redirect()->route('public.barangay.register.rbi-check', $barangay->slug);
        } else {
            return redirect()->route('public.barangay.register.full-form', $barangay->slug);
        }
    }

    public function registerRbiCheck(Barangay $barangay)
    {
        return view('public.barangay.register.rbi-check', compact('barangay'));
    }

    /**
     * ✅ FIXED: Proper handling for duplicate accounts
     */
    public function registerCheckRbi(Request $request, Barangay $barangay)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:10',
            'birth_date' => 'required|date|before:today',
        ]);

        // ✅ CHECK 1: Does this person already have an account?
        $duplicateCheck = BarangayInhabitant::personHasOnlineAccount(
            $validated['first_name'],
            $validated['last_name'], 
            $validated['birth_date'],
            $barangay->id
        );

        if ($duplicateCheck['exists']) {
            \Log::info('Existing account detected during RBI check', [
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'birth_date' => $validated['birth_date'],
                'barangay' => $barangay->name,
                'duplicate_type' => $duplicateCheck['type']
            ]);
            
            // ✅ Redirect to login page with helpful message
            return redirect()->route('login')
                ->with('info', 'You already have an account! Please login below.')
                ->with('suggested_email', $duplicateCheck['user']->email ?? null);
        }

        // ✅ CHECK 2: Find available RBI record (not linked to any account)
        $rbiRecord = BarangayInhabitant::findVerifiedRecord(
            $validated['first_name'],
            $validated['last_name'],
            $validated['birth_date'],
            $barangay->id
        );

        if ($rbiRecord) {
            // ✅ RBI found and available - proceed to password setup
            session([
                'rbi_check' => [
                    'found' => true,
                    'rbi_id' => $rbiRecord->id,
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'suffix' => $validated['suffix'],
                    'birth_date' => $validated['birth_date'],
                    'rbi_registry_number' => $rbiRecord->registry_number,
                ]
            ]);

            return redirect()->route('public.barangay.register.password', $barangay->slug);
        } else {
            // ✅ RBI not found - show not found page
            session([
                'rbi_search' => [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name' => $validated['last_name'],
                    'suffix' => $validated['suffix'],
                    'birth_date' => $validated['birth_date'],
                ]
            ]);

            return redirect()->route('public.barangay.register.not-found', $barangay->slug);
        }
    }

    public function registerPassword(Barangay $barangay)
    {
        if (!session('rbi_check.found')) {
            return redirect()->route('public.barangay.register', $barangay->slug)
                ->with('error', 'Session expired. Please start again.');
        }

        return view('public.barangay.register.password', compact('barangay'));
    }

    public function registerCompleteRbi(Request $request, Barangay $barangay)
    {
        $rbiCheck = session('rbi_check');
        
        if (!$rbiCheck || !$rbiCheck['found']) {
            return redirect()->route('public.barangay.register', $barangay->slug)
                ->with('error', 'Session expired. Please start again.');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $rbiRecord = BarangayInhabitant::find($rbiCheck['rbi_id']);

            if (!$rbiRecord || $rbiRecord->user_id) {
                DB::rollBack();
                return redirect()->route('public.barangay.register', $barangay->slug)
                    ->with('error', 'RBI record is no longer available.');
            }

            $user = User::create([
                'first_name' => $rbiCheck['first_name'],
                'middle_name' => $rbiCheck['middle_name'],
                'last_name' => $rbiCheck['last_name'],
                'suffix' => $rbiCheck['suffix'],
                'name' => trim($rbiCheck['first_name'] . ' ' . 
                         ($rbiCheck['middle_name'] ? $rbiCheck['middle_name'] . ' ' : '') . 
                         $rbiCheck['last_name'] . 
                         ($rbiCheck['suffix'] ? ' ' . $rbiCheck['suffix'] : '')),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'barangay_id' => $barangay->id,
                'birth_date' => $rbiCheck['birth_date'],
                'gender' => strtolower($rbiRecord->sex ?? 'male'),
                'address' => ($rbiRecord->house_number ? 'House No. ' . $rbiRecord->house_number . ', ' : '') . 
                            ($rbiRecord->zone_sitio ?? ''),
                'contact_number' => $rbiRecord->contact_number ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('resident');

            $rbiRecord->update(['user_id' => $user->id]);

            ResidentProfile::create([
                'user_id' => $user->id,
                'barangay_id' => $barangay->id,
                'rbi_inhabitant_id' => $rbiRecord->id,
                'purok_zone' => $rbiRecord->zone_sitio ?? 'N/A',
                'civil_status' => strtolower($rbiRecord->civil_status ?? 'single'),
                'nationality' => $rbiRecord->citizenship ?? 'Filipino',
                'occupation' => $rbiRecord->occupation ?? 'N/A',
                'educational_attainment' => $rbiRecord->educational_attainment ?? 'N/A',
                'emergency_contact_name' => $rbiRecord->emergency_contact_name ?? null,
                'emergency_contact_number' => $rbiRecord->emergency_contact_number ?? null,
                'emergency_contact_relationship' => $rbiRecord->emergency_contact_relationship ?? null,
                'residency_since' => $rbiRecord->residency_since ?? $rbiRecord->registered_at,
                'residency_type' => 'permanent',
            ]);

            DB::commit();
            
            session()->forget(['rbi_check', 'rbi_search']);
            
            $user->sendEmailVerificationCode();
            
            auth()->login($user);

            return redirect()->route('verification.notice')
                ->with('success', '✅ Account successfully linked to RBI record #' . $rbiRecord->registry_number . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('RBI Registration Error: ' . $e->getMessage());
            
            return redirect()->route('public.barangay.register.password', $barangay->slug)
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }

    public function registerNotFound(Barangay $barangay)
    {
        if (!session('rbi_search')) {
            return redirect()->route('public.barangay.register', $barangay->slug);
        }

        return view('public.barangay.register.not-found', compact('barangay'));
    }

    public function registerFullForm(Barangay $barangay)
    {
        return view('public.barangay.register.full-form', compact('barangay'));
    }

    /**
     * ✅ FIXED: Full form with proper duplicate handling
     */
    public function registerCompleteFull(Request $request, Barangay $barangay)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'suffix' => 'nullable|string|max:10',
            'place_of_birth' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'purok_zone' => 'required|string|max:100',
            'residency_since' => 'required|date',
            'residency_type' => 'required|in:permanent,temporary,transient',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'nationality' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'educational_attainment' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
        ]);

        // ✅ Duplicate check
        $duplicateCheck = BarangayInhabitant::personHasOnlineAccount(
            $validated['first_name'],
            $validated['last_name'], 
            $validated['birth_date'],
            $barangay->id
        );

        if ($duplicateCheck['exists']) {
            \Log::info('Duplicate full registration attempt', [
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'barangay' => $barangay->name,
            ]);
            
            return redirect()->route('login')
                ->with('info', 'You already have an account! Please login below.')
                ->with('suggested_email', $duplicateCheck['user']->email ?? null);
        }

        DB::beginTransaction();
        try {
            $existingRbiRecord = BarangayInhabitant::findVerifiedRecord(
                $validated['first_name'],
                $validated['last_name'],
                $validated['birth_date'],
                $barangay->id
            );

            $user = User::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'],
                'name' => trim($validated['first_name'] . ' ' . 
                         ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . 
                         $validated['last_name'] . 
                         ($validated['suffix'] ? ' ' . $validated['suffix'] : '')),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'barangay_id' => $barangay->id,
                'birth_date' => $validated['birth_date'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'contact_number' => $validated['contact_number'],
                'is_active' => true,
            ]);

            $user->assignRole('resident');

            $rbiInhabitantId = null;
            $residencySince = $validated['residency_since'];

            if ($existingRbiRecord) {
                $existingRbiRecord->update(['user_id' => $user->id]);
                $rbiInhabitantId = $existingRbiRecord->id;
                $residencySince = $existingRbiRecord->residency_since ?? $existingRbiRecord->registered_at;
            }

            ResidentProfile::create([
                'user_id' => $user->id,
                'barangay_id' => $barangay->id,
                'rbi_inhabitant_id' => $rbiInhabitantId,
                'purok_zone' => $validated['purok_zone'],
                'civil_status' => $validated['civil_status'],
                'nationality' => $validated['nationality'],
                'occupation' => $validated['occupation'],
                'educational_attainment' => $validated['educational_attainment'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'emergency_contact_number' => $validated['emergency_contact_number'],
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'],
                'residency_since' => $residencySince,
                'residency_type' => $validated['residency_type'],
            ]);

            DB::commit();
            
            session()->forget(['rbi_check', 'rbi_search']);
            
            $user->sendEmailVerificationCode();
            
            auth()->login($user);

            $message = $rbiInhabitantId 
                ? '✅ Account created and linked to RBI!'
                : '✅ Account created! Visit barangay office to register in RBI.';

            return redirect()->route('verification.notice')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Full Registration Error: ' . $e->getMessage());
            
            return redirect()->route('public.barangay.register.full-form', $barangay->slug)
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }

    
    
    
    
    
    
    
    
    
    
}