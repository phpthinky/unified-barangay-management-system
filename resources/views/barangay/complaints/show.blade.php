@extends('layouts.barangay')

@section('title', 'Complaint Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Complaint Details</h2>
        <p class="text-muted mb-0">Complaint #: <strong>{{ $complaint->complaint_number ?? 'N/A' }}</strong></p>
    </div>
    <div>
        <a href="{{ route('barangay.complaints.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Complaints
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Complaint Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Complaint Information</h5>
                <div>
                    <span class="badge {{ $complaint->status_badge['class'] ?? 'bg-secondary' }}">{{ $complaint->status_badge['text'] ?? ucfirst($complaint->status) }}</span>
                    <span class="badge {{ $complaint->priority_badge['class'] ?? 'bg-secondary' }} ms-1">{{ $complaint->priority_badge['text'] ?? ucfirst($complaint->priority) }}</span>
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
                        <p>{{ $complaint->complaintType->name ?? 'Unknown Type' }}</p>
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
                        <strong>Incident Date:</strong>
                        <p>{{ $complaint->incident_date ? $complaint->incident_date->format('F j, Y \a\t g:i A') : 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Incident Location:</strong>
                        <p>{{ $complaint->incident_location ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date Received:</strong>
                        <p>{{ $complaint->received_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Processing Time:</strong>
                        @if($complaint->resolved_at)
                            <p class="text-success">{{ $complaint->received_at->diffInDays($complaint->resolved_at) }} days to resolve</p>
                        @else
                            <p class="text-info">Processing for {{ $complaint->received_at->diffForHumans() }}</p>
                        @endif
                    </div>
                </div>

                @php
                    // Safely handle form_data
                    $formData = $complaint->form_data;
                    if (is_string($formData)) {
                        $formData = json_decode($formData, true) ?? [];
                    }
                @endphp

                @if($formData && count($formData) > 0)
                <div class="mb-3">
                    <strong>Additional Information:</strong>
                    <div class="border p-3 rounded">
                        @foreach($formData as $key => $value)
                            <div class="mb-1">
                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                {{ is_array($value) ? implode(', ', $value) : $value }}
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @php
                    // Safely handle uploaded_files
                    $uploadedFiles = $complaint->uploaded_files;
                    if (is_string($uploadedFiles)) {
                        $uploadedFiles = json_decode($uploadedFiles, true) ?? [];
                    }
                @endphp

                @if($uploadedFiles && count($uploadedFiles) > 0)
                <div class="mb-3">
                    <strong>Attached Files:</strong>
                    <div class="mt-2">
                        @foreach($uploadedFiles as $file)
                            <a href="{{ asset('uploads/complaints/' . $file) }}" 
                               target="_blank" class="btn btn-sm btn-outline-info me-2 mb-1">
                                <i class="fas fa-file"></i> {{ basename($file) }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Respondents Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Respondents</h5>
            </div>
            <div class="card-body">
                @php
                    // Safely handle respondents data
                    $respondents = $complaint->respondents;
                    if (is_string($respondents)) {
                        $respondents = json_decode($respondents, true) ?? [];
                    }
                    $respondents = is_array($respondents) ? $respondents : [];
                @endphp

                @if(count($respondents) > 0)
                    @foreach($respondents as $index => $respondent)
                        <div class="border p-3 rounded mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6>{{ $respondent['name'] ?? 'Unknown Respondent' }}</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Type:</small>
                                            <div>{{ ucfirst($respondent['type'] ?? 'unknown') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Contact:</small>
                                            <div>{{ $respondent['contact'] ?? 'Not provided' }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Address:</small>
                                        <div>{{ $respondent['address'] ?? 'Not provided' }}</div>
                                    </div>
                                    @if(isset($respondent['description']) && $respondent['description'])
                                    <div class="mt-2">
                                        <small class="text-muted">Description:</small>
                                        <div>{{ $respondent['description'] }}</div>
                                    </div>
                                    @endif
                                </div>
                                <div class="ms-3">
                                    @if(($respondent['type'] ?? '') === 'unregistered' && isset($residents))
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#linkRespondentModal"
                                                data-index="{{ $index }}"
                                                data-name="{{ $respondent['name'] ?? 'Unknown' }}">
                                            Link to Resident
                                        </button>
                                    @elseif(($respondent['type'] ?? '') === 'registered')
                                        <span class="badge bg-success">Registered</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No respondents listed.</p>
                @endif
            </div>
        </div>

        <!-- Resolution Details -->
        @if($complaint->resolution_details)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Resolution Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Resolution Type:</strong>
                    <p>{{ ucfirst($complaint->resolution_type ?? 'Not specified') }}</p>
                </div>
                <div class="mb-3">
                    <strong>Resolution:</strong>
                    <div class="border p-3 rounded bg-light">
                        {{ $complaint->resolution_details }}
                    </div>
                </div>
                @if($complaint->resolved_at && $complaint->resolver)
                    <small class="text-muted">
                        Resolved by {{ $complaint->resolver->name }} on {{ $complaint->resolved_at->format('M j, Y g:i A') }}
                    </small>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Actions -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @if($complaint->isPending())
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#assignModal">
                            <i class="fas fa-user-check"></i> Assign to Official
                        </button>
                    </div>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="fas fa-sync"></i> Update Status
                        </button>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resolveModal">
                            <i class="fas fa-check-circle"></i> Mark as Resolved
                        </button>
                    </div>
                @elseif($complaint->isResolved())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        This complaint has been resolved.
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="fas fa-sync"></i> Update Status
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Complainant Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Complainant Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $complaint->complainant->full_name ?? 'Unknown' }}</p>
                <p><strong>Email:</strong> {{ $complaint->complainant->email ?? 'Not provided' }}</p>
                <p><strong>Phone:</strong> {{ $complaint->complainant->phone ?? 'Not provided' }}</p>
                @if($complaint->complainant->address)
                    <p><strong>Address:</strong> {{ $complaint->complainant->address }}</p>
                @endif
            </div>
        </div>

        <!-- Assignment Information -->
        @if($complaint->assigned_to)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Assigned To</h6>
            </div>
            <div class="card-body">
                <p><strong>Official:</strong> {{ $complaint->assignedOfficial->name ?? 'Unknown' }}</p>
                <p><strong>Role:</strong> {{ ucfirst($complaint->assigned_role) }}</p>
                <p><strong>Assigned Date:</strong> {{ $complaint->assigned_at->format('M j, Y') }}</p>
                @if($complaint->assignment_notes)
                    <p><strong>Notes:</strong> {{ $complaint->assignment_notes }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <small class="text-muted">Received</small>
                        <div>{{ $complaint->received_at->format('M j, Y g:i A') }}</div>
                    </div>
                    @if($complaint->assigned_at)
                    <div class="timeline-item">
                        <small class="text-muted">Assigned</small>
                        <div>{{ $complaint->assigned_at->format('M j, Y g:i A') }}</div>
                    </div>
                    @endif
                    @if($complaint->resolved_at)
                    <div class="timeline-item">
                        <small class="text-muted">Resolved</small>
                        <div>{{ $complaint->resolved_at->format('M j, Y g:i A') }}</div>
                    </div>
                    @endif
                    @if($complaint->closed_at)
                    <div class="timeline-item">
                        <small class="text-muted">Closed</small>
                        <div>{{ $complaint->closed_at->format('M j, Y g:i A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barangay.complaints.assign', $complaint) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_role" class="form-label">Assign to Role</label>
                        <select class="form-select" id="assigned_role" name="assigned_role" required>
                            <option value="">Select Role</option>
                            <option value="captain">Barangay Captain</option>
                            <option value="secretary">Barangay Secretary</option>
                            <option value="lupon">Lupon Member</option>
                            <option value="staff">Barangay Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Select Official</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required disabled>
                            <option value="">Select Official</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_notes" class="form-label">Assignment Notes</label>
                        <textarea class="form-control" id="assignment_notes" name="assignment_notes" 
                                  rows="3" placeholder="Add any specific instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Complaint Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barangay.complaints.update-status', $complaint) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="received" {{ $complaint->status == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="assigned" {{ $complaint->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_process" {{ $complaint->status == 'in_process' ? 'selected' : '' }}>In Process</option>
                            <option value="mediation" {{ $complaint->status == 'mediation' ? 'selected' : '' }}>Mediation</option>
                            <option value="hearing_scheduled" {{ $complaint->status == 'hearing_scheduled' ? 'selected' : '' }}>Hearing Scheduled</option>
                            <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $complaint->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="dismissed" {{ $complaint->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" 
                                  rows="3" placeholder="Add status update notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Resolved</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barangay.complaints.resolve', $complaint) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="resolution_type" class="form-label">Resolution Type</label>
                        <select class="form-select" id="resolution_type" name="resolution_type" required>
                            <option value="">Select Resolution Type</option>
                            <option value="settled">Settled</option>
                            <option value="dismissed">Dismissed</option>
                            <option value="referred">Referred</option>
                            <option value="mediated">Mediated</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="resolution_details" class="form-label">Resolution Details</label>
                        <textarea class="form-control" id="resolution_details" name="resolution_details" 
                                  rows="4" placeholder="Describe the resolution..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Respondent Modal -->
<div class="modal fade" id="linkRespondentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Link Respondent to Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barangay.residents.link-rbi', $complaint) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="respondent_index" name="respondent_index">
                    <div class="mb-3">
                        <label class="form-label">Respondent to Link:</label>
                        <p class="form-control-plaintext" id="respondent_name"></p>
                    </div>
                    <div class="mb-3">
                        <label for="respondent_id" class="form-label">Select Registered Resident</label>
                        <select class="form-select" id="respondent_id" name="respondent_id" required>
                            <option value="">Select Resident</option>
                            @foreach($residents ?? [] as $resident)
                                <option value="{{ $resident->id }}">{{ $resident->name }} ({{ $resident->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Link Respondent</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
    padding-left: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #0d6efd;
}

.timeline-item:last-child {
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle role selection for assignment
    const roleSelect = document.getElementById('assigned_role');
    const officialSelect = document.getElementById('assigned_to');
    
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const role = this.value;
            officialSelect.disabled = !role;
            
            if (role) {
                fetch(`/barangay/complaints/officials/${role}`)
                    .then(response => response.json())
                    .then(data => {
                        officialSelect.innerHTML = '<option value="">Select Official</option>';
                        data.forEach(official => {
                            const option = document.createElement('option');
                            option.value = official.id;
                            option.textContent = official.name + (official.position_title ? ` (${official.position_title})` : '');
                            officialSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching officials:', error);
                    });
            }
        });
    }
    
    // Handle link respondent modal
    const linkModal = document.getElementById('linkRespondentModal');
    if (linkModal) {
        linkModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const index = button.getAttribute('data-index');
            const name = button.getAttribute('data-name');
            
            document.getElementById('respondent_index').value = index;
            document.getElementById('respondent_name').textContent = name;
        });
    }
});
</script>
@endpush
@endsection