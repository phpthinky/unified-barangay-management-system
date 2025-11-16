{{-- FILE: resources/views/barangay/residents/show.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Resident Details - ' . $resident->user->first_name . ' ' . $resident->user->last_name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('barangay.residents.index') }}">Residents</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $resident->user->first_name }} {{ $resident->user->last_name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user"></i> Resident Details
            </h1>
        </div>
        <div class="d-flex">
            <a href="{{ route('barangay.residents.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Personal Information -->
        <div class="col-xl-8">
            <!-- Verification Status Card -->
            {{-- FILE: resources/views/barangay/residents/show.blade.php --}}
{{-- Replace the Verification Status Card with this: --}}

<!-- Account Verification & Eligibility -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-shield-check me-2"></i>Account Verification & Eligibility
        </h6>
    </div>
    <div class="card-body">
        <!-- STATUS OVERVIEW -->
        <div class="row mb-4">
            <!-- Profile Status -->
            <div class="col-md-3 text-center">
                <div class="border rounded p-3 h-100">
                    <i class="fas fa-user-check fa-2x {{ $resident->is_verified ? 'text-success' : 'text-warning' }} mb-2"></i>
                    <h6>Profile Status</h6>
                    @if($resident->is_verified)
                        <span class="badge bg-success">Approved</span>
                        <small class="d-block text-muted mt-1">By: {{ $resident->verifier->name ?? 'System' }}</small>
                    @else
                        <span class="badge bg-warning">Pending</span>
                        <small class="d-block text-muted mt-1">Awaiting staff approval</small>
                    @endif
                </div>
            </div>

            <!-- Email Status -->
            <div class="col-md-3 text-center">
                <div class="border rounded p-3 h-100">
                    <i class="fas fa-envelope fa-2x {{ $resident->user->email_verified_at ? 'text-success' : 'text-warning' }} mb-2"></i>
                    <h6>Email Status</h6>
                    @if($resident->user->email_verified_at)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </div>
            </div>

            <!-- RBI Status -->
            <div class="col-md-3 text-center">
                <div class="border rounded p-3 h-100">
                    <i class="fas fa-archive fa-2x {{ $resident->rbiInhabitant ? 'text-success' : 'text-danger' }} mb-2"></i>
                    <h6>RBI Records</h6>
                    @if($resident->rbiInhabitant)
                        <span class="badge bg-success">Linked</span>
                        <small class="d-block text-muted mt-1">ID: {{ $resident->rbiInhabitant->id }}</small>
                    @else
                        <span class="badge bg-danger">Not Linked</span>
                    @endif
                </div>
            </div>

            <!-- Residential Status -->
            <div class="col-md-3 text-center">
                <div class="border rounded p-3 h-100">
                    <i class="fas fa-home fa-2x text-info mb-2"></i>
                    <h6>Residency</h6>
                    <span class="badge bg-info">{{ ucfirst($resident->residency_type) }}</span>
                    @if($resident->residency_since)
                        <small class="d-block text-muted mt-1">{{ $resident->residency_months }} months</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- ELIGILITY SUMMARY -->
        <div class="alert {{ $resident->is_verified && $resident->rbiInhabitant && $resident->meetsResidencyRequirement() ? 'alert-success' : 'alert-warning' }}">
            <h6><i class="fas fa-clipboard-check me-2"></i>Document Request Eligibility</h6>
            
            @if(!$resident->is_verified)
                <p class="mb-2">❌ <strong>Profile not approved</strong> - Account pending staff verification</p>
            @elseif(!$resident->rbiInhabitant)
                <p class="mb-2">❌ <strong>RBI record not linked</strong> - Cannot request documents until linked to official registry</p>
            @elseif(!$resident->meetsResidencyRequirement())
                <p class="mb-2">❌ <strong>Residency requirement not met</strong> - {{ $resident->residency_months }} months (need {{ $resident->remaining_months }} more months)</p>
            @else
                <p class="mb-2">✅ <strong>Eligible for document requests</strong> - Profile approved, RBI linked, and residency requirement met</p>
            @endif

            <!-- ACTIONS BASED ON STATUS -->
            <div class="mt-3">
                @if(!$resident->is_verified)
                    <form method="POST" action="{{ route('barangay.residents.verify', $resident) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-check-circle me-1"></i>Approve Profile
                        </button>
                    </form>
                @endif

                @if($resident->is_verified && !$resident->rbiInhabitant)
                    <a href="{{ route('barangay.inhabitants.quick-create', $resident->id) }}" 
                       class="btn btn-primary btn-sm">
                       <i class="fas fa-user-plus me-1"></i>Link to RBI
                    </a>
                @endif
            </div>
        </div>

        <!-- RE-VERIFICATION (for staff) -->
        @if($resident->is_verified)
        <div class="card border-info">
            <div class="card-body">
                <h6 class="text-info"><i class="fas fa-redo me-2"></i>Staff Actions</h6>
                <form method="POST" action="{{ route('barangay.residents.reverify', $resident) }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Re-verification Reason</label>
                        <textarea class="form-control" name="reason" rows="2" required
                                  placeholder="e.g., Re-checking eligibility, user reported issues..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-redo me-1"></i>Re-check All
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

            {{-- Personal Information --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if($resident->user->profile_photo)
                                <img class="rounded-circle mb-3" src="{{ asset('uploads/photos/' . $resident->user->profile_photo) }}" 
                                     alt="Profile Photo" width="150" height="150">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ substr($resident->user->first_name, 0, 1) }}{{ substr($resident->user->last_name, 0, 1) }}
                                </div>
                            @endif
                            <h5>{{ $resident->user->first_name }} {{ $resident->user->last_name }}</h5>
                            <p class="text-muted">{{ $resident->user->email }}</p>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <strong>Full Name:</strong>
                                    <p>{{ $resident->user->first_name }} {{ $resident->user->middle_name }} {{ $resident->user->last_name }} {{ $resident->user->suffix }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Email:</strong>
                                    <p>{{ $resident->user->email }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Contact Number:</strong>
                                    <p>{{ $resident->user->contact_number ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Birth Date:</strong>
                                    <p>
                                        {{ $resident->user->birth_date ? $resident->user->birth_date->format('M d, Y') : 'Not provided' }}
                                        @if($resident->age)
                                            <span class="text-muted">(Age: {{ $resident->age }})</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Gender:</strong>
                                    <p>{{ ucfirst($resident->user->gender ?? 'Not specified') }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Civil Status:</strong>
                                    <p>{{ ucfirst($resident->civil_status ?? 'Not specified') }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Nationality:</strong>
                                    <p>{{ $resident->nationality ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-sm-6 mb-3">
                                    <strong>Occupation:</strong>
                                    <p>{{ $resident->occupation ?? 'Not provided' }}</p>
                                </div>
                                
                                <div class="col-sm-12 mb-3">
                                    <strong>Educational Attainment:</strong>
                                    <p>{{ $resident->educational_attainment ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Address & Location</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>Barangay:</strong>
                            <p>{{ $resident->barangay->name }}</p>
                        </div>
                        
                        <div class="col-sm-6 mb-3">
                            <strong>Purok/Zone:</strong>
                            <p>{{ $resident->purok_zone ?? 'Not specified' }}</p>
                        </div>
                        
                        <div class="col-sm-12 mb-3">
                            <strong>Street Address:</strong>
                            <p>{{ $resident->user->address ?? 'Not provided' }}</p>
                        </div>
                        
                        <div class="col-sm-6 mb-3">
                            <strong>Residency Since:</strong>
                            <p>
                                {{ $resident->residency_since ? $resident->residency_since->format('M d, Y') : 'Not provided' }}
                                @if($resident->residency_since)
                                    <br><small class="text-muted">{{ $resident->residency_months }} months</small>
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-sm-6 mb-3">
                            <strong>Residency Type:</strong>
                            <p>{{ ucfirst($resident->residency_type ?? 'Not specified') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Emergency Contact</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4 mb-3">
                            <strong>Name:</strong>
                            <p>{{ $resident->emergency_contact_name ?? 'Not provided' }}</p>
                        </div>
                        
                        <div class="col-sm-4 mb-3">
                            <strong>Phone Number:</strong>
                            <p>{{ $resident->emergency_contact_number ?? 'Not provided' }}</p>
                        </div>
                        
                        <div class="col-sm-4 mb-3">
                            <strong>Relationship:</strong>
                            <p>{{ $resident->emergency_contact_relationship ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-xl-4">
            
            <!-- Service History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service History</h6>
                </div>
                <div class="card-body">
                    <!-- Document Requests -->
                    <div class="mb-4">
                        <h6 class="text-primary">Document Requests 
                            ({{ $serviceHistory['documents']->count() }})
                        </h6>
                        @if($serviceHistory['documents']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Status</th>
                                            <th>Requested</th>
                                            <th>Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['documents'] as $document)
                                        <tr>
                                            <td>{{ $document->documentType->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($document->status) }}</td>
                                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($document->processor)
                                                    {{ $document->processor->first_name }} {{ $document->processor->last_name }}
                                                @else
                                                    <span class="text-muted">Not processed</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No document requests found.</p>
                        @endif
                    </div>

                    <!-- Complaints -->
                    <div class="mb-4">
                        <h6 class="text-primary">Complaints 
                            ({{ $serviceHistory['complaints']->count() }})
                        </h6>
                        @if($serviceHistory['complaints']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Filed</th>
                                            <th>Assigned To</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['complaints'] as $complaint)
                                        <tr>
                                            <td>{{ $complaint->complaintType->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($complaint->status) }}</td>
                                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($complaint->assignedOfficial)
                                                    {{ $complaint->assignedOfficial->first_name }} {{ $complaint->assignedOfficial->last_name }}
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No complaints found.</p>
                        @endif
                    </div>

                    <!-- Business Permits -->
                    <div>
                        <h6 class="text-primary">Business Permits 
                            ({{ $serviceHistory['permits']->count() }})
                        </h6>
                        @if($serviceHistory['permits']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Business Name</th>
                                            <th>Permit Type</th>
                                            <th>Status</th>
                                            <th>Applied</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceHistory['permits'] as $permit)
                                        <tr>
                                            <td>{{ $permit->business_name }}</td>
                                            <td>{{ $permit->businessPermitType->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($permit->status) }}</td>
                                            <td>{{ $permit->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No business permits found.</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    {{-- QUICK ADD TO RBI BUTTON --}}
        @if(!$resident->rbiInhabitant)
            <a href="{{ route('barangay.inhabitants.quick-create', $resident->id) }}" 
               class="btn btn-primary btn-block w-100 mb-2"
               title="Add this resident to RBI Registry">
               <i class="fas fa-user-plus me-2"></i> Copy to RBI
            </a>
        @else
            <button class="btn btn-success btn-block w-100 mb-2" disabled>
                <i class="fas fa-check me-2"></i> Already in RBI
            </button>
            <a href="{{ route('barangay.inhabitants.show', $resident->rbiInhabitant->id) }}" 
               class="btn btn-info btn-block w-100 mb-2">
               <i class="fas fa-external-link-alt me-2"></i> View RBI Record
            </a>
        @endif
                    @if(!$resident->is_verified)
                        <form method="POST" action="{{ route('barangay.residents.verify', $resident) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block w-100" 
                                    onclick="return confirm('Run smart verification?')">
                                <i class="fas fa-check"></i> Run Smart Verification
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-info btn-block w-100 mb-2" 
                                data-bs-toggle="modal" data-bs-target="#reverifyModalSidebar">
                            <i class="fas fa-redo"></i> Re-verify Eligibility
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-block w-100 mb-2" 
                                data-bs-toggle="modal" data-bs-target="#unverifyModal">
                            <i class="fas fa-times"></i> Unverify Account
                        </button>
                    @endif
                    
                    <a href="mailto:{{ $resident->user->email }}" class="btn btn-secondary btn-block w-100 mb-2">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                    
                    @if($resident->user->contact_number)
                        <a href="tel:{{ $resident->user->contact_number }}" class="btn btn-secondary btn-block w-100">
                            <i class="fas fa-phone"></i> Call
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Re-verify Modal (Sidebar) -->
@if($resident->is_verified)
<div class="modal fade" id="reverifyModalSidebar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-redo me-2"></i>Re-verification Check</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('barangay.residents.reverify', $resident) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>System will re-check:</strong>
                        <ul class="mb-0 mt-2">
                            <li>✓ RBI record status</li>
                            <li>✓ 6-month residency</li>
                            <li>✓ Pending complaints</li>
                        </ul>
                    </div>

                    <p><strong>Account:</strong> {{ $resident->user->first_name }} {{ $resident->user->last_name }}</p>
                    <p><strong>Current Status:</strong> 
                        Verified
                        @if($resident->rbiInhabitant)
                            , RBI Linked
                        @endif
                    </p>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason for Re-verification <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required
                                  placeholder="e.g., User can't request documents, checking 6-month eligibility..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-redo"></i> Run Re-verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Unverify Modal -->
@if($resident->is_verified)
<div class="modal fade" id="unverifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Unverify Resident</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('barangay.residents.unverify', $resident) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will remove verification status. The resident will not be able to request documents.
                    </div>
                    
                    <p><strong>Resident:</strong> {{ $resident->user->first_name }} {{ $resident->user->last_name }}</p>
                    <p><strong>Verified by:</strong> {{ $resident->verifier->first_name ?? 'Unknown' }}</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason for Unverification <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required
                                  placeholder="Why is verification being removed?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Remove Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection