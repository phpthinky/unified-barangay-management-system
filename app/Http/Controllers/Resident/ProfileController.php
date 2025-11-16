<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ResidentProfile;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'resident']);
    }

    /**
     * Show resident profile.
     */
    public function show()
    {
        $user = Auth::user();
        $residentProfile = $user->residentProfile;

        if (!$residentProfile) {
            return redirect()->route('resident.profile.create')
                           ->with('info', 'Please create your resident profile to continue.');
        }

        $residentProfile->load(['barangay', 'verifier', 'householdHead', 'householdMembers']);

        return view('resident.profile.show', compact('residentProfile'));
    }

    /**
     * Show form for creating resident profile.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if profile already exists
        if ($user->residentProfile) {
            return redirect()->route('resident.profile.show')
                           ->with('info', 'You already have a resident profile.');
        }

        $barangay = $user->barangay;

        return view('resident.profile.create', compact('user', 'barangay'));
    }

    /**
     * Store resident profile.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if profile already exists
        if ($user->residentProfile) {
            return redirect()->route('resident.profile.show')
                           ->with('info', 'You already have a resident profile.');
        }

        $request->validate([
            // Basic resident info
            'purok_zone' => 'required|string|max:100',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'nationality' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'occupation' => 'required|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'educational_attainment' => 'required|string|max:255',
            
            // Emergency contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
            
            // Residency info
            'residency_since' => 'required|date|before_or_equal:today',
            'residency_type' => 'required|in:permanent,temporary,transient',
            'is_registered_voter' => 'required|boolean',
            'precinct_number' => 'nullable|string|max:20',
            
            // Special classifications
            'is_pwd' => 'nullable|boolean',
            'pwd_id_number' => 'nullable|string|max:50',
            'is_senior_citizen' => 'nullable|boolean',
            'is_solo_parent' => 'nullable|boolean',
            'is_4ps_beneficiary' => 'nullable|boolean',
            
            // ID documents
            'id_documents' => 'nullable|array',
            'id_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        // Handle ID document uploads
        $uploadedFiles = [];
        if ($request->hasFile('id_files')) {
            foreach ($request->file('id_files') as $file) {
                $filename = 'id_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/documents'), $filename);
                $uploadedFiles[] = $filename;
            }
        }

        // Create resident profile
        ResidentProfile::create([
            'user_id' => $user->id,
            'barangay_id' => $user->barangay_id,
            'purok_zone' => $request->purok_zone,
            'civil_status' => $request->civil_status,
            'nationality' => $request->nationality,
            'religion' => $request->religion,
            'occupation' => $request->occupation,
            'monthly_income' => $request->monthly_income,
            'educational_attainment' => $request->educational_attainment,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'id_documents' => $request->id_documents,
            'uploaded_files' => $uploadedFiles,
            'residency_since' => $request->residency_since,
            'residency_type' => $request->residency_type,
            'is_registered_voter' => $request->boolean('is_registered_voter'),
            'precinct_number' => $request->precinct_number,
            'is_pwd' => $request->boolean('is_pwd'),
            'pwd_id_number' => $request->pwd_id_number,
            'is_senior_citizen' => $request->boolean('is_senior_citizen'),
            'is_solo_parent' => $request->boolean('is_solo_parent'),
            'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
            'is_verified' => false, // Requires barangay verification
        ]);

        return redirect()->route('resident.profile.show')
                       ->with('success', 'Profile created successfully. Please wait for barangay staff verification.');
    }

    /**
     * Show form for editing resident profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $residentProfile = $user->residentProfile;

        if (!$residentProfile) {
            return redirect()->route('resident.profile.create');
        }

        return view('resident.profile.edit', compact('residentProfile'));
    }

    /**
     * Update resident profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $residentProfile = $user->residentProfile;

        if (!$residentProfile) {
            return redirect()->route('resident.profile.create');
        }

        $request->validate([
            // Basic resident info
            'purok_zone' => 'required|string|max:100',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'nationality' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'occupation' => 'required|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'educational_attainment' => 'required|string|max:255',
            
            // Emergency contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
            
            // Special classifications
            'is_pwd' => 'nullable|boolean',
            'pwd_id_number' => 'nullable|string|max:50',
            'is_senior_citizen' => 'nullable|boolean',
            'is_solo_parent' => 'nullable|boolean',
            'is_4ps_beneficiary' => 'nullable|boolean',
        ]);

        // Update profile (excluding verification fields)
        $residentProfile->update([
            'purok_zone' => $request->purok_zone,
            'civil_status' => $request->civil_status,
            'nationality' => $request->nationality,
            'religion' => $request->religion,
            'occupation' => $request->occupation,
            'monthly_income' => $request->monthly_income,
            'educational_attainment' => $request->educational_attainment,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'is_pwd' => $request->boolean('is_pwd'),
            'pwd_id_number' => $request->pwd_id_number,
            'is_senior_citizen' => $request->boolean('is_senior_citizen'),
            'is_solo_parent' => $request->boolean('is_solo_parent'),
            'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
        ]);

        // Note: Major changes might require re-verification
        $majorChanges = ['purok_zone', 'civil_status', 'occupation'];
        $hasChanges = false;
        
        foreach ($majorChanges as $field) {
            if ($residentProfile->wasChanged($field)) {
                $hasChanges = true;
                break;
            }
        }

        $message = 'Profile updated successfully.';
        if ($hasChanges && $residentProfile->is_verified) {
            $message .= ' Note: Major changes may require re-verification by barangay staff.';
        }

        return redirect()->route('resident.profile.show')->with('success', $message);
    }

    /**
     * Upload ID documents.
     */
    public function uploadId(Request $request)
    {
        $user = Auth::user();
        $residentProfile = $user->residentProfile;

        if (!$residentProfile) {
            return redirect()->route('resident.profile.create');
        }

        $request->validate([
            'id_type' => 'required|string|max:100',
            'id_number' => 'nullable|string|max:100',
            'id_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        // Handle file upload
        $file = $request->file('id_file');
        $filename = 'id_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/documents'), $filename);

        // Add to existing ID documents
        $idDocuments = $residentProfile->id_documents ?? [];
        $idDocuments[$request->id_type] = $request->id_number;

        $uploadedFiles = $residentProfile->uploaded_files ?? [];
        $uploadedFiles[] = $filename;

        $residentProfile->update([
            'id_documents' => $idDocuments,
            'uploaded_files' => $uploadedFiles
        ]);

        return redirect()->back()->with('success', 'ID document uploaded successfully.');
    }
}