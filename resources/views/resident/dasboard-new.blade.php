{{-- resources/views/resident/dashboard.blade.php --}}
@extends('layouts.resident')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-home"></i> Welcome, {{ $user->first_name }}!
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
        <span class="d-none d-sm-inline-block text-muted">
            <i class="fas fa-calendar"></i> {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- Alerts -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="fas fa-{{ $alert['icon'] }} mr-2"></i>
                <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
                @if(isset($alert['action']))
                <a href="{{ $alert['action'] }}" class="btn btn-sm btn-{{ $alert['type'] }} ml-2">{{ $alert['action_text'] }}</a>
                @endif
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistics Row -->
    <div class="row">
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
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($quickActions as $action)
                        <div class="col-md-3 mb-3">
                            @if($action['available'])
                            <a href="{{ $action['action'] }}" class="text-decoration-none">
                                <div class="card border-{{ $action['color'] }} h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-{{ $action['icon'] }} fa-3x text-{{ $action['color'] }} mb-3"></i>
                                        <h6 class="card-title">{{ $action['title'] }}</h6>
                                        <p class="card-text small text-muted">{{ $action['description'] }}</p>
                                    </div>
                                </div>
                            </a>
                            @else
                            <div class="card border-secondary h-100 opacity-50">
                                <div class="card-body text-center">
                                    <i class="fas fa-{{ $action['icon'] }} fa-3x text-secondary mb-3"></i>
                                    <h6 class="card-title text-muted">{{ $action['title'] }}</h6>
                                    <p class="card-text small text-muted">{{ $action['description'] }}</p>
                                    <small class="text-danger">Profile verification required</small>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Recent Documents -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">
                            <i class="fas fa-file-alt"></i> Document Requests
                        </h6>
                        @forelse($recentActivity['documents'] as $doc)
                        <div class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $doc->documentType->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        Submitted {{ $doc->submitted_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($doc->status) }}
                                    </span>
                                    <br>
                                    <a href="{{ route('resident.documents.show', $doc) }}" class="btn btn-sm btn-link p-0">View</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No recent document requests</p>
                        @endforelse
                        @if($recentActivity['documents']->count() > 0)
                        <a href="{{ route('resident.documents.index') }}" class="btn btn-sm btn-primary mt-2">
                            View All Documents
                        </a>
                        @endif
                    </div>

                    <!-- Recent Complaints -->
                    <div>
                        <h6 class="text-warning border-bottom pb-2">
                            <i class="fas fa-exclamation-triangle"></i> Complaints
                        </h6>
                        @forelse($recentActivity['complaints'] as $complaint)
                        <div class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $complaint->complaint_number }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ Str::limit($complaint->subject, 40) }}<br>
                                        Filed {{ $complaint->received_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $complaint->status_color }}">
                                        {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                    </span>
                                    <br>
                                    <a href="{{ route('resident.complaints.show', $complaint) }}" class="btn btn-sm btn-link p-0">View</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small">No complaints filed</p>
                        @endforelse
                        @if($recentActivity['complaints']->count() > 0)
                        <a href="{{ route('resident.complaints.index') }}" class="btn btn-sm btn-warning mt-2">
                            View All Complaints
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Items & Activity Chart -->
        <div class="col-lg-6 mb-4">
            <!-- Pending Items -->
            @if($pendingItems['pending_documents']->count() > 0 || $pendingItems['active_complaints']->count() > 0 || $pendingItems['expiring_permits']->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-bell"></i> Items Needing Attention
                    </h6>
                </div>
                <div class="card-body">
                    @if($pendingItems['pending_documents']->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-info"><i class="fas fa-file-alt"></i> Pending Documents ({{ $pendingItems['pending_documents']->count() }})</h6>
                        @foreach($pendingItems['pending_documents']->take(3) as $doc)
                        <div class="small mb-1">
                            • <strong>{{ $doc->documentType->name }}</strong> - {{ $doc->status }}
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($pendingItems['active_complaints']->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-warning"><i class="fas fa-exclamation-triangle"></i> Active Complaints ({{ $pendingItems['active_complaints']->count() }})</h6>
                        @foreach($pendingItems['active_complaints']->take(3) as $complaint)
                        <div class="small mb-1">
                            • <strong>{{ $complaint->complaint_number }}</strong> - {{ $complaint->status }}
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($pendingItems['expiring_permits']->count() > 0)
                    <div>
                        <h6 class="text-danger"><i class="fas fa-briefcase"></i> Expiring Permits ({{ $pendingItems['expiring_permits']->count() }})</h6>
                        @foreach($pendingItems['expiring_permits'] as $permit)
                        <div class="small mb-1">
                            • <strong>{{ $permit->businessPermitType->name ?? 'Business Permit' }}</strong> - Expires soon
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Activity Chart -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> My Activity (6 Months)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Services -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Available Services
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="text-primary">Document Types</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($availableServices['document_types']->take(5) as $type)
                                <li class="list-group-item px-0 py-1">
                                    <small>• {{ $type->name }}</small>
                                </li>
                                @empty
                                <li class="list-group-item px-0 py-1">
                                    <small class="text-muted">No document types available</small>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-warning">Complaint Types</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($availableServices['complaint_types']->take(5) as $type)
                                <li class="list-group-item px-0 py-1">
                                    <small>• {{ $type->name }}</small>
                                </li>
                                @empty
                                <li class="list-group-item px-0 py-1">
                                    <small class="text-muted">No complaint types available</small>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-success">Business Permit Types</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($availableServices['permit_types']->take(5) as $type)
                                <li class="list-group-item px-0 py-1">
                                    <small>• {{ $type->name }}</small>
                                </li>
                                @empty
                                <li class="list-group-item px-0 py-1">
                                    <small class="text-muted">No permit types available</small>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Activity Chart
const ctx = document.getElementById('activityChart').getContext('2d');
const monthlyActivity = @json($monthlyActivity);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: monthlyActivity.map(m => m.month),
        datasets: [
            {
                label: 'Documents',
                data: monthlyActivity.map(m => m.documents),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            },
            {
                label: 'Complaints',
                data: monthlyActivity.map(m => m.complaints),
                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                borderColor: 'rgb(255, 206, 86)',
                borderWidth: 1
            },
            {
                label: 'Permits',
                data: monthlyActivity.map(m => m.permits),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>
@endpush
@endsection