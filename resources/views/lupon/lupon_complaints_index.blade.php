{{-- resources/views/lupon/complaints/index.blade.php --}}
@extends('layouts.lupon')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-gavel"></i> My Assigned Complaints</h2>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Total Assigned</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['for_lupon'] }}</h3>
                    <small>Awaiting Hearing</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['ongoing_hearings'] }}</h3>
                    <small>Ongoing Hearings</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3 class="mb-0">{{ $stats['resolved'] }}</h3>
                    <small>Resolved</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lupon.complaints.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="for_lupon" {{ request('status') == 'for_lupon' ? 'selected' : '' }}>For Lupon</option>
                            <option value="1st_hearing_scheduled" {{ request('status') == '1st_hearing_scheduled' ? 'selected' : '' }}>1st Hearing Scheduled</option>
                            <option value="1st_hearing_ongoing" {{ request('status') == '1st_hearing_ongoing' ? 'selected' : '' }}>1st Hearing Ongoing</option>
                            <option value="1st_hearing_completed" {{ request('status') == '1st_hearing_completed' ? 'selected' : '' }}>1st Hearing Completed</option>
                            <option value="2nd_hearing_scheduled" {{ request('status') == '2nd_hearing_scheduled' ? 'selected' : '' }}>2nd Hearing Scheduled</option>
                            <option value="2nd_hearing_ongoing" {{ request('status') == '2nd_hearing_ongoing' ? 'selected' : '' }}>2nd Hearing Ongoing</option>
                            <option value="2nd_hearing_completed" {{ request('status') == '2nd_hearing_completed' ? 'selected' : '' }}>2nd Hearing Completed</option>
                            <option value="3rd_hearing_scheduled" {{ request('status') == '3rd_hearing_scheduled' ? 'selected' : '' }}>3rd Hearing Scheduled</option>
                            <option value="3rd_hearing_ongoing" {{ request('status') == '3rd_hearing_ongoing' ? 'selected' : '' }}>3rd Hearing Ongoing</option>
                            <option value="3rd_hearing_completed" {{ request('status') == '3rd_hearing_completed' ? 'selected' : '' }}>3rd Hearing Completed</option>
                            <option value="resolved_by_lupon" {{ request('status') == 'resolved_by_lupon' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="priority" class="form-select">
                            <option value="">All Priority</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="complaint_type_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach($complaintTypes as $type)
                                <option value="{{ $type->id }}" {{ request('complaint_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Complaints Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Case Number</th>
                            <th>Type</th>
                            <th>Complainant</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                        <tr>
                            <td>
                                <strong>{{ $complaint->complaint_number }}</strong>
                            </td>
                            <td>{{ $complaint->complaintType->name }}</td>
                            <td>{{ $complaint->complainant->full_name }}</td>
                            <td>{{ Str::limit($complaint->subject, 40) }}</td>
                            <td>
                                <span class="badge bg-{{ $complaint->priority_color }}">
                                    {{ strtoupper($complaint->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $complaint->workflow_status_color }}">
                                    {{ $complaint->workflow_status_label }}
                                </span>
                            </td>
                            <td>
                                {{ $complaint->assigned_to_lupon_at?->format('M d, Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ $complaint->assigned_to_lupon_at?->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <a href="{{ route('lupon.complaints.show', $complaint) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No complaints assigned to you yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
