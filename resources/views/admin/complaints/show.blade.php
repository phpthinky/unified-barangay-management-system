{{-- FILE: resources/views/admin/complaints/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Complaint Details - ' . $complaint->reference_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Complaint Details</h2>
        <p class="text-muted mb-0">Reference: <strong>{{ $complaint->reference_number }}</strong></p>
    </div>
    <div>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
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
                    <span class="badge {{ $complaint->status_badge }} me-2">{{ $complaint->status_text }}</span>
                    <span class="badge {{ $complaint->urgency_badge }}">{{ $complaint->urgency_text }}</span>
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
                        <strong>Days Open:</strong>
                        <p>{{ $complaint->days_open }} days</p>
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
                    @endif
                </div>
                @endif

                @if($complaint->evidence_file_path)
                <hr>
                <div class="mb-3">
                    <strong>Evidence Attached:</strong>
                    <div class="mt-2">
                        <a href="{{ asset($complaint->evidence_file_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-file"></i> View Evidence
                        </a>
                    </div>
                </div>
                @endif

                @if($complaint->resolution_details)
                <hr>
                <div class="mb-3">
                    <strong>Resolution Details:</strong>
                    <div class="border p-3 rounded bg-light">
                        {{ $complaint->resolution_details }}
                    </div>
                    @if($complaint->resolved_at)
                        <small class="text-muted">Resolved on: {{ $complaint->resolved_at->format('F j, Y \a\t g:i A') }}</small>
                    @endif
                </div>
                @endif

                @if($complaint->notes)
                <hr>
                <div class="mb-3">
                    <strong>Processing Notes:</strong>
                    <div class="border p-3 rounded">
                        {{ $complaint->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Parties Involved -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Parties Involved</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Complainant</h6>
                        <p><strong>{{ $complaint->complainant_name }}</strong></p>
                        @if($complaint->complainant_contact)
                            <p><i class="fas fa-phone"></i> {{ $complaint->complainant_contact }}</p>
                        @endif
                        @if($complaint->user && $complaint->user->residentProfile)
                            <p><i class="fas fa-envelope"></i> {{ $complaint->user->email }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $complaint->user->residentProfile->full_address }}</p>
                        @endif
                    </div>
                    
                    @if($complaint->respondent_name)
                    <div class="col-md-6">
                        <h6>Respondent</h6>
                        <p><strong>{{ $complaint->respondent_name }}</strong></p>
                        @if($complaint->respondent_address)
                            <p><i class="fas fa-map-marker-alt"></i> {{ $complaint->respondent_address }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hearings -->
        @if($complaint->hearings->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hearings Schedule</h5>
            </div>
            <div class="card-body">
                @foreach($complaint->hearings as $hearing)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>{{ $hearing->formatted_date_time }}</h6>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $hearing->location }}</p>
                            @if($hearing->agenda)
                                <p><strong>Agenda:</strong> {{ $hearing->agenda }}</p>
                            @endif
                        </div>
                        <span class="badge bg-{{ $hearing->status === 'completed' ? 'success' : 'info' }}">
                            {{ ucfirst($hearing->status) }}
                        </span>
                    </div>
                    
                    @if($hearing->minutes)
                    <div class="mt-3">
                        <strong>Minutes:</strong>
                        <div class="border p-2 rounded bg-light">
                            {{ $hearing->minutes }}
                        </div>
                    </div>
                    @endif

                    @if($hearing->next_hearing_date)
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-calendar"></i> Next hearing: {{ $hearing->next_hearing_date->format('F j, Y') }}
                        </small>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Actions -->
    <div class="col-lg-4">
        <!-- Status & Assignment -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Status & Assignment</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Current Status:</strong>
                    <div class="mt-2">
                        <span class="badge {{ $complaint->status_badge }}">{{ $complaint->status_text }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Assigned To:</strong>
                    <div class="mt-2">
                        @if($complaint->assignedTo)
                            <div class="d-flex align-items-center">
                                <span class="me-2">{{ $complaint->assignedTo->name }}</span>
                                <small class="text-muted">({{ $complaint->assignedTo->getRoleNames()->first() }})</small>
                            </div>
                            <small class="text-muted">
                                Assigned: {{ $complaint->assigned_at->format('M j, Y') }}
                            </small>
                        @else
                            <span class="text-muted">Not assigned</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Barangay:</strong>
                    <p>{{ $complaint->barangay->name }}</p>
                </div>

                <!-- Quick Actions -->
                @if($complaint->canBeResolved())
                <hr>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="fas fa-user-plus"></i> Assign Handler
                    </button>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <i class="fas fa-edit"></i> Update Status
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Complaint Statistics -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Expected Resolution:</small>
                    <div>{{ $complaint->complaintType->resolution_time_text }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Requires Hearing:</small>
                    <div>{{ $complaint->complaintType->requires_hearing ? 'Yes' : 'No' }}</div>
                </div>
                <div>
                    <small class="text-muted">Default Handler:</small>
                    <div>{{ $complaint->complaintType->default_handler_text }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Handler Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Handler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.assign-handler', $complaint) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select class="form-control" id="assigned_to" name="assigned_to" required>
                            <option value="">Select Handler</option>
                            <!-- This should be populated with barangay staff based on complaint's barangay -->
                            @foreach(\App\Models\User::whereHas('roles', function($q) {
                                $q->whereIn('name', ['barangay-captain', 'barangay-secretary', 'lupon']);
                            })->where('barangay_id', $complaint->barangay_id)->get() as $staff)
                                <option value="{{ $staff->id }}" {{ $complaint->assigned_to == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->getRoleNames()->first() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
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
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.update-status', $complaint) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="received" {{ $complaint->status == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="in_process" {{ $complaint->status == 'in_process' ? 'selected' : '' }}>In Process</option>
                            <option value="resolved" {{ $complaint->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div class="mb-3" id="resolution-details" style="display: {{ $complaint->status == 'resolved' ? 'block' : 'none' }};">
                        <label for="resolution_details" class="form-label">Resolution Details</label>
                        <textarea class="form-control" id="resolution_details" name="resolution_details" rows="4">{{ $complaint->resolution_details }}</textarea>
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

@push('scripts')
<script>
document.getElementById('status').addEventListener('change', function() {
    const resolutionDiv = document.getElementById('resolution-details');
    if (this.value === 'resolved') {
        resolutionDiv.style.display = 'block';
        document.getElementById('resolution_details').required = true;
    } else {
        resolutionDiv.style.display = 'none';
        document.getElementById('resolution_details').required = false;
    }
});
</script>
@endpush
@endsection