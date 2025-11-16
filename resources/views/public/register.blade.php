{{-- FILE: resources/views/public/register.blade.php (COMPLETE FORM) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ $barangay->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('public.barangay', $barangay->slug) }}">
            @if($siteSettings && $siteSettings->municipality_logo)
                <img src="{{ asset($siteSettings->municipality_logo) }}" alt="Logo" height="30" class="me-2">
            @endif
                {{ $barangay->name }}
            </a>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Resident Registration - {{ $barangay->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('public.barangay.register.submit', $barangay->slug) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Account Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Account Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       name="email" value="{{ old('email') }}" required>
                                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       name="password" required>
                                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" class="form-control" name="password_confirmation" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                       name="phone" value="{{ old('phone') }}" required>
                                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                                       name="first_name" value="{{ old('first_name') }}" required>
                                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                                       name="middle_name" value="{{ old('middle_name') }}">
                                                @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                                       name="last_name" value="{{ old('last_name') }}" required>
                                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Suffix</label>
                                                <select class="form-control @error('suffix') is-invalid @enderror" name="suffix">
                                                    <option value="">None</option>
                                                    <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                                    <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                                    <option value="II" {{ old('suffix') == 'II' ? 'selected' : '' }}>II</option>
                                                    <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                                                    <option value="IV" {{ old('suffix') == 'IV' ? 'selected' : '' }}>IV</option>
                                                </select>
                                                @error('suffix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                       name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Place of Birth <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror" 
                                                       name="place_of_birth" value="{{ old('place_of_birth') }}" required>
                                                @error('place_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                                <select class="form-control @error('gender') is-invalid @enderror" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                                </select>
                                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Civil Status <span class="text-danger">*</span></label>
                                                <select class="form-control @error('civil_status') is-invalid @enderror" name="civil_status" required>
                                                    <option value="">Select Status</option>
                                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                                    <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                                                </select>
                                                @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Nationality</label>
                                                <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                                       name="nationality" value="{{ old('nationality', 'Filipino') }}">
                                                @error('nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Religion</label>
                                                <input type="text" class="form-control @error('religion') is-invalid @enderror" 
                                                       name="religion" value="{{ old('religion') }}">
                                                @error('religion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Educational Attainment</label>
                                                <select class="form-control @error('educational_attainment') is-invalid @enderror" name="educational_attainment">
                                                    <option value="">Select Level</option>
                                                    <option value="Elementary" {{ old('educational_attainment') == 'Elementary' ? 'selected' : '' }}>Elementary</option>
                                                    <option value="High School" {{ old('educational_attainment') == 'High School' ? 'selected' : '' }}>High School</option>
                                                    <option value="Vocational" {{ old('educational_attainment') == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                                    <option value="College" {{ old('educational_attainment') == 'College' ? 'selected' : '' }}>College</option>
                                                    <option value="Graduate Studies" {{ old('educational_attainment') == 'Graduate Studies' ? 'selected' : '' }}>Graduate Studies</option>
                                                </select>
                                                @error('educational_attainment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Occupation</label>
                                                <input type="text" class="form-control @error('occupation') is-invalid @enderror" 
                                                       name="occupation" value="{{ old('occupation') }}">
                                                @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Monthly Income (₱)</label>
                                                <input type="number" step="0.01" class="form-control @error('monthly_income') is-invalid @enderror" 
                                                       name="monthly_income" value="{{ old('monthly_income') }}">
                                                @error('monthly_income')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">House Number</label>
                                                <input type="text" class="form-control @error('house_number') is-invalid @enderror" 
                                                       name="house_number" value="{{ old('house_number') }}">
                                                @error('house_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Street</label>
                                                <input type="text" class="form-control @error('street') is-invalid @enderror" 
                                                       name="street" value="{{ old('street') }}">
                                                @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Purok/Zone</label>
                                                <input type="text" class="form-control @error('purok_zone') is-invalid @enderror" 
                                                       name="purok_zone" value="{{ old('purok_zone') }}">
                                                @error('purok_zone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle me-1"></i>
                                        Your address will be in: <strong>{{ $barangay->name }}, {{ $siteSettings->municipality_name }}</strong></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Documents & Identification</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">ID Type</label>
                                                <select class="form-control @error('id_type') is-invalid @enderror" name="id_type">
                                                    <option value="">Select ID Type</option>
                                                    <option value="Driver's License" {{ old('id_type') == "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                                                    <option value="SSS ID" {{ old('id_type') == 'SSS ID' ? 'selected' : '' }}>SSS ID</option>
                                                    <option value="UMID" {{ old('id_type') == 'UMID' ? 'selected' : '' }}>UMID</option>
                                                    <option value="PhilHealth ID" {{ old('id_type') == 'PhilHealth ID' ? 'selected' : '' }}>PhilHealth ID</option>
                                                    <option value="Postal ID" {{ old('id_type') == 'Postal ID' ? 'selected' : '' }}>Postal ID</option>
                                                    <option value="Voter's ID" {{ old('id_type') == "Voter's ID" ? 'selected' : '' }}>Voter's ID</option>
                                                    <option value="Senior Citizen ID" {{ old('id_type') == 'Senior Citizen ID' ? 'selected' : '' }}>Senior Citizen ID</option>
                                                    <option value="PWD ID" {{ old('id_type') == 'PWD ID' ? 'selected' : '' }}>PWD ID</option>
                                                    <option value="Other" {{ old('id_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">ID Number</label>
                                                <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                                       name="id_number" value="{{ old('id_number') }}">
                                                @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Valid ID (Photo/Scan)</label>
                                                <input type="file" class="form-control @error('id_file') is-invalid @enderror" 
                                                       name="id_file" accept="image/*,.pdf">
                                                <small class="form-text text-muted">Max 2MB. JPG, PNG, or PDF</small>
                                                @error('id_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Proof of Residency</label>
                                                <input type="file" class="form-control @error('proof_of_residency') is-invalid @enderror" 
                                                       name="proof_of_residency" accept="image/*,.pdf">
                                                <small class="form-text text-muted">Utility bill, etc. Max 2MB</small>
                                                @error('proof_of_residency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Profile Photo</label>
                                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                                       name="profile_photo" accept="image/*">
                                                <small class="form-text text-muted">Max 1MB. JPG, PNG</small>
                                                @error('profile_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Emergency Contact</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Contact Name</label>
                                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                                       name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                                                @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Relationship</label>
                                                <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                                       name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" 
                                                       placeholder="e.g. Spouse, Parent, Sibling">
                                                @error('emergency_contact_relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                                       name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
                                                @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> 
                                            and consent to the collection and processing of my personal data.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('public.barangay', $barangay->slug) }}" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Submit Registration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Data Privacy Notice</h6>
                    <p>By registering, you consent to the collection, processing, and storage of your personal information by {{ $barangay->name }} for the following purposes:</p>
                    <ul>
                        <li>Resident identification and verification</li>
                        <li>Provision of barangay services</li>
                        <li>Processing of document requests</li>
                        <li>Emergency contact purposes</li>
                        <li>Statistical reporting (anonymized)</li>
                    </ul>
                    
                    <h6>Registration Requirements</h6>
                    <ul>
                        <li>You must be a bona fide resident of {{ $barangay->name }}</li>
                        <li>All information provided must be accurate and truthful</li>
                        <li>Documents submitted will be verified by barangay officials</li>
                        <li>Your account will be activated upon verification</li>
                    </ul>

                    <p><strong>For questions about data privacy, contact:</strong> {{ $siteSettings->contact_email }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize date picker
        flatpickr('[name="date_of_birth"]', {
            maxDate: 'today',
            yearRange: [1920, new Date().getFullYear()]
        });
    </script>
</body>
</html>

