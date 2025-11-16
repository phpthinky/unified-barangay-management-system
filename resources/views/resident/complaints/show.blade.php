{{-- resources/views/resident/complaints/show.blade.php --}}
@extends('layouts.resident')

@section('title', 'Complaint Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Complaint Details</h2>
        <p class="text-muted mb-0">Complaint #: <strong>{{ $complaint->complaint_number }}</strong></p>
    </div>
    <div>
        <a href="{{ route('resident.complaints.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Complaints
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Main Complaint Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Complaint Information</h5>
                <div>
                    <span class="badge bg-{{ $complaint->workflow_status_color }}">
                        {{ $complaint->workflow_status_label }}
                    </span>
                    <span class="badge {{ $complaint->priority_badge['class'] }} ms-1">
                        {{ $complaint->priority_badge['text'] }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Subject:</strong>
                        <p>{{ $complaint->subject }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Complaint Type:</strong>
                        <p>{{ $complaint->complaintType->name }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Description:</strong>
                    <div class="border p-3 rounded">
                        {{ $complaint->description }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date Filed:</strong>
                        <p>{{ $complaint->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Priority Level:</strong>
                        <p><span class="badge {{ $complaint->priority_badge['class'] }}">{{ $complaint->priority_badge['text'] }}</span></p>
                    </div>
                </div>

                @if($complaint->incident_date || $complaint->incident_location)
                <hr>
                <h6>Incident Details</h6>
                <div class="row">
                    @if($complaint->incident_date)
                    <div class="col-md-6">
                        <strong>Incident Date:</strong>
                        <p>{{ $complaint->incident_date->format('F j, Y') }}</p>
                    </div>
                    @endif
                    @if($complaint->incident_location)
                    <div class="col-md-6">
                        <strong>Incident Location:</strong>
                        <p>{{ $complaint->incident_location }}</p>
                    </div>
                    @endif>
                </div>
                @endif

                @if($complaint->respondents && count($complaint->respondents) > 0)
                <hr>
                <h6>Respondents/Other Parties</h6>
                @foreach($complaint->respondents as $index => $respondent)
                    <div class="border rounded p-2 mb-2">
                        <strong>{{ $respondent['name'] }}</strong>
                        @if(!empty($respondent['address']))
                            <br><small class="text-muted">Address: {{ $respondent['address'] }}</small>
                        @endif
                        @if(!empty($respondent['contact']))
                            <br><small class="text-muted">Contact: {{ $respondent['contact'] }}</small>
                        @endif
                    </div>
                @endforeach
                @endif

                @if($complaint->uploaded_files && count($complaint->uploaded_files) > 0)
                <hr>
                <div class="mb-3">
                    <strong>Evidence Files:</strong>
                    <div class="mt-2">
                        @foreach($complaint->uploaded_files as $file)
                            <a href="{{ asset('uploads/complaints/' . $file) }}" 
                               target="_blank" class="btn btn-sm btn-outline-info me-2 mb-1">
                                <i class="fas fa-file"></i> {{ basename($file) }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Settlement Terms (if settled by captain) --}}
                @if($complaint->workflow_status === 'settled_by_captain' && $complaint->settlement_terms)
                <hr>
                <div class="alert alert-success">
                    <h6><i class="fas fa-handshake"></i> Settlement Agreement</h6>
                    <p class="mb-0">{{ $complaint->settlement_terms }}</p>
                    <small class="text-muted">Settled on: {{ $complaint->settled_by_captain_at->format('F j, Y \a\t g:i A') }}</small>
                </div>
                @endif

                {{-- Lupon Resolution (if resolved by lupon) --}}
                @if($complaint->workflow_status === 'resolved_by_lupon' && $complaint->lupon_resolution)
                <hr>
                <div class="alert alert-success">
                    <h6><i class="fas fa-gavel"></i> Lupon Resolution</h6>
                    <p class="mb-0">{{ $complaint->lupon_resolution }}</p>
                    <small class="text-muted">Resolved on: {{ $complaint->lupon_resolved_at->format('F j, Y \a\t g:i A') }}</small>
                </div>
                @endif

                {{-- Certificate Issued --}}
                @if($complaint->workflow_status === 'certificate_issued')
                <hr>
                <div class="alert alert-info">
                    <h6><i class="fas fa-certificate"></i> Certificate to File Action</h6>
                    <p class="mb-0">Certificate Number: <strong>{{ $complaint->certificate_number }}</strong></p>
                    <p class="mb-0">Referred to: <strong>{{ $complaint->referred_to }}</strong></p>
                    <small class="text-muted">Issued on: {{ $complaint->certificate_issued_at->format('F j, Y \a\t g:i A') }}</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Upload Additional Evidence -->
        @if(in_array($complaint->workflow_status, ['pending_review', 'for_captain_review', 'approved', 'captain_mediation', 'for_lupon']))
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Upload Additional Evidence</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('resident.complaints.upload-evidence', $complaint) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">
                            <input type="file" class="form-control" name="evidence_files[]" multiple required
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        </div>
                    </div>
                    <div class="form-text">Upload additional evidence to support your complaint (Max: 5MB each)</div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- QR Code Tracking -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Track Your Complaint</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('track.request', $complaint->complaint_number)) }}" 
                         alt="QR Code" class="img-fluid">
                </div>
                <p class="text-muted mb-0">Scan this QR code to quickly check your complaint status</p>
                <small class="text-muted">Works from any device with a QR scanner</small>
            </div>
        </div>

        <!-- Workflow Progress -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Complaint Progress</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Current Stage:</small>
                    <div>
                        <span class="badge bg-{{ $complaint->workflow_status_color }}">
                            {{ $complaint->workflow_status_label }}
                        </span>
                    </div>
                </div>
                
                @if($complaint->secretary_reviewed_at)
                <div class="mb-2">
                    <small class="text-success"><i class="fas fa-check"></i> Reviewed by Secretary</small>
                </div>
                @endif
                
                @if($complaint->captain_approved_at)
                <div class="mb-2">
                    <small class="text-success"><i class="fas fa-check"></i> Approved by Captain</small>
                </div>
                @endif
                
                @if($complaint->summons_attempt > 0)
                <div class="mb-2">
                    <small class="text-info"><i class="fas fa-envelope"></i> Summons Issued ({{ $complaint->summons_attempt }}x)</small>
                </div>
                @endif
                
                @if($complaint->respondent_appeared_at)
                <div class="mb-2">
                    <small class="text-success"><i class="fas fa-user-check"></i> Respondent Appeared</small>
                </div>
                @endif
                
                @if($complaint->assignedOfficial)
                <div class="mb-2">
                    <small class="text-muted">Assigned To:</small>
                    <div>{{ $complaint->assignedOfficial->name }}</div>
                </div>
                @endif
                
                <div class="mb-2">
                    <small class="text-muted">Days in Process:</small>
                    <div><strong>{{ now()->diffInDays($complaint->created_at) }} days</strong></div>
                </div>
            </div>
        </div>

        <!-- Hearings Information -->
        @if($complaint->hearings->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Scheduled Hearings</h6>
            </div>
            <div class="card-body">
                @foreach($complaint->hearings as $hearing)
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>Hearing #{{ $hearing->hearing_number }}</strong>
                            <span class="badge {{ $hearing->status_badge['class'] }}">{{ $hearing->status_badge['text'] }}</span>
                        </div>
                        <small class="text-muted">
                            {{ $hearing->scheduled_date->format('M j, Y g:i A') }}<br>
                            Venue: {{ $hearing->venue }}
                        </small>
                        @if($hearing->presiding_officer)
                            <br><small>Officer: {{ $hearing->presidingOfficer->name }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Help & Support -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Need Help?</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">If you have questions about your complaint, you can:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone"></i> Call your barangay office</li>
                    <li><i class="fas fa-map-marker-alt"></i> Visit the barangay hall</li>
                    <li><i class="fas fa-envelope"></i> Send an email inquiry</li>
                </ul>
                <small class="text-muted">Reference your complaint number: <strong>{{ $complaint->complaint_number }}</strong></small>
            </div>
        </div>
    </div>
</div>
@endsection