{{-- resources/views/barangay/complaints/workflow/index.blade.php --}}
@extends('layouts.barangay')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Complaint Management</h1>
        <div>
            <select class="form-select" onchange="window.location.href='?status='+this.value">
                <option value="">All Complaints</option>
                <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                <option value="for_captain_review" {{ request('status') == 'for_captain_review' ? 'selected' : '' }}>For Captain Review</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="captain_mediation" {{ request('status') == 'captain_mediation' ? 'selected' : '' }}>Mediation</option>
            </select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Pending Review</h6>
                    <h3>{{ $stats['pending_review'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">For Captain</h6>
                    <h3>{{ $stats['for_captain_review'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Summons Issued</h6>
                    <h3>{{ $stats['summons_issued'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="card-title">Mediation</h6>
                    <h3>{{ $stats['captain_mediation'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h6 class="card-title">Lupon Hearing</h6>
                    <h3>{{ $stats['lupon_hearing'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Settled</h6>
                    <h3>{{ $stats['settled'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Complaints Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Complaint #</th>
                            <th>Filed By</th>
                            <th>Type</th>
                            <th>Respondent(s)</th>
                            <th>Status</th>
                            <th>Filed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                        <tr>
                            <td><strong>{{ $complaint->complaint_number }}</strong></td>
                            <td>{{ $complaint->complainant->name }}</td>
                            <td>{{ $complaint->complaintType->name }}</td>
                            <td>{{ $complaint->respondent_names }}</td>
                            <td>
                                <span class="badge bg-{{ $complaint->workflow_status_color }}">
                                    {{ $complaint->workflow_status_label }}
                                </span>
                            </td>
                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('barangay.complaints-workflow.show', $complaint) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No complaints found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $complaints->links() }}
        </div>
    </div>
</div>

@php
function getStatusColor($status) {
    return match($status) {
        'pending_review' => 'info',
        'for_captain_review' => 'warning',
        'approved' => 'success',
        'dismissed' => 'danger',
        'captain_mediation' => 'secondary',
        'settled_by_captain', 'resolved_by_lupon' => 'success',
        default => 'primary'
    };
}
@endphp
@endsection