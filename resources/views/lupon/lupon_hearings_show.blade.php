{{-- resources/views/lupon/hearings/show.blade.php --}}
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
            <h2>{{ $hearing->hearing_number }}</h2>
            <p class="text-muted mb-0">
                Case: <a href="{{ route('lupon.complaints.show', $hearing->complaint) }}">
                    {{ $hearing->complaint->complaint_number }}
                </a>
            </p>
        </div>
        <a href="{{ route('lupon.hearings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        {{-- Left Column: Hearing Details --}}
        <div class="col-md-8">
            {{-- Available Actions --}}
            @if($hearing->presiding_officer === auth()->id())
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Available Actions</h5>
                    </div>
                    <div class="card-body">
                        @if($hearing->status === 'scheduled')
                            <button type="button" class="btn btn-success me-2" 
                                    data-bs-toggle="modal" data-bs-target="#startHearingModal">
                                <i class="fas fa-play"></i> Start Hearing
                            </button>
                            <button type="button" class="btn btn-warning me-2" 
                                    data-bs-toggle="modal" data-bs-target="#postponeHearingModal">
                                <i class="fas fa-calendar-times"></i> Postpone Hearing
                            </button>
                        @elseif($hearing->status === 'ongoing')
                            <button type="button" class="btn btn-primary me-2" 
                                    data-bs-toggle="modal" data-bs-target="#completeHearingModal">
                                <i class="fas fa-check-circle"></i> Complete Hearing
                            </button>
                        @elseif($hearing->status === 'completed' && !$hearing->minutes)
                            <button type="button" class="btn btn-warning me-2" 
                                    data-bs-toggle="modal" data-bs-target="#uploadMinutesModal">
                                <i class="fas fa-upload"></i> Upload Minutes
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Hearing Information --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Hearing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Scheduled Date & Time:</strong><br>
                            {{ $hearing->scheduled_date->format('F d, Y - h:i A') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Venue:</strong><br>
                            {{ $hearing->venue }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Agenda:</strong>
                        <p>{{ $hearing->agenda }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Presiding Officer:</strong><br>
                            {{ $hearing->presidingOfficer->full_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Hearing Type:</strong><br>
                            {{ ucfirst($hearing->hearing_type ?? 'Lupon') }}
                        </div>
                    </div>

                    @if($hearing->lupon_members && count($hearing->lupon_members) > 0)
                    <div class="mb-3">
                        <strong>Lupon Members Present:</strong><br>
                        <ul class="list-unstyled">
                            @foreach($hearing->lupon_members as $memberId)
                                @php
                                    $member = \App\Models\User::find($memberId);
                                @endphp
                                @if($member)
                                    <li><i class="fas fa-user"></i> {{ $member->full_name }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($hearing->actual_start_time)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Actual Start Time:</strong><br>
                            {{ $hearing->actual_start_time->format('F d, Y - h:i A') }}
                        </div>
                        @if($hearing->actual_end_time)
                        <div class="col-md-6">
                            <strong>Actual End Time:</strong><br>
                            {{ $hearing->actual_end_time->format('F d, Y - h:i A') }}
                            <br>
                            <small class="text-muted">
                                Duration: {{ $hearing->actual_start_time->diffInMinutes($hearing->actual_end_time) }} minutes
                            </small>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Complaint Summary --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Complaint Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Subject:</strong> {{ $hearing->complaint->subject }}
                    </div>
                    <div class="mb-2">
                        <strong>Type:</strong> {{ $hearing->complaint->complaintType->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Complainant:</strong> {{ $hearing->complaint->complainant->full_name }}
                    </div>
                    <div class="mb-2">
                        <strong>Respondent(s):</strong>
                        @foreach($hearing->complaint->respondent_info as $respondent)
                            {{ $respondent['name'] }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Attendance --}}
            @if($hearing->attendees || $hearing->absent_parties)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Attendance</h5>
                </div>
                <div class="card-body">
                    @if($hearing->attendees)
                    <div class="mb-3">
                        <strong>Present:</strong>
                        <ul>
                            @foreach($hearing->attendees as $attendee)
                                <li>{{ $attendee }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($hearing->absent_parties && count($hearing->absent_parties) > 0)
                    <div>
                        <strong>Absent:</strong>
                        <ul>
                            @foreach($hearing->absent_parties as $absent)
                                <li class="text-danger">{{ $absent }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Minutes --}}
            @if($hearing->minutes)
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Hearing Minutes</h5>
                </div>
                <div class="card-body">
                    <p style="white-space: pre-wrap;">{{ $hearing->minutes }}</p>
                </div>
            </div>
            @endif

            {{-- Outcome & Resolution --}}
            @if($hearing->status === 'completed')
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Hearing Outcome</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Outcome:</strong>
                        <span class="badge bg-{{ $hearing->outcome === 'settled' ? 'success' : 'warning' }} fs-6">
                            {{ ucfirst($hearing->outcome ?? 'Pending') }}
                        </span>
                    </div>

                    @if($hearing->resolution)
                    <div class="mb-3">
                        <strong>Resolution:</strong>
                        <p>{{ $hearing->resolution }}</p>
                    </div>
                    @endif

                    @if($hearing->agreements_reached && count($hearing->agreements_reached) > 0)
                    <div class="mb-3">
                        <strong>Agreements Reached:</strong>
                        <ul>
                            @foreach($hearing->agreements_reached as $agreement)
                                <li>{{ $agreement }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($hearing->next_steps)
                    <div>
                        <strong>Next Steps:</strong>
                        <p>{{ $hearing->next_steps }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Uploaded Documents --}}
            @if($hearing->uploaded_documents && count($hearing->uploaded_documents) > 0)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Uploaded Documents</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($hearing->uploaded_documents as $doc)
                        <a href="{{ asset('uploads/hearings/' . $doc['filename']) }}" 
                           target="_blank" 
                           class="list-group-item list-group-item-action">
                            <i class="fas fa-file-pdf"></i> {{ $doc['original_name'] }}
                            <br>
                            <small class="text-muted">
                                Uploaded by {{ $doc['uploaded_by'] }} on {{ $doc['uploaded_at'] }}
                            </small>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Status --}}
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        @php
                            $statusColors = [
                                'scheduled' => 'info',
                                'ongoing' => 'warning',
                                'completed' => 'success',
                                'postponed' => 'secondary',
                                'cancelled' => 'danger'
                            ];
                            $color = $statusColors[$hearing->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }} fs-6">
                            {{ ucfirst($hearing->status) }}
                        </span>
                    </div>

                    @if($hearing->status === 'scheduled')
                    <div class="mb-3">
                        <strong>Time Until Hearing:</strong><br>
                        @if($hearing->scheduled_date->isPast())
                            <span class="text-danger">Overdue</span>
                        @elseif($hearing->scheduled_date->isToday())
                            <span class="badge bg-warning">TODAY</span>
                        @else
                            {{ $hearing->scheduled_date->diffForHumans() }}
                        @endif
                    </div>
                    @endif

                    @if($hearing->status === 'completed')
                    <div class="mb-3">
                        <strong>Completed:</strong><br>
                        {{ $hearing->actual_end_time->format('M d, Y h:i A') }}
                    </div>
                    @endif
                </div>
            </div>

            @if($hearing->status === 'completed' && !$hearing->minutes)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Action Required</strong><br>
                Hearing minutes need to be documented.
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
{{-- Start Hearing Modal --}}
<div class="modal fade" id="startHearingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lupon.hearings.start', $hearing) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Start Hearing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Parties Present <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="attendees[]" 
                                   value="{{ $hearing->complaint->complainant->full_name }}" checked>
                            <label class="form-check-label">
                                {{ $hearing->complaint->complainant->full_name }} (Complainant)
                            </label>
                        </div>
                        @foreach($hearing->complaint->respondent_info as $respondent)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="attendees[]" 
                                   value="{{ $respondent['name'] }}">
                            <label class="form-check-label">
                                {{ $respondent['name'] }} (Respondent)
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Absent Parties</label>
                        @foreach($hearing->complaint->respondent_info as $respondent)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="absent_parties[]" 
                                   value="{{ $respondent['name'] }}">
                            <label class="form-check-label">
                                {{ $respondent['name'] }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Start Hearing</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Complete Hearing Modal --}}
<div class="modal fade" id="completeHearingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('lupon.hearings.complete', $hearing) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Complete Hearing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hearing Minutes <span class="text-danger">*</span></label>
                        <textarea name="minutes" class="form-control" rows="6" required
                                  placeholder="Document what transpired during the hearing..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Outcome <span class="text-danger">*</span></label>
                        <select name="outcome" class="form-select" required>
                            <option value="">-- Select Outcome --</option>
                            <option value="settled">Settled/Resolved</option>
                            <option value="mediated">Mediated Agreement</option>
                            <option value="postponed">Postponed</option>
                            <option value="no_settlement">No Settlement Reached</option>
                            <option value="needs_next_hearing">Needs Next Hearing</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Resolution Details</label>
                        <textarea name="resolution" class="form-control" rows="4"
                                  placeholder="If settled, document the terms and conditions..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Next Steps</label>
                        <textarea name="next_steps" class="form-control" rows="3"
                                  placeholder="What needs to happen next?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Hearing</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Postpone Hearing Modal --}}
<div class="modal fade" id="postponeHearingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lupon.hearings.postpone', $hearing) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Postpone Hearing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required
                                  placeholder="Why is the hearing being postponed?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="new_date" class="form-control" required
                               min="{{ now()->addDay()->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Postpone Hearing</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Upload Minutes Modal --}}
<div class="modal fade" id="uploadMinutesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lupon.hearings.upload-minutes', $hearing) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Hearing Minutes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Documents <span class="text-danger">*</span></label>
                        <input type="file" name="documents[]" class="form-control" multiple 
                               accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">PDF or Word documents only (max 5MB each)</small>
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
