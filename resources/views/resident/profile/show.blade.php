@extends('layouts.resident')

@section('title', 'My Resident Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>My Resident Profile</h3>
            <p class="text-muted">{{ $residentProfile->barangay->name }}</p>
        </div>
        <div>
            <a href="{{ route('resident.profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Profile
            </a>
        </div>
    </div>

    <!-- Verification Status Alert -->
    @if(!$residentProfile->is_verified)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
        <div class="flex-grow-1">
            <strong>Profile Pending Verification</strong><br>
            Your profile is currently under review by barangay staff. Some services may be limited until verification is complete.
        </div>
    </div>
    @else
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle fs-4 me-3"></i>
        <div class="flex-grow-1">
            <strong>Profile Verified</strong><br>
            Your profile was verified on {{ $residentProfile->verified_at->format('F d, Y') }}
            @if($residentProfile->verifier)
                by {{ $residentProfile->verifier->name }}
            @endif
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    @if($residentProfile->user->profile_photo)
                    <img src="{{ asset('uploads/photos/' . $residentProfile->user->profile_photo) }}" 
                         alt="Profile Photo" class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        {{ strtoupper(substr($residentProfile->user->first_name, 0, 1)) }}{{ strtoupper(substr($residentProfile->user->last_name, 0, 1)) }}
                    </div>
                    @endif
                    
                    <h4>{{ $residentProfile->user->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $residentProfile->user->email }}</p>
                    
                    <div class="mb-3">
                        @if($residentProfile->is_verified)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Verified Resident
                        </span>
                        @else
                        <span class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>Pending Verification
                        </span>
                        @endif
                    </div>

                    <!-- Profile Completion -->
                    <div class="mb-3">
                        <small class="text-muted">Profile Completion</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $residentProfile->completion_percentage >= 80 ? 'success' : ($residentProfile->completion_percentage >= 50 ? 'warning' : 'danger') }}" 
                                 role="progressbar" 
                                 style="width: {{ $residentProfile->completion_percentage }}%">
                                {{ $residentProfile->completion_percentage }}%
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                            data-bs-toggle="modal" data-bs-target="#uploadIdModal">
                        <i class="fas fa-upload me-2"></i>Upload ID Document
                    </button>
                </div>
            </div>

            <!-- Special Classifications -->
            @if($residentProfile->is_pwd || $residentProfile->is_senior_citizen || $residentProfile->is_solo_parent || $residentProfile->is_4ps_beneficiary)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Special Classifications</h5>
                </div>
                <div class="card-body">
                    @if($residentProfile->is_pwd)
                    <div class="mb-2">
                        <span class="badge bg-primary">
                            <i class="fas fa-wheelchair me-1"></i>Person with Disability (PWD)
                        </span>
                        @if($residentProfile->pwd_id_number)
                        <br><small class="text-muted">ID: {{ $residentProfile->pwd_id_number }}</small>
                        @endif
                    </div>
                    @endif

                    @if($residentProfile->is_senior_citizen)
                    <div class="mb-2">
                        <span class="badge bg-success">
                            <i class="fas fa-user me-1"></i>Senior Citizen
                        </span>
                    </div>
                    @endif

                    @if($residentProfile->is_solo_parent)
                    <div class="mb-2">
                        <span class="badge bg-warning">
                            <i class="fas fa-child me-1"></i>Solo Parent
                        </span>
                    </div>
                    @endif

                    @if($residentProfile->is_4ps_beneficiary)
                    <div class="mb-0">
                        <span class="badge bg-danger">
                            <i class="fas fa-hand-holding-heart me-1"></i>4Ps Beneficiary
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Links -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-link me-2"></i>Quick Links</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('resident.documents.index') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="fas fa-file-alt me-2"></i>My Document Requests
                    </a>
                    <a href="{{ route('resident.complaints.index') }}" class="btn btn-outline-warning btn-sm w-100 mb-2">
                        <i class="fas fa-comments me-2"></i>My Complaints
                    </a>
                    
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Full Name</small>
                            <p class="mb-0 fw-bold">{{ $residentProfile->user->full_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Gender</small>
                            <p class="mb-0">{{ ucfirst($residentProfile->user->gender ?? 'Not specified') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Birth Date</small>
                            <p class="mb-0">
                                @if($residentProfile->user->birth_date)
                                    {{ $residentProfile->user->birth_date->format('F d, Y') }}
                                    <small class="text-muted">({{ $residentProfile->user->age }} years old)</small>
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Civil Status</small>
                            <p class="mb-0">{{ ucfirst($residentProfile->civil_status) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Nationality</small>
                            <p class="mb-0">{{ $residentProfile->nationality }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Religion</small>
                            <p class="mb-0">{{ $residentProfile->religion ?? 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Occupation</small>
                            <p class="mb-0">{{ $residentProfile->occupation }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Monthly Income</small>
                            <p class="mb-0">
                                @if($residentProfile->monthly_income)
                                    ₱{{ number_format($residentProfile->monthly_income, 2) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Educational Attainment</small>
                            <p class="mb-0">{{ $residentProfile->educational_attainment }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $residentProfile->user->contact_number ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Residential Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Residential Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Barangay</small>
                            <p class="mb-0 fw-bold">{{ $residentProfile->barangay->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Purok/Zone</small>
                            <p class="mb-0">{{ $residentProfile->purok_zone }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <small class="text-muted">Address</small>
                            <p class="mb-0">{{ $residentProfile->user->address ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Residency Type</small>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ ucfirst($residentProfile->residency_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Resident Since</small>
                            <p class="mb-0">{{ $residentProfile->residency_since->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Registered Voter</small>
                            <p class="mb-0">
                                @if($residentProfile->is_registered_voter)
                                    <span class="badge bg-success">Yes</span>
                                    @if($residentProfile->precinct_number)
                                        <br><small class="text-muted">Precinct: {{ $residentProfile->precinct_number }}</small>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Name</small>
                            <p class="mb-0 fw-bold">{{ $residentProfile->emergency_contact_name }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $residentProfile->emergency_contact_number }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Relationship</small>
                            <p class="mb-0">{{ $residentProfile->emergency_contact_relationship }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ID Documents -->
            @if($residentProfile->uploaded_files && count($residentProfile->uploaded_files) > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Uploaded ID Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($residentProfile->uploaded_files as $file)
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-image text-primary me-2"></i>
                                            <strong>{{ basename($file) }}</strong>
                                        </div>
                                        <a href="{{ asset('uploads/documents/' . $file) }}" 
                                           target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($residentProfile->id_documents)
                    <hr>
                    <h6 class="mb-3">ID Types Submitted</h6>
                    <div class="row">
                        @foreach($residentProfile->id_documents as $idType => $idNumber)
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-info">{{ $idType }}</span>
                            @if($idNumber)
                                <small class="text-muted">: {{ $idNumber }}</small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Household Information -->
            @if($residentProfile->householdHead || $residentProfile->householdMembers->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Household Information</h5>
                </div>
                <div class="card-body">
                    @if($residentProfile->householdHead)
                    <div class="mb-3">
                        <small class="text-muted">Household Head</small>
                        <p class="mb-0">
                            <strong>{{ $residentProfile->householdHead->user->full_name }}</strong>
                            <span class="badge bg-primary ms-2">Head of Family</span>
                        </p>
                    </div>
                    @endif

                    @if($residentProfile->householdMembers->count() > 0)
                    <hr>
                    <h6 class="mb-3">Household Members</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Relationship</th>
                                    <th>Age</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($residentProfile->householdMembers as $member)
                                <tr>
                                    <td>{{ $member->user->full_name }}</td>
                                    <td>{{ $member->relationship_to_head ?? 'Member' }}</td>
                                    <td>{{ $member->user->age ?? 'N/A' }}</td>
                                    <td>
                                        @if($member->is_verified)
                                        <span class="badge bg-success">Verified</span>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Account Information -->
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Account Created</small>
                            <p class="mb-0">{{ $residentProfile->user->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Profile Created</small>
                            <p class="mb-0">{{ $residentProfile->created_at->format('F d, Y') }}</p>
                        </div>
                        @if($residentProfile->verified_at)
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Verified On</small>
                            <p class="mb-0">{{ $residentProfile->verified_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Verified By</small>
                            <p class="mb-0">{{ $residentProfile->verifier->name ?? 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload ID Modal -->
<div class="modal fade" id="uploadIdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload ID Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resident.profile.upload-id') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ID Type <span class="text-danger">*</span></label>
                        <select name="id_type" class="form-select" required>
                            <option value="">Select ID Type</option>
                            <option value="National ID">National ID (PhilSys)</option>
                            <option value="Voter's ID">Voter's ID</option>
                            <option value="Driver's License">Driver's License</option>
                            <option value="Passport">Passport</option>
                            <option value="UMID">UMID</option>
                            <option value="SSS ID">SSS ID</option>
                            <option value="GSIS ID">GSIS ID</option>
                            <option value="PhilHealth ID">PhilHealth ID</option>
                            <option value="Postal ID">Postal ID</option>
                            <option value="Senior Citizen ID">Senior Citizen ID</option>
                            <option value="PWD ID">PWD ID</option>
                            <option value="Other">Other Valid ID</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ID Number (Optional)</label>
                        <input type="text" name="id_number" class="form-control" placeholder="Enter ID number if applicable">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Upload File <span class="text-danger">*</span></label>
                        <input type="file" name="id_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Max 2MB. Allowed: JPG, PNG, PDF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection