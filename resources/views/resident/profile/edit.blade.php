@extends('layouts.resident')

@section('title', 'Edit Resident Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Edit Resident Profile</h3>
            <p class="text-muted">Update your personal and residential information</p>
        </div>
        <div>
            <a href="{{ route('resident.profile.show') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="alert alert-info d-flex align-items-start mb-4">
        <i class="fas fa-info-circle fs-4 me-3"></i>
        <div>
            <strong>Important Notice:</strong><br>
            Major changes to your profile (such as address, civil status, or occupation) may require re-verification by barangay staff. 
            Please ensure all information is accurate and up-to-date.
        </div>
    </div>

    <form action="{{ route('resident.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-6">
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" value="{{ $residentProfile->user->first_name }}" readonly disabled>
                                <small class="text-muted">Contact admin to change</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" value="{{ $residentProfile->user->last_name }}" readonly disabled>
                                <small class="text-muted">Contact admin to change</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                                <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                                    <option value="">Select Civil Status</option>
                                    <option value="single" {{ old('civil_status', $residentProfile->civil_status) == 'single' ? 'selected' : '' }}>Single</option>
                                    <option value="married" {{ old('civil_status', $residentProfile->civil_status) == 'married' ? 'selected' : '' }}>Married</option>
                                    <option value="widowed" {{ old('civil_status', $residentProfile->civil_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="separated" {{ old('civil_status', $residentProfile->civil_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="divorced" {{ old('civil_status', $residentProfile->civil_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                                @error('civil_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nationality <span class="text-danger">*</span></label>
                                <input type="text" name="nationality" class="form-control @error('nationality') is-invalid @enderror" 
                                       value="{{ old('nationality', $residentProfile->nationality) }}" required>
                                @error('nationality')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Religion</label>
                                <input type="text" name="religion" class="form-control @error('religion') is-invalid @enderror" 
                                       value="{{ old('religion', $residentProfile->religion) }}">
                                @error('religion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Educational Attainment <span class="text-danger">*</span></label>
                                <select name="educational_attainment" class="form-select @error('educational_attainment') is-invalid @enderror" required>
                                    <option value="">Select Education Level</option>
                                    <option value="No formal education" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'No formal education' ? 'selected' : '' }}>No formal education</option>
                                    <option value="Elementary Level" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'Elementary Level' ? 'selected' : '' }}>Elementary Level</option>
                                    <option value="Elementary Graduate" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'Elementary Graduate' ? 'selected' : '' }}>Elementary Graduate</option>
                                    <option value="High School Level" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'High School Level' ? 'selected' : '' }}>High School Level</option>
                                    <option value="High School Graduate" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'High School Graduate' ? 'selected' : '' }}>High School Graduate</option>
                                    <option value="Vocational" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                    <option value="College Level" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'College Level' ? 'selected' : '' }}>College Level</option>
                                    <option value="College Graduate" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'College Graduate' ? 'selected' : '' }}>College Graduate</option>
                                    <option value="Post Graduate" {{ old('educational_attainment', $residentProfile->educational_attainment) == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                                </select>
                                @error('educational_attainment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

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
                                <label class="form-label">Monthly Income</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="monthly_income" class="form-control @error('monthly_income') is-invalid @enderror" 
                                           value="{{ old('monthly_income', $residentProfile->monthly_income) }}" 
                                           min="0" step="0.01" placeholder="0.00">
                                </div>
                                <small class="text-muted">Optional. For statistical purposes only.</small>
                                @error('monthly_income')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Residential Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-home me-2"></i>Residential Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Barangay</label>
                            <input type="text" class="form-control" value="{{ $residentProfile->barangay->name }}" readonly disabled>
                            <small class="text-muted">Contact admin to transfer barangay</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Purok/Zone <span class="text-danger">*</span></label>
                            <input type="text" name="purok_zone" class="form-control @error('purok_zone') is-invalid @enderror" 
                                   value="{{ old('purok_zone', $residentProfile->purok_zone) }}" 
                                   placeholder="e.g., Purok 1, Zone 3" required>
                            @error('purok_zone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Residency Type</label>
                                <input type="text" class="form-control" value="{{ ucfirst($residentProfile->residency_type) }}" readonly disabled>
                                <small class="text-muted">Contact barangay to change</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Resident Since</label>
                                <input type="text" class="form-control" value="{{ $residentProfile->residency_since->format('F d, Y') }}" readonly disabled>
                                <small class="text-muted">Contact barangay to change</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Registered Voter</label>
                                <input type="text" class="form-control" value="{{ $residentProfile->is_registered_voter ? 'Yes' : 'No' }}" readonly disabled>
                                <small class="text-muted">Contact barangay to update</small>
                            </div>

                            @if($residentProfile->precinct_number)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Precinct #</label>
                                <input type="text" class="form-control" value="{{ $residentProfile->precinct_number }}" readonly disabled>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-6">
                <!-- Emergency Contact -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_name', $residentProfile->emergency_contact_name) }}" 
                                   placeholder="Full name of emergency contact" required>
                            @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact_number" class="form-control @error('emergency_contact_number') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_number', $residentProfile->emergency_contact_number) }}" 
                                   placeholder="09XXXXXXXXX" required>
                            @error('emergency_contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact_relationship" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                   value="{{ old('emergency_contact_relationship', $residentProfile->emergency_contact_relationship) }}" 
                                   placeholder="e.g., Spouse, Parent, Sibling" required>
                            @error('emergency_contact_relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Special Classifications -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Special Classifications</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Select if any of the following classifications apply to you:</p>

                        <!-- PWD -->
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_pwd" value="1" 
                                           id="is_pwd" {{ old('is_pwd', $residentProfile->is_pwd) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_pwd">
                                        <i class="fas fa-wheelchair text-primary me-1"></i>Person with Disability (PWD)
                                    </label>
                                </div>
                                <div id="pwdFields" style="display: {{ old('is_pwd', $residentProfile->is_pwd) ? 'block' : 'none' }};">
                                    <label class="form-label small">PWD ID Number</label>
                                    <input type="text" name="pwd_id_number" class="form-control form-control-sm @error('pwd_id_number') is-invalid @enderror" 
                                           value="{{ old('pwd_id_number', $residentProfile->pwd_id_number) }}" 
                                           placeholder="Enter PWD ID number">
                                    @error('pwd_id_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Senior Citizen -->
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1" 
                                           id="is_senior_citizen" {{ old('is_senior_citizen', $residentProfile->is_senior_citizen) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_senior_citizen">
                                        <i class="fas fa-user text-success me-1"></i>Senior Citizen (60 years old and above)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Solo Parent -->
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_solo_parent" value="1" 
                                           id="is_solo_parent" {{ old('is_solo_parent', $residentProfile->is_solo_parent) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_solo_parent">
                                        <i class="fas fa-child text-warning me-1"></i>Solo Parent
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 4Ps Beneficiary -->
                        <div class="card border">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_4ps_beneficiary" value="1" 
                                           id="is_4ps_beneficiary" {{ old('is_4ps_beneficiary', $residentProfile->is_4ps_beneficiary) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_4ps_beneficiary">
                                        <i class="fas fa-hand-holding-heart text-danger me-1"></i>4Ps Beneficiary (Pantawid Pamilyang Pilipino Program)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('resident.profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>

                        <hr class="my-3">

                        <div class="alert alert-warning mb-0">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Note:</strong> Changes to major profile fields may require re-verification by barangay staff.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle PWD ID field
    const pwdCheckbox = document.getElementById('is_pwd');
    const pwdFields = document.getElementById('pwdFields');
    
    if (pwdCheckbox) {
        pwdCheckbox.addEventListener('change', function() {
            pwdFields.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const confirmUpdate = confirm('Are you sure you want to update your profile? Major changes may require re-verification.');
        if (!confirmUpdate) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    border-radius: 10px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card.border {
    transition: all 0.3s ease;
}

.card.border:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

input[readonly], input[disabled] {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>
@endpush
@endsection