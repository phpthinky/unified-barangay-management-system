@extends('layouts.barangay')

@section('title', 'Inhabitant Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user"></i> Inhabitant Details
            <small class="text-muted">{{ $inhabitant->registry_number }}</small>
        </h1>
        <div>
            <a href="{{ route('barangay.inhabitants.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('barangay.inhabitants.edit', $inhabitant) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if(!$inhabitant->is_verified)
            <form action="{{ route('barangay.inhabitants.verify', $inhabitant) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Verify this inhabitant?')">
                    <i class="fas fa-check"></i> Verify
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Personal Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card"></i> Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Photo & Basic Info -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            @if($inhabitant->photo_path)
                                <img src="{{ Storage::url($inhabitant->photo_path) }}" alt="Photo" class="img-thumbnail mb-2" style="max-width: 150px;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center mb-2" style="width: 150px; height: 150px; margin: 0 auto;">
                                    <i class="fas fa-user fa-4x"></i>
                                </div>
                            @endif
                            
                            @if($inhabitant->is_verified)
                                <span class="badge badge-success badge-lg">Verified</span>
                            @else
                                <span class="badge badge-warning badge-lg">Pending Verification</span>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h3 class="mb-3">{{ $inhabitant->full_name }}</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Registry Number:</strong><br>{{ $inhabitant->registry_number }}</p>
                                    <p class="mb-2"><strong>Age:</strong><br>{{ $inhabitant->age }} years old</p>
                                    <p class="mb-2"><strong>Sex:</strong><br>{{ $inhabitant->sex }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Date of Birth:</strong><br>{{ $inhabitant->date_of_birth->format('F d, Y') }}</p>
                                    <p class="mb-2"><strong>Civil Status:</strong><br>{{ $inhabitant->civil_status }}</p>
                                    <p class="mb-2"><strong>Citizenship:</strong><br>{{ $inhabitant->citizenship }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- NAME (1) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">NAME (1)</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Last Name (1):</strong><br>{{ $inhabitant->last_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>First Name (1-2):</strong><br>{{ $inhabitant->first_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Middle Name (1-3):</strong><br>{{ $inhabitant->middle_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-2"><strong>Ext. (1-4):</strong><br>{{ $inhabitant->ext ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- ADDRESS (2) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">ADDRESS (2)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-2"><strong>No (2-1):</strong><br>{{ $inhabitant->house_number ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-8">
                                <p class="mb-2"><strong>Name of Zone/Sitio (2-2):</strong><br>{{ $inhabitant->zone_sitio }}</p>
                            </div>
                        </div>
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-map-marker-alt"></i> <strong>Full Address:</strong> {{ $inhabitant->full_address }}
                        </div>
                    </div>

                    <!-- PLACE OF BIRTH (3-3) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">PLACE OF BIRTH (3-3)</h6>
                        <p class="mb-2">{{ $inhabitant->place_of_birth }}</p>
                    </div>

                    <!-- DATE OF BIRTH (4) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">DATE OF BIRTH (4)</h6>
                        <p class="mb-2">{{ $inhabitant->date_of_birth->format('m/d/Y') }} ({{ $inhabitant->age }} years old)</p>
                    </div>

                    <!-- SEX (5) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">SEX (5)</h6>
                        <p class="mb-2">{{ $inhabitant->sex }}</p>
                    </div>

                    <!-- CIVIL STATUS (6) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">CIVIL STATUS (6)</h6>
                        <p class="mb-2">{{ $inhabitant->civil_status }}</p>
                    </div>

                    <!-- CITIZENSHIP (7) -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">CITIZENSHIP (7)</h6>
                        <p class="mb-2">{{ $inhabitant->citizenship }}</p>
                    </div>

                    <!-- OCCUPATION -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">OCCUPATION</h6>
                        <p class="mb-2">{{ $inhabitant->occupation ?? 'N/A' }}</p>
                    </div>
{{-- ADD AFTER OCCUPATION SECTION (around line 132) --}}

<!-- EDUCATIONAL ATTAINMENT -->
<div class="mb-4">
    <h6 class="text-primary mb-3">EDUCATIONAL ATTAINMENT</h6>
    <p class="mb-2">{{ $inhabitant->educational_attainment ?? 'N/A' }}</p>
</div>

<!-- CONTACT INFORMATION -->
<div class="mb-4">
    <h6 class="text-primary mb-3">CONTACT INFORMATION</h6>
    <p class="mb-2">{{ $inhabitant->contact_number ?? 'N/A' }}</p>
</div>

<!-- EMERGENCY CONTACT -->
<div class="mb-4">
    <h6 class="text-primary mb-3">EMERGENCY CONTACT</h6>
    <div class="row">
        <div class="col-md-4">
            <p class="mb-2"><strong>Name:</strong><br>{{ $inhabitant->emergency_contact_name ?? 'N/A' }}</p>
        </div>
        <div class="col-md-4">
            <p class="mb-2"><strong>Number:</strong><br>{{ $inhabitant->emergency_contact_number ?? 'N/A' }}</p>
        </div>
        <div class="col-md-4">
            <p class="mb-2"><strong>Relationship:</strong><br>{{ $inhabitant->emergency_contact_relationship ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- ✅ RESIDENCY INFORMATION -->
<div class="mb-4">
    <h6 class="text-primary mb-3"><i class="fas fa-home"></i> RESIDENCY INFORMATION</h6>
    <div class="row">
        <div class="col-md-6">
            <p class="mb-2"><strong>Residency Since:</strong><br>
                @if($inhabitant->residency_since)
                    {{ $inhabitant->residency_since->format('F d, Y') }}
                    <br><small class="text-muted">({{ $inhabitant->getMonthsResided() }} months ago)</small>
                @else
                    <span class="text-muted">Not set</span>
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <p class="mb-2"><strong>Residency Type:</strong><br>{{ ucfirst($inhabitant->residency_type ?? 'N/A') }}</p>
        </div>
    </div>
    
    @if($inhabitant->residency_since)
        <div class="alert {{ $inhabitant->meetsResidencyRequirement() ? 'alert-success' : 'alert-warning' }} mt-2">
            @if($inhabitant->meetsResidencyRequirement())
                <i class="fas fa-check-circle"></i> <strong>Meets 6-month residency requirement</strong>
                <br><small>Eligible for barangay documents</small>
            @else
                <i class="fas fa-clock"></i> <strong>Does not meet 6-month requirement yet</strong>
                <br><small>Needs {{ 6 - $inhabitant->getMonthsResided() }} more months. Will be eligible on: {{ $inhabitant->getEligibleDate()->format('F d, Y') }}</small>
            @endif
        </div>
    @endif
</div>

<!-- ✅ PROOF OF RESIDENCY -->
<div class="mb-4">
    <h6 class="text-primary mb-3"><i class="fas fa-file-alt"></i> PROOF OF RESIDENCY</h6>
    <div class="row">
        <div class="col-md-6">
            <p class="mb-2"><strong>Cedula Number:</strong><br>{{ $inhabitant->cedula_number ?? 'N/A' }}</p>
        </div>
        <div class="col-md-6">
            <p class="mb-2"><strong>Certificate of Residency Number:</strong><br>{{ $inhabitant->certificate_of_residency_number ?? 'N/A' }}</p>
        </div>
    </div>
    
    @if($inhabitant->proof_of_residency_file)
        <div class="mt-2">
            <p class="mb-2"><strong>Proof Document:</strong></p>
            <a href="{{ asset($inhabitant->proof_of_residency_file) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="fas fa-file-download"></i> View Document
            </a>
        </div>
    @endif
</div>
                    @if($inhabitant->remarks)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">REMARKS</h6>
                        <p class="mb-2">{{ $inhabitant->remarks }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Household Information -->
            @if($inhabitant->household_number)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-home"></i> Household Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Household Number:</strong><br>{{ $inhabitant->household_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Role:</strong><br>
                                @if($inhabitant->is_household_head)
                                    <span class="badge badge-primary">Household Head</span>
                                @else
                                    <span class="badge badge-secondary">Household Member</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($inhabitant->householdMembers->count() > 0)
                    <h6 class="text-info mb-3">Household Members</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Sex</th>
                                    <th>Role</th>
                                    <th>Occupation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inhabitant->householdMembers as $member)
                                <tr>
                                    <td>
                                        <a href="{{ route('barangay.inhabitants.show', $member) }}">
                                            {{ $member->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $member->age }}</td>
                                    <td>{{ $member->sex }}</td>
                                    <td>
                                        @if($member->is_household_head)
                                            <span class="badge badge-primary">Head</span>
                                        @else
                                            <span class="badge badge-secondary">Member</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->occupation ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Verification Status:</strong></p>
                        @if($inhabitant->is_verified)
                            <span class="badge badge-success">Verified</span>
                        @else
                            <span class="badge badge-warning">Pending Verification</span>
                        @endif
                    </div>

                    @if($inhabitant->is_verified)
                    <div class="mb-3">
                        <p class="mb-1"><strong>Verified By:</strong></p>
                        <p class="mb-0">{{ $inhabitant->verifiedBy->full_name ?? 'N/A' }}</p>
                        <small class="text-muted">{{ $inhabitant->verified_at->format('M d, Y h:i A') }}</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <p class="mb-1"><strong>Account Status:</strong></p>
                        @if($inhabitant->user_id)
                            <span class="badge badge-success">Has User Account</span>
                        @else
                            <span class="badge badge-secondary">No User Account</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><strong>Registry Status:</strong></p>
                        @if($inhabitant->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($inhabitant->status == 'moved_out')
                            <span class="badge badge-warning">Moved Out</span>
                        @elseif($inhabitant->status == 'deceased')
                            <span class="badge badge-dark">Deceased</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Registration Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar"></i> Registration Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Registered On:</strong></p>
                        <p class="mb-0">{{ $inhabitant->registered_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><strong>Registered By:</strong></p>
                        <p class="mb-0">{{ $inhabitant->registeredBy->full_name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><strong>Last Updated:</strong></p>
                        <p class="mb-0">{{ $inhabitant->updated_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('barangay.inhabitants.edit', $inhabitant) }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-edit"></i> Edit Information
                    </a>

                    @if(!$inhabitant->is_verified)
                    <form action="{{ route('barangay.inhabitants.verify', $inhabitant) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block mb-2" onclick="return confirm('Verify this inhabitant?')">
                            <i class="fas fa-check"></i> Verify Inhabitant
                        </button>
                    </form>
                    @endif

                    @if(!$inhabitant->user_id)
                    <button class="btn btn-info btn-block mb-2" disabled>
                        <i class="fas fa-user-plus"></i> Create User Account
                    </button>
                    <small class="text-muted d-block mb-2">Feature coming soon</small>
                    @endif

                    <hr>

                    <form action="{{ route('barangay.inhabitants.destroy', $inhabitant) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this inhabitant? This action can be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-archive"></i> Archive
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection