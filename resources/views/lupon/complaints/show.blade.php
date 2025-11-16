{{-- resources/views/lupon/complaints/show.blade.php --}}
@extends('layouts.lupon')

@section('content')
<div class="container-fluid">
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
            <h2>{{ $complaint->complaint_number }}</h2>
            <p class="text-muted mb-0">{{ $complaint->complaintType->name }}</p>
        </div>
        <a href="{{ route('lupon.complaints.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        {{-- Left Column: Details --}}
        <div class="col-md-8">
            {{-- Available Actions --}}
            @if(count($nextActions) > 0)
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Available Actions</h5>
                </div>
                <div class="card-body">
                    @foreach($nextActions as $action)
                        @if(str_contains($action['route'], 'schedule-hearing'))
                            <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                                    data-bs-toggle="modal" data-bs-target="#scheduleHearingModal">
                                <i class="fas fa-calendar-plus"></i> {{ $action['label'] }}
                            </button>
                        @elseif(str_contains($action['route'], 'record-resolution'))
                            <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                                    data-bs-toggle="modal" data-bs-target="#recordResolutionModal">
                                <i class="fas fa-check-circle"></i> {{ $action['label'] }}
                            </button>
                        @elseif(str_contains($action['route'], 'recommend-certificate'))
                            <button type="button" class="btn btn-{{ $action['color'] }} me-2" 
                                    data-bs-toggle="modal" data-bs-target="#recommendCertificateModal">
                                <i class="fas fa-file-certificate"></i> {{ $action['label'] }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Complaint Details --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Complaint Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Complainant:</strong><br>
                            {{ $complaint->complainant->full_name }}<br>
                            <small class="text-muted">{{ $complaint->complainant->email }}</small><br>
                            <small class="text-muted">{{ $complaint->complainant->address }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Respondent(s):</strong><br>
                            @foreach($complaint->respondent_info as $respondent)
                                <div class="mb-2">
                                    {{ $respondent['name'] }}
                                    @if($respondent['is_registered'])
                                        <span class="badge bg-success">Registered</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($respondent['type']) }}</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $respondent['address'] }}</small>
                                </div>
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
                        <strong>Incident Date:</strong> {{ $complaint->incident_date->format('F d, Y') }}<br>
                        <strong>Location:</strong> {{ $complaint->incident_location }}
                    </div>
                    @endif

                    @if($complaint->uploaded_files && count($complaint->uploaded_files) > 0)
                    <div class="mb-3">
                        <strong>Evidence Files:</strong><br>
                        @foreach($complaint->uploaded_files as $file)
                            <a href="{{ asset('uploads/complaints/' . $file) }}" target="_blank" 
                               class="btn btn-sm btn-outline-primary me-1 mb-1">
                                <i class="fas fa-file"></i> View Evidence
                            </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Hearings History --}}
            @if($complaint->hearings->count() > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Hearings History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Hearing #</th>
                                    <th>Scheduled Date</th>
                                    <th>Status</th>
                                    <th>Outcome</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($complaint->hearings as $hearing)
                                <tr>
                                    <td>{{ $hearing->hearing_number }}</td>
                                    <td>{{ $hearing->scheduled_date->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $hearing->status == 'completed' ? 'success' : ($hearing->status == 'ongoing' ? 'warning' : 'info') }}">
                                            {{ ucfirst($hearing->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $hearing->outcome ? ucfirst($hearing->outcome) : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('lupon.hearings.show', $hearing) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Lupon Resolution (if resolved) --}}
            @if($complaint->workflow_status === 'resolved_by_lupon' && $complaint->lupon_resolution)
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Lupon Resolution</h5>
                </div>
                <div class="card-body">
                    <p>{{ $complaint->lupon_resolution }}</p>
                    <small class="text-muted">
                        Resolved on: {{ $complaint->lupon_resolved_at->format('F d, Y h:i A') }}
                    </small>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Status & Timeline --}}
        <div class="col-md-4">
            {{-- Status Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Case Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge bg-{{ $complaint->workflow_status_color }} fs-6">
                            {{ $complaint->workflow_status_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Priority:</strong><br>
                        <span class="badge bg-{{ $complaint->priority_color }} fs-6">
                            {{ strtoupper($complaint->priority) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Assigned to Lupon:</strong><br>
                        {{ $complaint->assigned_to_lupon_at?->format('M d, Y') }}
                        <br>
                        <small class="text-muted">{{ $complaint->assigned_to_lupon_at?->diffForHumans() }}</small>
                    </div>

                    @if($complaint->total_hearings_conducted)
                    <div class="mb-3">
                        <strong>Hearings Conducted:</strong><br>
                        {{ $complaint->total_hearings_conducted }} / 3
                    </div>
                    @endif

                    @if($complaint->workflow_status !== 'resolved_by_lupon')
                    <div class="mb-3">
                        <strong>Days in Process:</strong><br>
                        <span class="fs-4">{{ now()->diffInDays($complaint->created_at) }}</span> days
                    </div>
                    @endif
                </div>
            </div>

            {{-- Assignment Notes --}}
            @if($complaint->lupon_assignment_notes)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Assignment Notes</h5>
                </div>
                <div class="card-body">
                    <p class="small">{{ $complaint->lupon_assignment_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
{{-- Schedule Hearing Modal --}}
<div class="modal fade" id="scheduleHearingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lupon.complaints.schedule-hearing', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Hearing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Scheduled Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_date" class="form-control" required
                               min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Venue <span class="text-danger">*</span></label>
                        <input type="text" name="venue" class="form-control" required
                               placeholder="e.g., Barangay Hall Conference Room">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Agenda <span class="text-danger">*</span></label>
                        <textarea name="agenda" class="form-control" rows="3" required
                                  placeholder="Purpose and topics to be discussed..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Lupon Members (optional)</label>
                        <select name="lupon_members[]" class="form-select" multiple>
                            {{-- This should be populated with other Lupon members --}}
                        </select>
                        <small class="text-muted">Select other Lupon members to assist in the hearing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule Hearing</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Record Resolution Modal --}}
<div class="modal fade" id="recordResolutionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lupon.complaints.record-resolution', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Record Lupon Resolution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This will mark the complaint as resolved by the Lupon.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Resolution Details <span class="text-danger">*</span></label>
                        <textarea name="lupon_resolution" class="form-control" rows="6" required
                                  placeholder="Document the agreed resolution, terms, conditions, and any commitments made by parties..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Notes (optional)</label>
                        <textarea name="lupon_resolution_notes" class="form-control" rows="3"
                                  placeholder="Any additional remarks or observations..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Resolution</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Recommend Certificate Modal --}}
<div class="modal fade" id="recommendCertificateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lupon.complaints.recommend-certificate', $complaint) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Recommend Certificate to File Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This indicates that the case cannot be resolved at the barangay level and should be referred to a higher authority.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Recommendation Notes <span class="text-danger">*</span></label>
                        <textarea name="recommendation_notes" class="form-control" rows="4" required
                                  placeholder="Explain why the case should be certified for filing in court..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit Recommendation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
