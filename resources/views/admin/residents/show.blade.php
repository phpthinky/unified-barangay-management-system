@extends('layouts.admin')

@section('title', 'Resident Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Resident Profile</h1>
        <div>
            @if(!$resident->is_verified)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal">
                <i class="fas fa-check-circle me-2"></i>Verify Resident
            </button>
            @else
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#unverifyModal">
                <i class="fas fa-times-circle me-2"></i>Unverify
            </button>
            @endif
            <a href="{{ route('admin.residents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Residents
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Verification Status Alert -->
            @if($resident->is_verified)
            <div class="alert alert-success shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Verified Resident</h5>
                        <p class="mb-0">
                            Verified on {{ $resident->verified_at->format('F d, Y h:i A') }}
                            @if($resident->verifier)
                                by {{ $resident->verifier->name }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1">Pending Verification</h5>
                        <p class="mb-0">This resident has not been verified yet.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Full Name</small>
                            <p class="mb-0 fw-bold">{{ $resident->user->name }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Email</small>
                            <p class="mb-0">{{ $resident->user->email }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $resident->user->contact_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Birth Date</small>
                            <p class="mb-0">
                                @if($resident->user->birth_date)
                                    {{ $resident->user->birth_date->format('F d, Y') }}
                                    <span class="text-muted">({{ $resident->user->birth_date->age }} years old)</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Gender</small>
                            <p class="mb-0">{{ $resident->user->gender ? ucfirst($resident->user->gender) : 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <small class="text-muted">Civil Status</small>
                            <p class="mb-0">{{ ucfirst($resident->civil_status) }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Nationality</small>
                            <p class="mb-0">{{ $resident->nationality }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Religion</small>
                            <p class="mb-0">{{ $resident->religion ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address & Residence Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address & Residence</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Barangay</small>
                            <p class="mb-0 fw-bold">{{ $resident->barangay->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Purok/Zone</small>
                            <p class="mb-0">{{ $resident->purok_zone }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Complete Address</small>
                        <p class="mb-0">{{ $resident->user->address }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Residency Type</small>
                            <p class="mb-0">
                                <span class="badge bg-info">{{ ucfirst($resident->residency_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Resident Since</small>
                            <p class="mb-0">{{ $resident->residency_since ? $resident->residency_since->format('F Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Registered Voter</small>
                            <p class="mb-0">
                                @if($resident->is_registered_voter)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Yes</span>
                                    @if($resident->precinct_number)
                                        <span class="text-muted ms-2">Precinct: {{ $resident->precinct_number }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Household Head</small>
                            <p class="mb-0">
                                @if($resident->is_household_head)
                                    <span class="badge bg-primary">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Livelihood & Economic Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Livelihood & Economic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Occupation</small>
                            <p class="mb-0">{{ $resident->occupation }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Monthly Income</small>
                            <p class="mb-0">
                                @if($resident->monthly_income)
                                    ₱{{ number_format($resident->monthly_income, 2) }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Educational Attainment</small>
                        <p class="mb-0">{{ $resident->educational_attainment }}</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card shadow mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Contact Name</small>
                            <p class="mb-0">{{ $resident->emergency_contact_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Contact Number</small>
                            <p class="mb-0">{{ $resident->emergency_contact_number }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Relationship</small>
                        <p class="mb-0">{{ $resident->emergency_contact_relationship }}</p>
                    </div>
                </div>
            </div>

            <!-- Service History -->
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Service History</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#documents-tab">
                                <i class="fas fa-file-alt me-2"></i>Documents
                                <span class="badge bg-primary ms-1">{{ $serviceHistory['documents']->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#complaints-tab">
                                <i class="fas fa-exclamation-triangle me-2"></i>Complaints
                                <span class="badge bg-warning ms-1">{{ $serviceHistory['complaints']->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#permits-tab">
                                <i class="fas fa-certificate me-2"></i>Permits
                                <span class="badge bg-success ms-1">{{ $serviceHistory['permits']->count() }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Documents Tab -->
                        <div class="tab-pane fade show active" id="documents-tab">
                            @if($serviceHistory['documents']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Status</th>
                                            <th>Date Requested</th>
                                            <th>Barangay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['documents'] as $doc)
                                        <tr>
                                            <td>{{ $doc->documentType->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($doc->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $doc->created_at->format('M d, Y') }}</td>
                                            <td>{{ $doc->processor?->barangay?->name ?? 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No document requests yet</p>
                            @endif
                        </div>

                        <!-- Complaints Tab -->
                        <div class="tab-pane fade" id="complaints-tab">
                            @if($serviceHistory['complaints']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Date Filed</th>
                                            <th>Barangay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['complaints'] as $complaint)
                                        <tr>
                                            <td>{{ $complaint->complaintType->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : ($complaint->status == 'in_process' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                            <td>{{ $complaint->barangay->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No complaints filed yet</p>
                            @endif
                        </div>

                        <!-- Permits Tab -->
                        <div class="tab-pane fade" id="permits-tab">
                            @if($serviceHistory['permits']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th>Permit Type</th>
                                            <th>Status</th>
                                            <th>Date Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['permits'] as $permit)
                                        <tr>
                                            <td>{{ $permit->business_name }}</td>
                                            <td>{{ $permit->businessPermitType->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $permit->status == 'approved' ? 'success' : ($permit->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($permit->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $permit->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center py-4">No business permits yet</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Special Classifications -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Special Classifications</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        @if($resident->is_senior_citizen)
                            <span class="badge bg-purple"><i class="fas fa-user-clock me-1"></i>Senior Citizen</span>
                        @endif
                        @if($resident->is_pwd)
                            <span class="badge bg-info"><i class="fas fa-wheelchair me-1"></i>PWD</span>
                            @if($resident->pwd_id_number)
                                <br><small class="text-muted">ID: {{ $resident->pwd_id_number }}</small>
                            @endif
                        @endif
                        @if($resident->is_solo_parent)
                            <span class="badge bg-pink"><i class="fas fa-user-friends me-1"></i>Solo Parent</span>
                        @endif
                        @if($resident->is_4ps_beneficiary)
                            <span class="badge bg-success"><i class="fas fa-hand-holding-usd me-1"></i>4Ps Beneficiary</span>
                        @endif
                        @if(!$resident->is_senior_citizen && !$resident->is_pwd && !$resident->is_solo_parent && !$resident->is_4ps_beneficiary)
                            <p class="text-muted mb-0">No special classifications</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Verification Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-{{ $resident->is_verified ? 'success' : 'warning' }} text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Verification Status</h5>
                </div>
                <div class="card-body">
                    @if($resident->is_verified)
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge bg-success">Verified</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Verified Date</small>
                            <p class="mb-0">{{ $resident->verified_at->format('F d, Y h:i A') }}</p>
                        </div>
                        @if($resident->verifier)
                        <div class="mb-3">
                            <small class="text-muted">Verified By</small>
                            <p class="mb-0">{{ $resident->verifier->name }}</p>
                            <small class="text-muted">{{ $resident->verifier->barangay->name ?? 'Municipality Admin' }}</small>
                        </div>
                        @endif
                        @if($resident->verification_notes)
                        <div class="mb-0">
                            <small class="text-muted">Notes</small>
                            <p class="mb-0 small">{{ $resident->verification_notes }}</p>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Pending Verification</strong>
                            <p class="mb-0 small mt-2">Registered on {{ $resident->created_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Account Status</small>
                        <p class="mb-0">
                            <span class="badge bg-{{ $resident->user->is_active ? 'success' : 'danger' }}">
                                {{ $resident->user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Registered Date</small>
                        <p class="mb-0">{{ $resident->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Last Updated</small>
                        <p class="mb-0">{{ $resident->updated_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- ID Documents -->
            @if(!empty($resident->uploaded_files) && count($resident->uploaded_files) > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Uploaded Documents</h5>
                </div>
                <div class="card-body">
                    @foreach($resident->uploaded_files as $file)
                    <div class="mb-2">
                        <a href="{{ asset('uploads/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-file me-2"></i>View Document {{ $loop->iteration }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Verify Resident</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.residents.verify', $resident) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>You are about to verify <strong>{{ $resident->user->name }}</strong> as an administrative override.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Override Reason <span class="text-danger">*</span></label>
                        <textarea name="override_reason" class="form-control" rows="3" required 
                                  placeholder="Explain why municipality admin is overriding barangay verification..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" 
                                  placeholder="Any additional notes..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This action will be logged as an administrative override.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Verify Resident</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unverify Modal -->
<div class="modal fade" id="unverifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Unverify Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.residents.unverify', $resident) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>You are about to remove verification for <strong>{{ $resident->user->name }}</strong>.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason for Unverification <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required 
                                  placeholder="Specify the reason for removing verification..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Override Reason <span class="text-danger">*</span></label>
                        <textarea name="override_reason" class="form-control" rows="2" required 
                                  placeholder="Explain why municipality admin is overriding..."></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will revoke the resident's verified status. This action will be logged.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Unverify Resident</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-purple {
    background-color: #6f42c1 !important;
}
.bg-pink {
    background-color: #e83e8c !important;
}
</style>
@endpush