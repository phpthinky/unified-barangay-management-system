<?php

namespace App\Http\Controllers\Barangay;

use App\Http\Controllers\Controller;
use App\Models\BarangayInhabitant;
use App\Models\Barangay;
use App\Models\ResidentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InhabitantController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        $query = BarangayInhabitant::byBarangay($barangay->id)
                                  ->with(['registeredBy', 'verifiedBy']);

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->verified();
            } elseif ($request->status === 'unverified') {
                $query->unverified();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('zone')) {
            $query->where('zone_sitio', $request->zone);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('registry_number', 'like', "%{$search}%");
            });
        }

        $inhabitants = $query->orderBy('last_name')->orderBy('first_name')->paginate(20);

        $stats = [
            'total' => BarangayInhabitant::byBarangay($barangay->id)->active()->count(),
            'verified' => BarangayInhabitant::byBarangay($barangay->id)->verified()->count(),
            'unverified' => BarangayInhabitant::byBarangay($barangay->id)->unverified()->count(),
            'households' => BarangayInhabitant::byBarangay($barangay->id)->householdHeads()->count(),
            'male' => BarangayInhabitant::byBarangay($barangay->id)->where('sex', 'Male')->count(),
            'female' => BarangayInhabitant::byBarangay($barangay->id)->where('sex', 'Female')->count(),
        ];

        $zones = BarangayInhabitant::byBarangay($barangay->id)
                                   ->select('zone_sitio')
                                   ->distinct()
                                   ->orderBy('zone_sitio')
                                   ->pluck('zone_sitio');

        return view('barangay.inhabitants.index', compact('barangay', 'inhabitants', 'stats', 'zones'));
    }

    public function create()
    {
        $user = Auth::user();
        $barangay = $user->barangay;

        if (!$barangay) {
            return redirect()->route('dashboard')->with('error', 'No barangay assigned.');
        }

        return view('barangay.inhabitants.create', compact('barangay'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'ext' => 'nullable|string|max:10',
            'house_number' => 'nullable|string|max:50',
            'zone_sitio' => 'required|string|max:100',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|string|max:50',
            'citizenship' => 'required|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'educational_attainment' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'residency_since' => 'required|date',
            'residency_type' => 'required|in:permanent,temporary,transient',
            'cedula_number' => 'nullable|string|max:50',
            'certificate_of_residency_number' => 'nullable|string|max:50',
            'proof_of_residency_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'household_number' => 'nullable|string|max:50',
            'is_household_head' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $barangay = auth()->user()->barangay;
            
            $latestInhabitant = BarangayInhabitant::where('barangay_id', $barangay->id)
                ->latest('id')
                ->first();
            
            $nextNumber = $latestInhabitant 
                ? (int) substr($latestInhabitant->registry_number, -5) + 1 
                : 1;
            
            $registryNumber = 'RBI-' . strtoupper($barangay->slug) . '-' . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'inhabitant_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/inhabitants/photos');
                
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $photoPath = 'uploads/inhabitants/photos/' . $filename;
            }

            $proofFilePath = null;
            if ($request->hasFile('proof_of_residency_file')) {
                $file = $request->file('proof_of_residency_file');
                $filename = 'proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/inhabitants/proof-of-residency');
                
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $proofFilePath = 'uploads/inhabitants/proof-of-residency/' . $filename;
            }

            // ✅ REMOVED: 'is_verified' => false
            // The boot() method will handle auto-verification
            $inhabitant = BarangayInhabitant::create([
                'barangay_id' => $barangay->id,
                'registry_number' => $registryNumber,
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'ext' => $validated['ext'] ?? null,
                'house_number' => $validated['house_number'] ?? null,
                'zone_sitio' => $validated['zone_sitio'],
                'place_of_birth' => $validated['place_of_birth'],
                'date_of_birth' => $validated['date_of_birth'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'citizenship' => $validated['citizenship'],
                'occupation' => $validated['occupation'] ?? null,
                'educational_attainment' => $validated['educational_attainment'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
                'residency_since' => $validated['residency_since'],
                'residency_type' => $validated['residency_type'],
                'cedula_number' => $validated['cedula_number'] ?? null,
                'certificate_of_residency_number' => $validated['certificate_of_residency_number'] ?? null,
                'proof_of_residency_file' => $proofFilePath,
                'household_number' => $validated['household_number'] ?? null,
                'is_household_head' => $request->has('is_household_head'),
                'photo_path' => $photoPath,
                'remarks' => $validated['remarks'] ?? null,
                'registered_at' => now(),
                'registered_by' => auth()->id(),
                'status' => 'active',
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('barangay.inhabitants.show', $inhabitant)
                ->with('success', '✅ Inhabitant registered successfully and auto-verified! Registry Number: ' . $registryNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Inhabitant Registration Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            if (isset($photoPath) && File::exists(public_path($photoPath))) {
                File::delete(public_path($photoPath));
            }
            if (isset($proofFilePath) && File::exists(public_path($proofFilePath))) {
                File::delete(public_path($proofFilePath));
            }
            
            return redirect()->back()
                ->with('error', 'Failed to register inhabitant: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(BarangayInhabitant $inhabitant)
    {
        $inhabitant->load(['householdMembers', 'registeredBy', 'verifiedBy']);
        
        return view('barangay.inhabitants.show', compact('inhabitant'));
    }

    public function edit(BarangayInhabitant $inhabitant)
    {
        if ($inhabitant->barangay_id !== auth()->user()->barangay_id) {
            abort(403, 'Access denied.');
        }

        $barangay = $inhabitant->barangay;

        return view('barangay.inhabitants.edit', compact('inhabitant', 'barangay'));
    }

    public function update(Request $request, BarangayInhabitant $inhabitant)
    {
        if ($inhabitant->barangay_id !== auth()->user()->barangay_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'last_name' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'ext' => 'nullable|string|max:10',
            'house_number' => 'nullable|string|max:50',
            'zone_sitio' => 'required|string|max:100',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|string|max:50',
            'citizenship' => 'required|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'educational_attainment' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'residency_since' => 'required|date',
            'residency_type' => 'required|in:permanent,temporary,transient',
            'cedula_number' => 'nullable|string|max:50',
            'certificate_of_residency_number' => 'nullable|string|max:50',
            'proof_of_residency_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'household_number' => 'nullable|string|max:50',
            'is_household_head' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'ext' => $validated['ext'] ?? null,
                'house_number' => $validated['house_number'] ?? null,
                'zone_sitio' => $validated['zone_sitio'],
                'place_of_birth' => $validated['place_of_birth'],
                'date_of_birth' => $validated['date_of_birth'],
                'sex' => $validated['sex'],
                'civil_status' => $validated['civil_status'],
                'citizenship' => $validated['citizenship'],
                'occupation' => $validated['occupation'] ?? null,
                'educational_attainment' => $validated['educational_attainment'] ?? null,
                'contact_number' => $validated['contact_number'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_number' => $validated['emergency_contact_number'] ?? null,
                'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
                'residency_since' => $validated['residency_since'],
                'residency_type' => $validated['residency_type'],
                'cedula_number' => $validated['cedula_number'] ?? null,
                'certificate_of_residency_number' => $validated['certificate_of_residency_number'] ?? null,
                'household_number' => $validated['household_number'] ?? null,
                'is_household_head' => $request->has('is_household_head'),
                'remarks' => $validated['remarks'] ?? null,
            ];

            if ($request->hasFile('photo')) {
                if ($inhabitant->photo_path && File::exists(public_path($inhabitant->photo_path))) {
                    File::delete(public_path($inhabitant->photo_path));
                }
                
                $file = $request->file('photo');
                $filename = 'inhabitant_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/inhabitants/photos');
                
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $updateData['photo_path'] = 'uploads/inhabitants/photos/' . $filename;
            }

            if ($request->hasFile('proof_of_residency_file')) {
                if ($inhabitant->proof_of_residency_file && File::exists(public_path($inhabitant->proof_of_residency_file))) {
                    File::delete(public_path($inhabitant->proof_of_residency_file));
                }
                
                $file = $request->file('proof_of_residency_file');
                $filename = 'proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/inhabitants/proof-of-residency');
                
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                $updateData['proof_of_residency_file'] = 'uploads/inhabitants/proof-of-residency/' . $filename;
            }

            $inhabitant->update($updateData);

            if ($inhabitant->user_id) {
                $residentProfile = ResidentProfile::where('user_id', $inhabitant->user_id)->first();
                
                if ($residentProfile) {
                    $residentProfile->update([
                        'residency_since' => $validated['residency_since'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('barangay.inhabitants.show', $inhabitant)
                ->with('success', '✅ Inhabitant information updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Inhabitant Update Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Failed to update inhabitant: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function verify(BarangayInhabitant $inhabitant)
    {
        if ($inhabitant->barangay_id !== auth()->user()->barangay_id) {
            abort(403, 'Access denied.');
        }

        DB::beginTransaction();
        try {
            $inhabitant->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', '✅ Inhabitant verified successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Inhabitant Verification Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to verify inhabitant: ' . $e->getMessage());
        }
    }

    public function unverify(BarangayInhabitant $inhabitant)
    {
        if ($inhabitant->barangay_id !== auth()->user()->barangay_id) {
            abort(403, 'Access denied.');
        }

        DB::beginTransaction();
        try {
            $inhabitant->update([
                'is_verified' => false,
                'verified_at' => null,
                'verified_by' => null,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', '⚠️ Inhabitant verification removed.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Inhabitant Unverification Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to unverify inhabitant: ' . $e->getMessage());
        }
    }

    public function destroy(BarangayInhabitant $inhabitant)
    {
        $inhabitant->delete();

        return redirect()->route('barangay.inhabitants.index')
                        ->with('success', 'Inhabitant archived successfully!');
    }

    public function quickCreate($residentId)
    {
        $resident = ResidentProfile::with(['user', 'barangay'])->findOrFail($residentId);
        
        // Check if already has RBI record
        if ($resident->rbiInhabitant) {
            return redirect()->route('barangay.residents.show', $resident->id)
                ->with('warning', 'This resident already has an RBI record.');
        }
      

        return view('barangay.inhabitants.quick-create', compact('resident'));
    }

    /**
     * Store quick RBI record from resident data
     */
    public function quickStore(Request $request, $residentId)
    {
        $resident = ResidentProfile::with(['user', 'barangay'])->findOrFail($residentId);
        
        $request->validate([
           // 'house_number' => 'required|string|max:50',
            'zone_sitio' => 'required|string|max:100',
            'household_number' => 'required|string|max:50',
            'is_household_head' => 'boolean',
            'residency_since' => 'required|date',
            'residency_type' => 'required|string|max:50',
            'contact_number' => 'required|string|max:20',
            'occupation' => 'required|string|max:100',
            'educational_attainment' => 'required|string|max:100',
        ]);

        // Create RBI record
        $rbiRecord = BarangayInhabitant::create([
            'barangay_id' => $resident->barangay_id,
            'first_name' => $resident->user->first_name,
            'last_name' => $resident->user->last_name,
            'middle_name' => $resident->user->middle_name,
            'ext' => $resident->user->suffix,
            'date_of_birth' => $resident->user->birth_date,
            'sex' => $resident->user->gender,
            'civil_status' => $resident->civil_status,
            'citizenship' => $resident->nationality,
            'place_of_birth' => $request->place_of_birth,
            
            // From form
            'house_number' => $request->house_number,
            'zone_sitio' => $request->zone_sitio,
            'household_number' => $request->household_number,
            'is_household_head' => $request->is_household_head ?? false,
            'residency_since' => $request->residency_since,
            'residency_type' => $request->residency_type,
            'contact_number' => $request->contact_number,
            'occupation' => $request->occupation,
            'educational_attainment' => $request->educational_attainment,
            
            // Emergency contact from resident profile
            'emergency_contact_name' => $resident->emergency_contact_name,
            'emergency_contact_number' => $resident->emergency_contact_number,
            'emergency_contact_relationship' => $resident->emergency_contact_relationship,
            
            // Auto-verified since created by barangay staff
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'registered_at' => now(),
            'registered_by' => auth()->id(),
            'status' => 'active',
            'is_active' => true,
            
            // Link to user
            'user_id' => $resident->user_id,
        ]);

        // Generate registry number
        $rbiRecord->update([
            'registry_number' => $rbiRecord->generateRegistryNumber()
        ]);

        // Link back to resident profile
        $resident->update([
            'rbi_inhabitant_id' => $rbiRecord->id
        ]);

        return redirect()->route('barangay.inhabitants.show', $rbiRecord->id)
            ->with('success', 'Resident successfully added to RBI registry!');
    }
}