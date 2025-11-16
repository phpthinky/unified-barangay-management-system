@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(!$profileComplete)
        <div class="alert alert-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="alert-heading mb-2">Profile Incomplete</h5>
                    <p class="mb-0">Please complete your profile to access all services</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-warning">
                    Complete Profile <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif

    @if($profileComplete)
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Document Requests</h5>
                            <span class="badge bg-primary rounded-pill">{{ $pendingRequests }}</span>
                        </div>
                        <p class="card-text text-muted mb-4">Submit requests for barangay documents and certificates</p>
                        <a href="{{ route('requests.create') }}" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i> New Request
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Complaints</h5>
                            <span class="badge bg-danger rounded-pill">{{ $activeComplaints }}</span>
                        </div>
                        <p class="card-text text-muted mb-4">Report issues or concerns to the barangay</p>
                        <a href="{{ route('complaints.create') }}" class="btn btn-danger w-100">
                            <i class="bi bi-exclamation-triangle me-2"></i> File Complaint
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                @if($recentActivities->count())
                    <div class="list-group list-group-flush">
                        @foreach($recentActivities as $activity)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $activity->title }}</h6>
                                    <p class="mb-0 text-muted small">{{ $activity->description }}</p>
                                </div>
                                <span class="text-muted small">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar2-event text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No recent activities found</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection