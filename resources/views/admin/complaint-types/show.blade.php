@extends('layouts.admin')

@section('title', $complaintType->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Complaint Type Details</h2>
    <div>
        <a href="{{ route('admin.complaint-types.edit', $complaintType) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.complaint-types.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Complaint Type Information -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar-lg mb-3 mx-auto bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-gavel fa-2x"></i>
                </div>
                <h4>{{ $complaintType->name }}</h4>
                <p class="text-muted">{{ $complaintType->description }}</p>
                
                <div class="mb-3">
                    <span class="badge bg-secondary">{{ ucfirst($complaintType->category) }}</span>
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_', ' ', $complaintType->default_handler_type)) }}
                    </span>
                    @if($complaintType->requires_hearing)
                        <span class="badge bg-warning">Requires Hearing</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    @if($complaintType->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Configuration Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Configuration</h6>
            </div>
            <div class="card-body">
                <p><strong>Estimated Resolution:</strong><br>{{ $complaintType->estimated_resolution_days }} days</p>
                <p><strong>Default Handler:</strong><br>{{ ucfirst(str_replace('_', ' ', $complaintType->default_handler_type)) }}</p>
                <p><strong>Requires Hearing:</strong><br>
                    @if($complaintType->requires_hearing)
                        <span class="badge bg-warning">Yes</span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </p>
                <p><strong>Sort Order:</strong><br>{{ $complaintType->sort_order }}</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.complaint-types.edit', $complaintType) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Complaint Type
                    </a>
                    @if($complaintType->complaints_count == 0)
                        <form action="{{ route('admin.complaint-types.destroy', $complaintType) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Delete this complaint type?')">
                                <i class="fas fa-trash"></i> Delete Complaint Type
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $stats['total_complaints'] }}</h4>
                        <p class="card-text">Total Complaints</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $stats['active_complaints'] }}</h4>
                        <p class="card-text">Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $stats['resolved_complaints'] }}</h4>
                        <p class="card-text">Resolved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $stats['dismissed_complaints'] }}</h4>
                        <p class="card-text">Dismissed</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Performance Metrics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-primary">{{ $stats['avg_resolution_days'] }} days</h4>
                            <p class="text-muted">Average Resolution Time</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h4 class="text-success">
                                @if($stats['total_complaints'] > 0)
                                    {{ round(($stats['resolved_complaints'] / $stats['total_complaints']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </h4>
                            <p class="text-muted">Resolution Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Required Information -->
        @if($complaintType->required_information && count($complaintType->required_information) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Required Information</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($complaintType->required_information as $info)
                    <li class="list-group-item">
                        <i class="fas fa-check text-success me-2"></i>{{ $info }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        
        <!-- Recent Complaints -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Complaints</h6>
            </div>
            <div class="card-body">
                @if($recentComplaints->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Complaint ID</th>
                                    <th>Complainant</th>
                                    <th>Barangay</th>
                                    <th>Status</th>
                                    <th>Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentComplaints as $complaint)
                                <tr>
                                    <td>
                                        <a href="{{ route('barangay.complaints.show', $complaint) }}" class="text-decoration-none">
                                            #{{ $complaint->id }}
                                        </a>
                                    </td>
                                    <td>{{ $complaint->complainant->full_name ?? 'N/A' }}</td>
                                    <td>{{ $complaint->barangay->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $complaint->status_badge['class'] ?? 'secondary' }}">
                                            {{ $complaint->status_badge['text'] ?? $complaint->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $complaint->received_at->format('M j, Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No complaints filed for this type yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
</style>
@endpush