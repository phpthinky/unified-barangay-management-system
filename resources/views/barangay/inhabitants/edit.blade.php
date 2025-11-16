{{-- FILE: resources/views/barangay/inhabitants/edit.blade.php - COMPLETE UPDATE --}}

@extends('layouts.barangay')

@section('title', 'Edit Inhabitant - ' . $inhabitant->full_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit"></i> Edit Inhabitant
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
        <div>
            <a href="{{ route('barangay.inhabitants.show', $inhabitant) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('barangay.inhabitants.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">EDIT REGISTRY INFORMATION</h5>
                    <small>Registry Number: {{ $inhabitant->registry_number }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('barangay.inhabitants.update', $inhabitant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- NAME (1) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">NAME (1)</h6>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">LAST NAME <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                           value="{{ old('last_name', $inhabitant->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">FIRST NAME (1-2) <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                           value="{{ old('first_name', $inhabitant->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">MIDDLE NAME (1-3)</label>
                                    <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                                           value="{{ old('middle_name', $inhabitant->middle_name) }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">EXT. (1-4)</label>
                                    <select name="ext" class="form-control @error('ext') is-invalid @enderror">
                                        <option value="">None</option>
                                        <option value="Jr." {{ old('ext', $inhabitant->ext) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                        <option value="Sr." {{ old('ext', $inhabitant->ext) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                        <option value="II" {{ old('ext', $inhabitant->ext) == 'II' ? 'selected' : '' }}>II</option>
                                        <option value="III" {{ old('ext', $inhabitant->ext) == 'III' ? 'selected' : '' }}>III</option>
                                        <option value="IV" {{ old('ext', $inhabitant->ext) == 'IV' ? 'selected' : '' }}>IV</option>
                                        <option value="V" {{ old('ext', $inhabitant->ext) == 'V' ? 'selected' : '' }}>V</option>
                                    </select>
                                    @error('ext')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ADDRESS (2) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">ADDRESS (2)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NO (2-1)</label>
                                    <input type="text" name="house_number" class="form-control @error('house_number') is-invalid @enderror" 
                                           value="{{ old('house_number', $inhabitant->house_number) }}" placeholder="House/Block/Lot Number">
                                    @error('house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label class="form-label">NAME OF ZONE/SITIO (2-2) <span class="text-danger">*</span></label>
                                    <input type="text" name="zone_sitio" class="form-control @error('zone_sitio') is-invalid @enderror" 
                                           value="{{ old('zone_sitio', $inhabitant->zone_sitio) }}" placeholder="e.g., Purok 1, Zone A, Sitio Riverside" required>
                                    @error('zone_sitio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- PLACE OF BIRTH (3-3) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">PLACE OF BIRTH (3-3)</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">PLACE OF BIRTH <span class="text-danger">*</span></label>
                                    <input type="text" name="place_of_birth" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                           value="{{ old('place_of_birth', $inhabitant->place_of_birth) }}" placeholder="City/Municipality, Province" required>
                                    @error('place_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- DATE OF BIRTH (4) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">DATE OF BIRTH (4)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">DATE OF BIRTH (mm/dd/yy) <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           value="{{ old('date_of_birth', $inhabitant->date_of_birth->format('Y-m-d')) }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEX (5) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">SEX (5)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">SEX <span class="text-danger">*</span></label>
                                    <select name="sex" class="form-control @error('sex') is-invalid @enderror" required>
                                        <option value="">Select Sex</option>
                                        <option value="Male" {{ old('sex', $inhabitant->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('sex', $inhabitant->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('sex')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CIVIL STATUS (6) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CIVIL STATUS (6)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CIVIL STATUS <span class="text-danger">*</span></label>
                                    <select name="civil_status" class="form-control @error('civil_status') is-invalid @enderror" required>
                                        <option value="">Select Civil Status</option>
                                        <option value="Single" {{ old('civil_status', $inhabitant->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status', $inhabitant->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ old('civil_status', $inhabitant->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('civil_status', $inhabitant->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Divorced" {{ old('civil_status', $inhabitant->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                    @error('civil_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- CITIZENSHIP (7) -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CITIZENSHIP (7)</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CITIZENSHIP <span class="text-danger">*</span></label>
                                    <input type="text" name="citizenship" class="form-control @error('citizenship') is-invalid @enderror" 
                                           value="{{ old('citizenship', $inhabitant->citizenship) }}" required>
                                    @error('citizenship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- OCCUPATION & EDUCATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">OCCUPATION & EDUCATION</h6>
                            <div class="row">
                                <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Occupation</label>
        <select class="form-control @error('occupation') is-invalid @enderror" 
                name="occupation" id="occupation" required>
            <option value="">-- Select Occupation --</option>
            <option value="Student" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Student' ? 'selected' : '' }}>Student</option>
            <option value="Teacher" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="Government Employee" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Government Employee' ? 'selected' : '' }}>Government Employee</option>
            <option value="Private Employee" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Private Employee' ? 'selected' : '' }}>Private Employee</option>
            <option value="Self-Employed" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
            <option value="Business Owner" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Business Owner' ? 'selected' : '' }}>Business Owner</option>
            <option value="Driver" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Driver' ? 'selected' : '' }}>Driver</option>
            <option value="Vendor" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Vendor' ? 'selected' : '' }}>Vendor</option>
            <option value="Farmer" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
            <option value="Fisherman" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Fisherman' ? 'selected' : '' }}>Fisherman</option>
            <option value="Construction Worker" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Construction Worker' ? 'selected' : '' }}>Construction Worker</option>
            <option value="Security Guard" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Security Guard' ? 'selected' : '' }}>Security Guard</option>
            <option value="Housewife/Househusband" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Housewife/Househusband' ? 'selected' : '' }}>Housewife/Househusband</option>
            <option value="Retired" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Retired' ? 'selected' : '' }}>Retired</option>
            <option value="Unemployed" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
            <option value="OFW (Overseas Filipino Worker)" {{ (old('occupation') ?? $resident->occupation ?? '') == 'OFW (Overseas Filipino Worker)' ? 'selected' : '' }}>OFW (Overseas Filipino Worker)</option>
            <option value="Healthcare Worker" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Healthcare Worker' ? 'selected' : '' }}>Healthcare Worker</option>
            <option value="Engineer" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Engineer' ? 'selected' : '' }}>Engineer</option>
            <option value="Accountant" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
            <option value="Sales Representative" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Sales Representative' ? 'selected' : '' }}>Sales Representative</option>
            <option value="Mechanic" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Mechanic' ? 'selected' : '' }}>Mechanic</option>
            <option value="Electrician" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Electrician' ? 'selected' : '' }}>Electrician</option>
            <option value="Plumber" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Plumber' ? 'selected' : '' }}>Plumber</option>
            <option value="Carpenter" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Carpenter' ? 'selected' : '' }}>Carpenter</option>
            <option value="Cook/Chef" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Cook/Chef' ? 'selected' : '' }}>Cook/Chef</option>
            <option value="Domestic Helper" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Domestic Helper' ? 'selected' : '' }}>Domestic Helper</option>
            <option value="Others" {{ (old('occupation') ?? $resident->occupation ?? '') == 'Others' ? 'selected' : '' }}>Others</option>
        </select>
        @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">EDUCATIONAL ATTAINMENT</label>
                                    <select name="educational_attainment" class="form-control @error('educational_attainment') is-invalid @enderror">
                                        <option value="">Select Education Level</option>
                                        <option value="No Formal Education" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'No Formal Education' ? 'selected' : '' }}>No Formal Education</option>
                                        <option value="Elementary Undergraduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'Elementary Undergraduate' ? 'selected' : '' }}>Elementary Undergraduate</option>
                                        <option value="Elementary Graduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'Elementary Graduate' ? 'selected' : '' }}>Elementary Graduate</option>
                                        <option value="High School Undergraduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'High School Undergraduate' ? 'selected' : '' }}>High School Undergraduate</option>
                                        <option value="High School Graduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'High School Graduate' ? 'selected' : '' }}>High School Graduate</option>
                                        <option value="Vocational" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                        <option value="College Undergraduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'College Undergraduate' ? 'selected' : '' }}>College Undergraduate</option>
                                        <option value="College Graduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'College Graduate' ? 'selected' : '' }}>College Graduate</option>
                                        <option value="Post Graduate" {{ old('educational_attainment', $inhabitant->educational_attainment) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                                    </select>
                                    @error('educational_attainment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ✅ CONTACT INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">CONTACT INFORMATION</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CONTACT NUMBER</label>
                                    <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" 
                                           value="{{ old('contact_number', $inhabitant->contact_number) }}" placeholder="e.g., 09171234567">
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ✅ EMERGENCY CONTACT -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">EMERGENCY CONTACT</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">EMERGENCY CONTACT NAME</label>
                                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_name', $inhabitant->emergency_contact_name) }}" placeholder="Full Name">
                                    @error('emergency_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">EMERGENCY CONTACT NUMBER</label>
                                    <input type="text" name="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_number', $inhabitant->emergency_contact_number) }}" placeholder="e.g., 09171234567">
                                    @error('emergency_contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">RELATIONSHIP</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                           value="{{ old('emergency_contact_relationship', $inhabitant->emergency_contact_relationship) }}" placeholder="e.g., Spouse, Parent, Sibling">
                                    @error('emergency_contact_relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ✅ RESIDENCY INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-home me-2"></i>RESIDENCY INFORMATION
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RESIDENCY SINCE (Date of Stay) <span class="text-danger">*</span></label>
                                    <input type="date" name="residency_since" class="form-control @error('residency_since') is-invalid @enderror" 
                                           value="{{ old('residency_since', $inhabitant->residency_since ? $inhabitant->residency_since->format('Y-m-d') : '') }}" required>
                                    <small class="form-text text-muted">
                                        @if($inhabitant->residency_since)
                                            Current: {{ $inhabitant->residency_since->format('F d, Y') }} 
                                            ({{ now()->diffInMonths($inhabitant->residency_since) }} months ago)
                                        @else
                                            When did the person start residing in this barangay?
                                        @endif
                                    </small>
                                    @error('residency_since')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RESIDENCY TYPE <span class="text-danger">*</span></label>
                                    <select name="residency_type" class="form-control @error('residency_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="permanent" {{ old('residency_type', $inhabitant->residency_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="temporary" {{ old('residency_type', $inhabitant->residency_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                        <option value="transient" {{ old('residency_type', $inhabitant->residency_type) == 'transient' ? 'selected' : '' }}>Transient</option>
                                    </select>
                                    @error('residency_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ✅ PROOF OF RESIDENCY -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-file-alt me-2"></i>PROOF OF RESIDENCY (Optional)
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CEDULA NUMBER</label>
                                    <input type="text" name="cedula_number" class="form-control @error('cedula_number') is-invalid @enderror" 
                                           value="{{ old('cedula_number', $inhabitant->cedula_number) }}" placeholder="e.g., 12345678">
                                    <small class="form-text text-muted">Community Tax Certificate Number</small>
                                    @error('cedula_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CERTIFICATE OF RESIDENCY NUMBER</label>
                                    <input type="text" name="certificate_of_residency_number" class="form-control @error('certificate_of_residency_number') is-invalid @enderror" 
                                           value="{{ old('certificate_of_residency_number', $inhabitant->certificate_of_residency_number) }}" placeholder="e.g., CR-2024-001">
                                    <small class="form-text text-muted">If previously issued</small>
                                    @error('certificate_of_residency_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

{{-- Proof of Residency File Section --}}
<div class="col-md-12 mb-3">
    <label class="form-label">PROOF OF RESIDENCY DOCUMENT</label>
    
    @if($inhabitant->proof_of_residency_file)
        <div class="alert alert-info mb-2">
            <i class="fas fa-file me-2"></i>Current file: 
            <a href="{{ asset($inhabitant->proof_of_residency_file) }}" target="_blank">
                View Document
            </a>
        </div>
    @endif
    
    <input type="file" name="proof_of_residency_file" class="form-control @error('proof_of_residency_file') is-invalid @enderror" 
           accept=".pdf,.jpg,.jpeg,.png">
    <small class="form-text text-muted">Upload new file to replace existing (PDF, JPG, PNG - Max: 5MB)</small>
    @error('proof_of_residency_file')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                        </div>

                        <!-- HOUSEHOLD INFORMATION -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">HOUSEHOLD INFORMATION</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Household Number</label>
                                    <input type="text" name="household_number" class="form-control @error('household_number') is-invalid @enderror" 
                                           value="{{ old('household_number', $inhabitant->household_number) }}" placeholder="e.g., HH-001">
                                    <small class="form-text text-muted">Used to group family members together</small>
                                    @error('household_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input type="checkbox" name="is_household_head" class="form-check-input" 
                                               id="is_household_head" value="1" {{ old('is_household_head', $inhabitant->is_household_head) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_household_head">
                                            <strong>Is Household Head?</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

{{-- Photo Section --}}
<div class="col-md-6 mb-3">
    @if($inhabitant->photo_path)
        <div class="mb-3">
            <img src="{{ asset($inhabitant->photo_path) }}" 
                 alt="{{ $inhabitant->full_name }}" 
                 class="img-thumbnail" style="max-width: 200px;">
        </div>
    @endif
    
    <label class="form-label">Photo {{ $inhabitant->photo_path ? '(Replace)' : '' }}</label>
    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
    <small class="form-text text-muted">Accepted: JPG, PNG (Max: 2MB)</small>
    @error('photo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        <!-- REMARKS -->
                        <div class="form-section mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">REMARKS</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Remarks/Notes</label>
                                    <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                              rows="3">{{ old('remarks', $inhabitant->remarks) }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Inhabitant
                            </button>
                            <a href="{{ route('barangay.inhabitants.show', $inhabitant) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection