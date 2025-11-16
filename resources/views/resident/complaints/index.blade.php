{{-- resources/views/resident/complaints/index.blade.php --}}
@extends('layouts.resident')

@section('title', 'My Complaints')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>My Complaints</h2>
        <p class="text-muted mb-0">Track and manage your filed complaints</p>
    </div>
    <div>
        <a href="{{ route('resident.complaints.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> File New Complaint
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total'] }}</h4>
                        <p class="card-text">Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['pending'] }}</h4>
                        <p class="card-text">Active</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['resolved'] }}</h4>
                        <p class="card-text">Resolved</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by complaint number or subject" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                    <option value="for_captain_review" {{ request('status') == 'for_captain_review' ? 'selected' : '' }}>For Captain Review</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="captain_mediation" {{ request('status') == 'captain_mediation' ? 'selected' : '' }}>Mediation</option>
                    <option value="for_lupon" {{ request('status') == 'for_lupon' ? 'selected' : '' }}>For Lupon</option>
                    <option value="settled_by_captain" {{ request('status') == 'settled_by_captain' ? 'selected' : '' }}>Settled</option>
                    <option value="resolved_by_lupon" {{ request('status') == 'resolved_by_lupon' ? 'selected' : '' }}>Resolved</option>
                    <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('resident.complaints.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <!-- Complaints List -->
        <div class="row">
            @forelse($complaints as $complaint)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $complaint->complaint_number }}</h6>
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
                            <h6 class="card-title">{{ Str::limit($complaint->subject, 50) }}</h6>
                            <p class="text-muted mb-2">
                                <small>
                                    <i class="fas fa-tag"></i> {{ $complaint->complaintType->name }} |
                                    <i class="fas fa-calendar"></i> {{ $complaint->created_at->format('M j, Y') }}
                                </small>
                            </p>
                            <p class="card-text">{{ Str::limit($complaint->description, 100) }}</p>
                            
                            @if($complaint->assignedOfficial)
                                <p class="text-info mb-2">
                                    <small><i class="fas fa-user"></i> Assigned to: {{ $complaint->assignedOfficial->name }}</small>
                                </p>
                            @endif

                            @if($complaint->hearings->count() > 0)
                                <p class="text-warning mb-2">
                                    <small><i class="fas fa-gavel"></i> {{ $complaint->hearings->count() }} hearing(s) scheduled</small>
                                </p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    {{ $complaint->created_at->diffForHumans() }}
                                </small>
                                <a href="{{ route('resident.complaints.show', $complaint) }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h4>No Complaints Filed</h4>
                        <p class="text-muted">You haven't filed any complaints yet.</p>
                        <a href="{{ route('resident.complaints.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> File Your First Complaint
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{ $complaints->links() }}
    </div>
</div>
@endsection