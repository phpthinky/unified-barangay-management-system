{{-- FILE: resources/views/public/barangay/register.blade.php - FIXED REQUIRED FIELDS --}}
@extends('layouts.public')

@section('title', 'Register - ' . $barangay->name)

@section('content')
<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold mb-3">
                    <i class="fas fa-user-plus me-3"></i>Resident Registration
                </h1>
                <p class="lead mb-0">Register as a resident of <strong>{{ $barangay->name }}</strong></p>
            </div>
            <div class="col-lg-4 text-end">
                @if($barangay->logo_url)
                    <img src="{{ $barangay->logo_url }}" alt="{{ $barangay->name }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid white;">
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Registration Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.barangay.register.submit', $barangay->slug) }}" id="registrationForm">
                            @csrf

                            <!-- STEP 0: RBI QUESTION -->
                            <div id="step_question">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Registry of Barangay Inhabitants (RBI)</strong> is required for requesting barangay documents.
                                </div>
                                
                                <h5 class="mb-4">Are you registered in {{ $barangay->name }}'s RBI?</h5>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="has_rbi" id="rbi_yes" value="yes">
                                    <label class="form-check-label fw-bold" for="rbi_yes">
                                        <i class="fas fa-check-circle text-success me-2"></i>YES - I am registered in the RBI
                                    </label>
                                    <small class="d-block text-muted ms-4">We will verify your record automatically</small>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="radio" name="has_rbi" id="rbi_no" value="no">
                                    <label class="form-check-label fw-bold" for="rbi_no">
                                        <i class="fas fa-times-circle text-warning me-2"></i>NO - I am not registered yet
                                    </label>
                                    <small class="d-block text-muted ms-4">Visit the barangay office for RBI registration</small>
                                </div>

                                <button type="button" class="btn btn-primary" onclick="continueFromQuestion()">
                                    Continue <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>

                            <!-- STEP 1: BASIC INFO (IF YES) - These fields will be enabled when visible -->
                            <div id="step_basic" style="display: none;">
                                <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="goBack('step_question', 'step_basic')">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>

                                <h4 class="text-primary mb-3"><i class="fas fa-search me-2"></i>RBI Verification</h4>
                                <p class="text-muted mb-4">Enter your details exactly as registered:</p>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" id="basic_first_name" value="{{ old('first_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" id="basic_middle_name" value="{{ old('middle_name') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" id="basic_last_name" value="{{ old('last_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Extension (Jr., Sr., etc.)</label>
                                        <input type="text" class="form-control" name="suffix" id="basic_suffix" value="{{ old('suffix') }}" placeholder="Optional">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="birth_date" id="basic_birth_date" value="{{ old('birth_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="basic_email" value="{{ old('email') }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" name="action" value="check_rbi">
                                    <i class="fas fa-search me-2"></i>Check RBI Registry
                                </button>
                            </div>

                            <!-- STEP 2: PASSWORD (IF RBI FOUND) -->
                            <div id="step_password" style="display: {{ session('rbi_found') ? 'block' : 'none' }};">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>RBI Record Found!</strong> Your information matches our registry.
                                </div>

                                <h4 class="text-primary mb-4"><i class="fas fa-lock me-2"></i>Create Your Password</h4>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="rbi_password" id="rbi_password">
                                        <small class="text-muted">Minimum 8 characters</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="rbi_password_confirmation" id="rbi_password_confirmation">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg" name="action" value="complete_rbi_registration">
                                    <i class="fas fa-check me-2"></i>Complete Registration
                                </button>
                            </div>

                            <!-- STEP 3: FULL FORM (IF NO RBI) -->
                            <div id="step_full" style="display: {{ session('rbi_not_found') || old('has_rbi') == 'no' ? 'block' : 'none' }};">
                                <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="goBack('step_question', 'step_full')">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </button>

                                @if(session('rbi_not_found'))
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>RBI Record Not Found</h6>
                                        <p class="mb-0">Continue registration? You'll need to visit the barangay office for RBI verification before requesting documents.</p>
                                    </div>
                                @endif

                                <!-- Personal Information -->
                                <h4 class="text-primary mb-3">Personal Information</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_first_name" id="full_first_name" value="{{ old('first_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_last_name" id="full_last_name" value="{{ old('last_name') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="full_middle_name" id="full_middle_name" value="{{ old('middle_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Extension</label>
                                        <input type="text" class="form-control" name="full_suffix" id="full_suffix" value="{{ old('suffix') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Birth Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="full_birth_date" id="full_birth_date" value="{{ old('birth_date') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Sex <span class="text-danger">*</span></label>
                                        <select class="form-select" name="full_gender" id="full_gender">
                                            <option value="">Select</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Contact -->
                                <h4 class="text-primary mb-3 mt-4">Contact Information</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="full_email" id="full_email" value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Contact Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="full_contact_number" id="full_contact_number" value="{{ old('contact_number') }}">
                                    </div>
                                </div>

                                <!-- Password -->
                                <h4 class="text-primary mb-3 mt-4">Account Security</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="full_password" id="full_password">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="full_password_confirmation" id="full_password_confirmation">
                                    </div>
                                </div>

                                <!-- Address -->
                                <h4 class="text-primary mb-3 mt-4">Address</h4>
                                <div class="mb-3">
                                    <label>Street Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="full_address" id="full_address" rows="2">{{ old('address') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Purok/Zone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_purok_zone" id="full_purok_zone" value="{{ old('purok_zone') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Resident Since <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="full_residency_since" id="full_residency_since" value="{{ old('residency_since') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Residency Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="full_residency_type" id="full_residency_type">
                                            <option value="">Select</option>
                                            <option value="permanent">Permanent</option>
                                            <option value="temporary">Temporary</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Civil Status <span class="text-danger">*</span></label>
                                        <select class="form-select" name="full_civil_status" id="full_civil_status">
                                            <option value="">Select</option>
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="widowed">Widowed</option>
                                            <option value="separated">Separated</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Other Details -->
                                <h4 class="text-primary mb-3 mt-4">Other Details</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Nationality <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_nationality" id="full_nationality" value="{{ old('nationality', 'Filipino') }}">
                                    </div>
                                      <div class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Occupation</label>
        <select class="form-control @error('occupation') is-invalid @enderror" 
                name="occupation" id="occupation" required>
            <option value="">-- Select Occupation --</option>
            <option value="Student" {{ old('occupation') == 'Student' ? 'selected' : '' }}>Student</option>
            <option value="Teacher" {{ old('occupation') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="Government Employee" {{ old('occupation') == 'Government Employee' ? 'selected' : '' }}>Government Employee</option>
            <option value="Private Employee" {{ old('occupation') == 'Private Employee' ? 'selected' : '' }}>Private Employee</option>
            <option value="Self-Employed" {{ old('occupation') == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
            <option value="Business Owner" {{ old('occupation') == 'Business Owner' ? 'selected' : '' }}>Business Owner</option>
            <option value="Driver" {{ old('occupation') == 'Driver' ? 'selected' : '' }}>Driver</option>
            <option value="Vendor" {{ old('occupation') == 'Vendor' ? 'selected' : '' }}>Vendor</option>
            <option value="Farmer" {{ old('occupation') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
            <option value="Fisherman" {{ old('occupation') == 'Fisherman' ? 'selected' : '' }}>Fisherman</option>
            <option value="Construction Worker" {{ old('occupation') == 'Construction Worker' ? 'selected' : '' }}>Construction Worker</option>
            <option value="Security Guard" {{ old('occupation') == 'Security Guard' ? 'selected' : '' }}>Security Guard</option>
            <option value="Housewife/Househusband" {{ old('occupation') == 'Housewife/Househusband' ? 'selected' : '' }}>Housewife/Househusband</option>
            <option value="Retired" {{ old('occupation') == 'Retired' ? 'selected' : '' }}>Retired</option>
            <option value="Unemployed" {{ old('occupation') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
            <option value="OFW (Overseas Filipino Worker)" {{ old('occupation') == 'OFW (Overseas Filipino Worker)' ? 'selected' : '' }}>OFW (Overseas Filipino Worker)</option>
            <option value="Healthcare Worker" {{ old('occupation') == 'Healthcare Worker' ? 'selected' : '' }}>Healthcare Worker</option>
            <option value="Engineer" {{ old('occupation') == 'Engineer' ? 'selected' : '' }}>Engineer</option>
            <option value="Accountant" {{ old('occupation') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
            <option value="Sales Representative" {{ old('occupation') == 'Sales Representative' ? 'selected' : '' }}>Sales Representative</option>
            <option value="Mechanic" {{ old('occupation') == 'Mechanic' ? 'selected' : '' }}>Mechanic</option>
            <option value="Electrician" {{ old('occupation') == 'Electrician' ? 'selected' : '' }}>Electrician</option>
            <option value="Plumber" {{ old('occupation') == 'Plumber' ? 'selected' : '' }}>Plumber</option>
            <option value="Carpenter" {{ old('occupation') == 'Carpenter' ? 'selected' : '' }}>Carpenter</option>
            <option value="Cook/Chef" {{ old('occupation') == 'Cook/Chef' ? 'selected' : '' }}>Cook/Chef</option>
            <option value="Domestic Helper" {{ old('occupation') == 'Domestic Helper' ? 'selected' : '' }}>Domestic Helper</option>
            <option value="Others" {{ old('occupation') == 'Others' ? 'selected' : '' }}>Others</option>
        </select>
        @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
                                </div>

                                <div class="mb-3">
                                    <label>Educational Attainment <span class="text-danger">*</span></label>
                                    <select class="form-select" name="full_educational_attainment" id="full_educational_attainment">
                                        <option value="">Select</option>
                                        <option value="Elementary Graduate">Elementary Graduate</option>
                                        <option value="High School Graduate">High School Graduate</option>
                                        <option value="College Graduate">College Graduate</option>
                                    </select>
                                </div>

                                <!-- Emergency Contact -->
                                <h4 class="text-primary mb-3 mt-4">Emergency Contact</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_emergency_contact_name" id="full_emergency_contact_name">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="full_emergency_contact_number" id="full_emergency_contact_number">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label>Relationship <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_emergency_contact_relationship" id="full_emergency_contact_relationship">
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="full_agree_terms">
                                    <label class="form-check-label" for="full_agree_terms">
                                        I agree that all information provided is correct <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg" name="action" value="register_full">
                                    <i class="fas fa-user-plus me-2"></i>Complete Registration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Enable/disable required based on visible step
function enableFields(stepId) {
    // Disable ALL fields first
    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.removeAttribute('required');
    });
    
    // Enable only visible step fields
    if (stepId === 'step_basic') {
        document.getElementById('basic_first_name').setAttribute('required', 'required');
        document.getElementById('basic_last_name').setAttribute('required', 'required');
        document.getElementById('basic_birth_date').setAttribute('required', 'required');
        document.getElementById('basic_email').setAttribute('required', 'required');
    } else if (stepId === 'step_password') {
        document.getElementById('rbi_password').setAttribute('required', 'required');
        document.getElementById('rbi_password_confirmation').setAttribute('required', 'required');
    } else if (stepId === 'step_full') {
        document.getElementById('full_first_name').setAttribute('required', 'required');
        document.getElementById('full_last_name').setAttribute('required', 'required');
        document.getElementById('full_birth_date').setAttribute('required', 'required');
        document.getElementById('full_gender').setAttribute('required', 'required');
        document.getElementById('full_email').setAttribute('required', 'required');
        document.getElementById('full_contact_number').setAttribute('required', 'required');
        document.getElementById('full_password').setAttribute('required', 'required');
        document.getElementById('full_password_confirmation').setAttribute('required', 'required');
        document.getElementById('full_address').setAttribute('required', 'required');
        document.getElementById('full_purok_zone').setAttribute('required', 'required');
        document.getElementById('full_residency_since').setAttribute('required', 'required');
        document.getElementById('full_residency_type').setAttribute('required', 'required');
        document.getElementById('full_civil_status').setAttribute('required', 'required');
        document.getElementById('full_nationality').setAttribute('required', 'required');
        document.getElementById('full_occupation').setAttribute('required', 'required');
        document.getElementById('full_educational_attainment').setAttribute('required', 'required');
        document.getElementById('full_emergency_contact_name').setAttribute('required', 'required');
        document.getElementById('full_emergency_contact_number').setAttribute('required', 'required');
        document.getElementById('full_emergency_contact_relationship').setAttribute('required', 'required');
        document.getElementById('full_agree_terms').setAttribute('required', 'required');
    }
}

function showStep(stepId) {
    document.getElementById('step_question').style.display = 'none';
    document.getElementById('step_basic').style.display = 'none';
    document.getElementById('step_full').style.display = 'none';
    document.getElementById(stepId).style.display = 'block';
    enableFields(stepId);
}

function continueFromQuestion() {
    var rbiYes = document.getElementById('rbi_yes');
    var rbiNo = document.getElementById('rbi_no');
    
    if (!rbiYes.checked && !rbiNo.checked) {
        alert('Please select YES or NO');
        return;
    }
    
    if (rbiYes.checked) {
        showStep('step_basic');
    } else {
        showStep('step_full');
    }
}

function goBack(showId, hideId) {
    document.getElementById(hideId).style.display = 'none';
    document.getElementById(showId).style.display = 'block';
    enableFields('');
}
</script>
@endsection