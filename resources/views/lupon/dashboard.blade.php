@extends('layouts.lupon')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome, {{ $user->first_name }}!</h2>
            <p class="text-muted">{{ $barangay->name }} - {{ now()->format('l, F d, Y') }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Assigned Complaints</h6>
                            <h3 class="mb-0">{{ $stats['assigned_complaints'] }}</h3>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Cases</h6>
                            <h3 class="mb-0">{{ $stats['active_complaints'] }}</h3>
                        </div>
                        <i class="fas fa-tasks fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Resolved</h6>
                            <h3 class="mb-0">{{ $stats['resolved_complaints'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Hearings Conducted</h6>
                            <h3 class="mb-0">{{ $stats['hearings_conducted'] }}</h3>
                        </div>
                        <i class="fas fa-gavel fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Items -->
    @if(count($actionItems) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Action Items</h5>
                </div>
                <div class="card-body">
                    @foreach($actionItems as $item)
                    <div class="alert alert-{{ $item['type'] }} d-flex align-items-center mb-3">
                        <i class="fas fa-{{ $item['icon'] }} fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <strong>{{ $item['title'] }}</strong>
                            <p class="mb-0">{{ $item['message'] }}</p>
                        </div>
                        <a href="{{ $item['action'] }}" class="btn btn-{{ $item['type'] }} btn-sm">View</a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Today's Hearings -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Today's Hearings</h5>
                    <a href="{{ route('lupon.hearings.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($todaysHearings->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todaysHearings as $hearing)
                        <a href="{{ route('lupon.hearings.show', $hearing) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $hearing->complaint->subject ?? 'N/A' }}</h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $hearing->scheduled_date->format('h:i A') }}
                                        <span class="ms-2"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($hearing->venue, 25) }}</span>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $hearing->complaint->complainant->full_name ?? 'N/A' }}
                                    </small>
                                </div>
                                @php
                                    $statusColors = [
                                        'scheduled' => 'info',
                                        'ongoing' => 'warning',
                                        'completed' => 'success',
                                        'postponed' => 'secondary'
                                    ];
                                    $color = $statusColors[$hearing->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}">{{ ucfirst($hearing->status) }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-calendar-check fa-3x mb-3 d-block"></i>
                        No hearings scheduled for today
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- For Lupon (Awaiting Hearing Schedule) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-hourglass-half me-2 text-warning"></i>Awaiting Hearing Schedule</h5>
                    <a href="{{ route('lupon.complaints.index', ['status' => 'for_lupon']) }}" class="btn btn-sm btn-warning">View All</a>
                </div>
                <div class="card-body">
                    @if($assignedComplaints['for_hearing']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($assignedComplaints['for_hearing'] as $complaint)
                        <a href="{{ route('lupon.complaints.show', $complaint) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ Str::limit($complaint->subject, 40) }}</h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $complaint->complainant->full_name }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>Assigned: {{ $complaint->assigned_to_lupon_at->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $complaint->priority_color }} mb-1">{{ strtoupper($complaint->priority) }}</span><br>
                                    <span class="badge bg-info">For Lupon</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-check-circle fa-3x mb-3 d-block"></i>
                        All assigned complaints have scheduled hearings
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ongoing Hearings & Needs Decision -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-gavel me-2 text-primary"></i>Ongoing Hearings</h5>
                    <a href="{{ route('lupon.complaints.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($assignedComplaints['ongoing_hearings']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($assignedComplaints['ongoing_hearings'] as $complaint)
                        <a href="{{ route('lupon.complaints.show', $complaint) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ Str::limit($complaint->subject, 40) }}</h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $complaint->complainant->full_name }}
                                    </p>
                                    @if($complaint->latestHearing)
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $complaint->latestHearing->scheduled_date->format('M d, Y h:i A') }}
                                    </small>
                                    @endif
                                </div>
                                <span class="badge bg-warning">{{ $complaint->workflow_status_label }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        No ongoing hearings at the moment
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2 text-danger"></i>Needs Decision</h5>
                    <a href="{{ route('lupon.complaints.index', ['needs_decision' => true]) }}" class="btn btn-sm btn-danger">View All</a>
                </div>
                <div class="card-body">
                    @if($assignedComplaints['completed_hearings']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($assignedComplaints['completed_hearings'] as $complaint)
                        <a href="{{ route('lupon.complaints.show', $complaint) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ Str::limit($complaint->subject, 40) }}</h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-user me-1"></i>{{ $complaint->complainant->full_name }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle me-1"></i>Hearing {{ $complaint->current_hearing_number ?? 1 }} completed
                                    </small>
                                </div>
                                <span class="badge bg-info">{{ $complaint->workflow_status_label }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-check-double fa-3x mb-3 d-block"></i>
                        No completed hearings awaiting decision
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $performance['resolution_rate'] }}%</h3>
                            <p class="text-muted mb-0">Resolution Rate</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info">{{ $performance['avg_resolution_days'] }} days</h3>
                            <p class="text-muted mb-0">Avg. Resolution Time</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success">{{ $performance['successful_mediations'] }}%</h3>
                            <p class="text-muted mb-0">Successful Mediations</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning">{{ $performance['hearing_attendance'] }}%</h3>
                            <p class="text-muted mb-0">Hearing Attendance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Hearings (Next 7 Days) -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Upcoming Hearings (Next 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($upcomingHearings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hearing #</th>
                                    <th>Complaint</th>
                                    <th>Complainant</th>
                                    <th>Date & Time</th>
                                    <th>Venue</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingHearings as $hearing)
                                <tr>
                                    <td><strong>{{ $hearing->hearing_number }}</strong></td>
                                    <td>{{ Str::limit($hearing->complaint->subject ?? 'N/A', 30) }}</td>
                                    <td>{{ $hearing->complaint->complainant->full_name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $hearing->scheduled_date->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $hearing->scheduled_date->format('h:i A') }}</small>
                                    </td>
                                    <td>{{ Str::limit($hearing->venue, 25) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'info',
                                                'ongoing' => 'warning',
                                                'completed' => 'success',
                                                'postponed' => 'secondary'
                                            ];
                                            $color = $statusColors[$hearing->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($hearing->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('lupon.hearings.show', $hearing) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">
                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                        No upcoming hearings in the next 7 days
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection