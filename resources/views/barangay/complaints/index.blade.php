{{-- resources/views/barangay/complaints/index.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Complaint Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-comments"></i> Complaint Management
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                High Priority
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['high_priority'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Assigned to Me
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['assigned_to_me'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filters
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('barangay.complaints.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Search complaints...">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>In Process</option>
                            <option value="mediation" {{ request('status') == 'mediation' ? 'selected' : '' }}>Mediation</option>
                            <option value="hearing_scheduled" {{ request('status') == 'hearing_scheduled' ? 'selected' : '' }}>Hearing Scheduled</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Priority</label>
                        <select name="priority" class="form-control">
                            <option value="">All Priority</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Assignment</label>
                        <select name="assigned" class="form-control">
                            <option value="">All</option>
                            <option value="unassigned" {{ request('assigned') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            <option value="assigned_to_me" {{ request('assigned') == 'assigned_to_me' ? 'selected' : '' }}>Assigned to Me</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('barangay.complaints.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Complaints List --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Complaints List</h6>
        </div>
        <div class="card-body">
            @if($complaints->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Complaint #</th>
                            <th>Subject</th>
                            <th>Complainant</th>
                            <th>Respondent(s)</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Date Filed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                        <tr>
                            <td>
                                <strong>{{ $complaint->complaint_number }}</strong>
                            </td>
                            <td>{{ Str::limit($complaint->subject, 30) }}</td>
                            <td>{{ $complaint->complainant->name ?? 'N/A' }}</td>
                            <td>
                                {{ $complaint->respondent_names }}
                                @if($complaint->hasRegisteredRespondents())
                                <span class="badge badge-success badge-sm">Registered</span>
                                @endif
                                @if($complaint->hasUnregisteredRespondents())
                                <span class="badge badge-warning badge-sm">Unregistered</span>
                                @endif
                            </td>
                            <td>{{ $complaint->complaintType->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $complaint->priority_color }}">
                                    {{ ucfirst($complaint->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $complaint->status_color }}">
                                    {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($complaint->assignedOfficial)
                                {{ $complaint->assignedOfficial->name }}
                                @else
                                <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $complaint->received_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('barangay.complaints.show', $complaint) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $complaints->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No complaints found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection