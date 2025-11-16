@extends('layouts.resident')

@section('title', 'Resident Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Welcome, {{ $user->first_name }}!</h1>
            <p class="text-muted">{{ $barangay->name }} - Resident Portal</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('resident.profile.show') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
        </div>
    </div>

    <!-- Profile Status Alerts -->
    @foreach($alerts as $alert)
    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-{{ $alert['icon'] }} me-2"></i>
            <div class="flex-grow-1">
                <strong>{{ $alert['title'] }}</strong><br>
                {{ $alert['message'] }}
            </div>
            <a href="{{ $alert['action'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }} ms-2">
                {{ $alert['action_text'] }}
            </a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endforeach

    <!-- Quick Stats -->
    <div class="row mb-4">
        
        <!-- Document Requests -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Document Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['document_requests'] }}</div>
                            <div class="text-xs text-muted">{{ $stats['pending_documents'] }} pending</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                My Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['complaints'] }}</div>
                            <div class="text-xs text-muted">{{ $stats['active_complaints'] }} active</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Permits -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Business Permits
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['business_permits'] }}</div>
                            <div class="text-xs text-muted">{{ $stats['active_permits'] }} active</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Status -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Profile Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($residentProfile->is_verified)
                                <span class="text-success">Verified</span>
                                @else
                                <span class="text-warning">Pending</span>
                                @endif
                            </div>
                            @if(method_exists($residentProfile, 'getCompletionPercentageAttribute'))
                            <div class="text-xs text-muted">{{ $residentProfile->completion_percentage }}% complete</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($quickActions as $action)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 {{ $action['available'] ? '' : 'border-secondary' }}">
                                <div class="card-body text-center {{ $action['available'] ? '' : 'text-muted' }}">
                                    <i class="fas fa-{{ $action['icon'] }} fs-1 text-{{ $action['available'] ? $action['color'] : 'secondary' }}"></i>
                                    <h6 class="card-title mt-2">{{ $action['title'] }}</h6>
                                    <p class="card-text small">{{ $action['description'] }}</p>
                                    @if($action['available'])
                                        <a href="{{ $action['action'] }}" class="btn btn-{{ $action['color'] }} btn-sm">
                                            Get Started
                                        </a>
                                    @else
                                        <small class="text-muted">Profile verification required</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Items -->
    @if(!empty(array_filter($pendingItems, function($item) { return is_countable($item) ? count($item) > 0 : $item; })))
    <div class="row mb-4">
        <div class="col">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Items Requiring Your Attention
                    </h5>
                </div>
                <div class="card-body">
                    @if($pendingItems['profile_incomplete'])
                        <div class="alert alert-warning">
                            <strong>Complete Your Profile:</strong> Your profile is {{ $residentProfile->completion_percentage }}% complete.
                            <a href="{{ route('resident.profile.edit') }}" class="btn btn-sm btn-warning ms-2">Complete Now</a>
                        </div>
                    @endif

                    @if($pendingItems['profile_unverified'])
                        <div class="alert alert-info">
                            <strong>Profile Verification:</strong> Your profile is pending verification by barangay staff.
                            <a href="{{ route('resident.profile.show') }}" class="btn btn-sm btn-info ms-2">View Status</a>
                        </div>
                    @endif

                    @if($pendingItems['pending_documents']->isNotEmpty())
                        <div class="alert alert-primary">
                            <strong>Document Requests in Progress:</strong> 
                            You have {{ $pendingItems['pending_documents']->count() }} document(s) being processed.
                            <a href="{{ route('resident.documents.index') }}" class="btn btn-sm btn-primary ms-2">Check Status</a>
                        </div>
                    @endif

                    @if($pendingItems['active_complaints']->isNotEmpty())
                        <div class="alert alert-warning">
                            <strong>Active Complaints:</strong> 
                            You have {{ $pendingItems['active_complaints']->count() }} active complaint(s).
                            <a href="{{ route('resident.complaints.index') }}" class="btn btn-sm btn-warning ms-2">View Updates</a>
                        </div>
                    @endif

                    @if($pendingItems['expiring_permits']->isNotEmpty())
                        <div class="alert alert-danger">
                            <strong>Permits Expiring Soon:</strong> 
                            {{ $pendingItems['expiring_permits']->count() }} permit(s) will expire within 30 days.
                            <a href="" class="btn btn-sm btn-danger ms-2">Renew Now</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activity and Available Services -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Recent Documents -->
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="fas fa-file-alt"></i> Recent Documents</h6>
                            @forelse($recentActivity['documents']->take(3) as $doc)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ $doc->documentType->name }}</strong><br>
                                        <small class="text-muted">{{ $doc->submitted_at->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                </div>
                                @if(!$loop->last)<hr class="my-2">@endif
                            @empty
                                <p class="text-muted">No document requests yet</p>
                            @endforelse
                            @if($recentActivity['documents']->isNotEmpty())
                                <a href="{{ route('resident.documents.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    View All Documents
                                </a>
                            @endif
                        </div>

                        <!-- Recent Complaints -->
                        <div class="col-md-6">
                            <h6 class="text-warning"><i class="fas fa-comments"></i> Recent Complaints</h6>
                            @forelse($recentActivity['complaints']->take(3) as $complaint)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong>{{ Str::limit($complaint->subject, 30) }}</strong><br>
                                        <small class="text-muted">{{ $complaint->received_at->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $complaint->status == 'resolved' ? 'success' : ($complaint->status == 'pending' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($complaint->status) }}
                                    </span>
                                </div>
                                @if(!$loop->last)<hr class="my-2">@endif
                            @empty
                                <p class="text-muted">No complaints filed</p>
                            @endforelse
                            @if($recentActivity['complaints']->isNotEmpty())
                                <a href="{{ route('resident.complaints.index') }}" class="btn btn-sm btn-outline-warning mt-2">
                                    View All Complaints
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($recentActivity['permits']->isNotEmpty())
                    <hr>
                    <h6 class="text-success"><i class="fas fa-briefcase"></i> Recent Business Permits</h6>
                    @foreach($recentActivity['permits'] as $permit)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $permit->business_name }}</strong><br>
                                <small class="text-muted">{{ $permit->businessPermitType->name }} - {{ $permit->submitted_at->format('M d, Y') }}</small>
                            </div>
                            <span class="badge bg-{{ $permit->status == 'approved' ? 'success' : ($permit->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($permit->status) }}
                            </span>
                        </div>
                        @if(!$loop->last)<hr class="my-2">@endif
                    @endforeach
                    <a href="{{ route('') }}" class="btn btn-sm btn-outline-success mt-2">
                        View All Permits
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Services -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Available Services</h5>
                </div>
                <div class="card-body">
                    <!-- Document Types -->
                    <h6 class="text-primary mb-2"><i class="fas fa-file-alt"></i> Documents Available</h6>
                    <div class="list-group list-group-flush mb-3">
                        @foreach($availableServices['document_types']->take(5) as $docType)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <strong>{{ $docType->name }}</strong><br>
                                <small class="text-muted">{{ $docType->formatted_fee }} - {{ $docType->processing_days }} days</small>
                            </div>
                            <a href="{{ route('resident.documents.create', ['type' => $docType->slug]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Request
                            </a>
                        </div>
                        @endforeach
                        @if($availableServices['document_types']->count() > 5)
                            <div class="list-group-item px-0">
                                <a href="{{ route('resident.documents.create') }}" class="text-primary">
                                    View all {{ $availableServices['document_types']->count() }} document types...
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <h6 class="text-secondary mb-2"><i class="fas fa-link"></i> Quick Links</h6>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-secondary btn-sm" onclick="event.preventDefault(); promptTrackingNumber();">
                            <i class="fas fa-search"></i> Track Request
                        </a>
                        <a href="{{ route('public.barangay.home', $barangay->slug) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-house-user"></i> Barangay Info
                        </a>
                        <a href="{{ route('public.services') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-list"></i> All Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Chart -->
    @if(!empty($monthlyActivity))
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Activity (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Track Request Function
function promptTrackingNumber() {
    const trackingNumber = prompt('Enter your tracking number:');
    if (trackingNumber) {
        window.location.href = "{{ route('track.request', '') }}/" + trackingNumber;
    }
}

// Activity Chart
@if(!empty($monthlyActivity))
document.addEventListener('DOMContentLoaded', function() {
    const activityData = @json($monthlyActivity);
    const ctx = document.getElementById('activityChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: activityData.map(item => item.month),
            datasets: [{
                label: 'Documents',
                data: activityData.map(item => item.documents),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }, {
                label: 'Complaints',
                data: activityData.map(item => item.complaints),
                backgroundColor: 'rgba(255, 193, 7, 0.5)',
                borderColor: 'rgb(255, 193, 7)',
                borderWidth: 1
            }, {
                label: 'Permits',
                data: activityData.map(item => item.permits),
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
@endif
</script>
@endpush
@endsection