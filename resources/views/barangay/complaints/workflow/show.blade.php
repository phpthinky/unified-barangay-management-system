{{-- resources/views/barangay/complaints-workflow/show.blade.php --}}
@extends('layouts.barangay')

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $complaint->complaint_number }}</h1>
            <small class="text-muted">{{ $complaint->complaintType->name }}</small>
        </div>
        <a href="{{ route('barangay.complaints-workflow.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        {{-- Left: Details --}}
        <div class="col-md-8">
            {{-- Complaint Details Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Complaint Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Complainant:</strong><br>
                            {{ $complaint->complainant->name }}<br>
                            <small class="text-muted">{{ $complaint->complainant->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Respondent(s):</strong><br>
                            @foreach($complaint->respondent_info as $respondent)
                                {{ $respondent['name'] }}<br>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Subject:</strong>
                        <p>{{ $complaint->subject }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p>{{ $complaint->description }}</p>
                    </div>

                    @if($complaint->incident_date)
                    <div class="mb-3">
                        <strong>Incident Date:</strong> {{ $complaint->incident_date->format('M d, Y') }}<br>
                        <strong>Location:</strong> {{ $complaint->incident_location }}
                    </div>
                    @endif

                    @if($complaint->uploaded_files)
                    <div class="mb-3">
                        <strong>Evidence Files:</strong><br>
                        @foreach($complaint->uploaded_files as $file)
                            <a href="{{ asset('uploads/complaints/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                <i class="fas fa-file"></i> View File
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Current Status & Actions --}}
          {{-- In show.blade.php - Available Actions section --}}

@if(count($nextActions) > 0)
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Available Actions</h5>
    </div>
    <div class="card-body">
        @php
            $captainDecisionShown = false;
        @endphp
        
        @foreach($nextActions as $action)
            @if(str_contains($action['route'], 'secretary-review'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#secretaryReviewModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(str_contains($action['route'], 'captain-decision') && !$captainDecisionShown)
                @php $captainDecisionShown = true; @endphp
                <button type="button" class="btn btn-success me-2" 
                        onclick="showDecisionModal('Approve')">
                    Approve
                </button>
                <button type="button" class="btn btn-danger me-2" 
                        onclick="showDecisionModal('Dismiss')">
                    Dismiss
                </button>
                
            @elseif(str_contains($action['route'], 'issue-summons'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#summonsModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(str_contains($action['route'], 'record-appearance'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#appearanceModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(str_contains($action['route'], 'start-mediation'))
                <form action="{{ route('barangay.complaints-workflow.start-mediation', $complaint) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $action['color'] }} me-2" 
                            onclick="return confirm('Start Captain mediation (15-day period)?')">
                        {{ $action['label'] }}
                    </button>
                </form>
                
            @elseif(str_contains($action['route'], 'record-settlement'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#settlementModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(str_contains($action['route'], 'assign-lupon'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#luponModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(str_contains($action['route'], 'issue-certificate'))
                <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                        data-bs-toggle="modal" data-bs-target="#certificateModal">
                    {{ $action['label'] }}
                </button>
                
            @elseif(!str_contains($action['route'], 'captain-decision'))
                {{-- Only show other actions that aren't captain-decision --}}
                <form action="{{ route($action['route'], $complaint) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $action['color'] }} me-2">
                        {{ $action['label'] }}
                    </button>
                </form>
            @endif
        @endforeach
    </div>
</div>
@endif

            {{-- Settlement Terms (if settled) --}}
            @if($complaint->workflow_status === 'settled_by_captain' && $complaint->settlement_terms)
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Settlement Agreement</h5>
                </div>
                <div class="card-body">
                    <p>{{ $complaint->settlement_terms }}</p>
                    <small class="text-muted">
                        Settled on: {{ $complaint->settled_by_captain_at->format('M d, Y h:i A') }}
                    </small>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Timeline --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Complaint Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($timeline as $event)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <span style="font-size: 1.5rem">{{ $event['icon'] }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $event['title'] }}</strong><br>
                                    <small class="text-muted">
                                        @if(is_object($event['date']))
                                            {{ $event['date']->format('M d, Y h:i A') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($event['date'])->format('M d, Y h:i A') }}
                                        @endif
                                    </small><br>
                                    @if($event['desc'])
                                        <small>{{ $event['desc'] }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No timeline events yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Days Counter --}}
            @if(!in_array($complaint->workflow_status, ['settled_by_captain', 'resolved_by_lupon', 'dismissed', 'certificate_issued']))
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ now()->diffInDays($complaint->created_at) }}</h3>
                    <small class="text-muted">Days in Process</small>
                    
                    @if($complaint->workflow_status === 'captain_mediation' && $complaint->captain_mediation_deadline)
                        <hr>
                        <div class="alert alert-warning mb-0 mt-2">
                            <strong>Mediation Deadline:</strong><br>
                            {{ \Carbon\Carbon::parse($complaint->captain_mediation_deadline)->format('M d, Y') }}<br>
                            <small>({{ now()->diffInDays(\Carbon\Carbon::parse($complaint->captain_mediation_deadline)) }} days remaining)</small>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
@include('barangay.complaints.workflow.modals')

@endsection

@push('scripts')
<script>
// Modal functions for Bootstrap 5.3
function showDecisionModal(decision) {
    const modalEl = document.getElementById('captainDecisionModal');
    if (!modalEl) {
        console.error('Modal element not found');
        return;
    }
    
    const modal = new bootstrap.Modal(modalEl);
    const isApprove = decision.toLowerCase().includes('approve');
    
    document.getElementById('decisionInput').value = isApprove ? 'approve' : 'dismiss';
    document.getElementById('decisionModalTitle').textContent = decision;
    
    const submitBtn = document.getElementById('decisionSubmitBtn');
    submitBtn.textContent = decision;
    submitBtn.className = 'btn ' + (isApprove ? 'btn-success' : 'btn-danger');
    
    modal.show();
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush