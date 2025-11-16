{{-- FILE: resources/views/public/barangay/register/full-form.blade.php --}}
@extends('layouts.public')

@section('title', 'Complete Registration - ' . $barangay->name)

@section('content')
<section class="bg-primary text-white py-5">
    <div class="container">
        <h1 class="display-6 fw-bold">
            <i class="fas fa-user-plus me-3"></i>Complete Registration
        </h1>
        <p class="lead mb-0">Register for {{ $barangay->name }}</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <a href="{{ route('public.barangay.register', $barangay->slug) }}" class="btn btn-sm btn-secondary mb-4">
                            <i class="fas fa-arrow-left me-2"></i>Start Over
                        </a>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Registration Information</h6>
                            <p class="mb-0">Fill out all required fields. If you are already in our RBI registry, we will automatically link your account.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.barangay.register.complete-full', $barangay->slug) }}">
                            @csrf

                            {{-- NAME (1) --}}
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>NAME
                            </h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                           name="middle_name" value="{{ old('middle_name') }}">
                                    @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Extension</label>
                                    <select class="form-select @error('suffix') is-invalid @enderror" name="suffix">
                                        <option value="">None</option>
                                        <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                        <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                        <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                                        <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                                        <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                                        <option value="V" {{ old('suffix') == 'V' ? 'selected' : '' }}>V</option>
                                    </select>
                                    @error('suffix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- ADDRESS (2) --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-map-marker-alt me-2"></i>ADDRESS
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>House/Block/Lot Number</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           name="address" value="{{ old('address') }}" placeholder="e.g., House No. 123">
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label>Zone/Sitio/Purok <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('purok_zone') is-invalid @enderror" 
                                           name="purok_zone" value="{{ old('purok_zone') }}" placeholder="e.g., Purok 1, Zone A" required>
                                    @error('purok_zone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- BIRTH INFORMATION (3-4) --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-birthday-cake me-2"></i>BIRTH INFORMATION
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Place of Birth <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                           name="place_of_birth" value="{{ old('place_of_birth') }}" 
                                           placeholder="City/Municipality, Province" required>
                                    @error('place_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                           name="birth_date" value="{{ old('birth_date') }}" required>
                                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- SEX, CIVIL STATUS, CITIZENSHIP (5-7) --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-id-card me-2"></i>PERSONAL DETAILS
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Sex <span class="text-danger">*</span></label>
                                    <select class="form-select @error('gender') is-invalid @enderror" name="gender" required>
                                        <option value="">Select</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Civil Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('civil_status') is-invalid @enderror" name="civil_status" required>
                                        <option value="">Select</option>
                                        <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                    @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Citizenship <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                           name="nationality" value="{{ old('nationality', 'Filipino') }}" required>
                                    @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- OCCUPATION & EDUCATION --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-briefcase me-2"></i>OCCUPATION & EDUCATION
                            </h5>
                            <div class="row">
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
                                <div class="col-md-6 mb-3">
                                    <label>Educational Attainment <span class="text-danger">*</span></label>
                                    <select class="form-select @error('educational_attainment') is-invalid @enderror" 
                                            name="educational_attainment" required>
                                        <option value="">Select</option>
                                        <option value="No Formal Education">No Formal Education</option>
                                        <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                                        <option value="Elementary Graduate">Elementary Graduate</option>
                                        <option value="High School Undergraduate">High School Undergraduate</option>
                                        <option value="High School Graduate">High School Graduate</option>
                                        <option value="Vocational">Vocational</option>
                                        <option value="College Undergraduate">College Undergraduate</option>
                                        <option value="College Graduate">College Graduate</option>
                                        <option value="Post Graduate">Post Graduate</option>
                                    </select>
                                    @error('educational_attainment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- CONTACT INFORMATION --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-phone me-2"></i>CONTACT INFORMATION
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('contact_number') is-invalid @enderror" 
                                           name="contact_number" value="{{ old('contact_number') }}" 
                                           placeholder="e.g., 09171234567" required>
                                    @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- EMERGENCY CONTACT --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-phone-square me-2"></i>EMERGENCY CONTACT
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                           name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                                    @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('emergency_contact_number') is-invalid @enderror" 
                                           name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" required>
                                    @error('emergency_contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Relationship <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                           name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" 
                                           placeholder="e.g., Spouse, Parent" required>
                                    @error('emergency_contact_relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- RESIDENCY INFORMATION --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-home me-2"></i>RESIDENCY INFORMATION
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Resident Since (Date of Stay) <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('residency_since') is-invalid @enderror" 
                                           name="residency_since" value="{{ old('residency_since') }}" required>
                                    <small class="text-muted">When did you start living in this barangay?</small>
                                    @error('residency_since')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Residency Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('residency_type') is-invalid @enderror" 
                                            name="residency_type" required>
                                        <option value="">Select Type</option>
                                        <option value="permanent" {{ old('residency_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="temporary" {{ old('residency_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                        <option value="transient" {{ old('residency_type') == 'transient' ? 'selected' : '' }}>Transient</option>
                                    </select>
                                    @error('residency_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- ACCOUNT SECURITY --}}
                            <h5 class="text-primary mb-3 mt-4">
                                <i class="fas fa-lock me-2"></i>ACCOUNT SECURITY
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required>
                                    <small class="text-muted">Minimum 8 characters</small>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            {{-- TERMS --}}
                            <div class="form-check mb-4 mt-4">
                                <input class="form-check-input" type="checkbox" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    I certify that all information provided is true and correct <span class="text-danger">*</span>
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
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