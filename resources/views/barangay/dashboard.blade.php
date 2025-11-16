@extends('layouts.barangay')

@section('title', 'Barangay Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">{{ $barangay->name }} Dashboard</h1>
            <p class="text-muted">{{ ucfirst(str_replace('-', ' ', auth()->user()->getRoleNames()->first())) }} Portal</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('barangay.reports.index') }}" class="btn btn-outline-success">
                    <i class="bi bi-bar-chart"></i> Reports
                </a>
                <a href="{{ route('public.barangay.home', $barangay) }}" class="btn btn-outline-info" target="_blank">
                    <i class="bi bi-globe"></i> Public Page
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="row mb-4">
        <div class="col">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-{{ $alert['icon'] }} me-2"></i>
                    <div class="flex-grow-1">
                        <strong>{{ $alert['title'] }}</strong><br>
                        {{ $alert['message'] }}
                    </div>
                    <a href="{{ $alert['action'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }} ms-2">
                        View
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_residents'] ?? 0 }}</h4>
                            <p class="card-text">Total Residents</p>
                            <small>{{ $stats['verified_residents'] ?? 0 }} verified</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('barangay.residents.index') }}" class="text-white text-decoration-none">
                        <small>Manage Residents <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['document_requests'] ?? 0 }}</h4>
                            <p class="card-text">Document Requests</p>
                            @if(($stats['pending_documents'] ?? 0) > 0)
                                <small class="badge bg-warning text-dark">{{ $stats['pending_documents'] }} pending</small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('barangay.documents.index') }}" class="text-white text-decoration-none">
                        <small>Process Documents <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['complaints'] ?? 0 }}</h4>
                            <p class="card-text">Total Complaints</p>
                            @if(($stats['active_complaints'] ?? 0) > 0)
                                <small class="badge bg-danger">{{ $stats['active_complaints'] }} active</small>
                            @endif
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-chat-square-text fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('barangay.complaints.index') }}" class="text-dark text-decoration-none">
                        <small>Handle Complaints <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['business_permits'] ?? 0 }}</h4>
                            <p class="card-text">Business Permits</p>
                            <small>{{ $stats['active_permits'] ?? 0 }} active</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-briefcase fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('barangay.permits.index') }}" class="text-white text-decoration-none">
                        <small>Process Permits <i class="bi bi-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Processing Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Document Processing</span>
                            <span><strong>{{ $performance['document_processing_avg'] ?? 0 }} days avg</strong></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $docWidth = isset($performance['document_processing_avg']) ? max(0, min(100, (7 - $performance['document_processing_avg']) * 14.3)) : 0;
                            @endphp
                            <div class="progress-bar bg-info" style="width: {{ $docWidth }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Complaint Resolution</span>
                            <span><strong>{{ $performance['complaint_resolution_avg'] ?? 0 }} days avg</strong></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $compWidth = isset($performance['complaint_resolution_avg']) ? max(0, min(100, (20 - $performance['complaint_resolution_avg']) * 5)) : 0;
                            @endphp
                            <div class="progress-bar bg-warning" style="width: {{ $compWidth }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Permit Processing</span>
                            <span><strong>{{ $performance['permit_processing_avg'] ?? 0 }} days avg</strong></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $permitWidth = isset($performance['permit_processing_avg']) ? max(0, min(100, (10 - $performance['permit_processing_avg']) * 10)) : 0;
                            @endphp
                            <div class="progress-bar bg-success" style="width: {{ $permitWidth }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Contributions</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="text-primary">{{ $userStats['documents_processed'] ?? 0 }}</h3>
                            <small class="text-muted">Documents Processed</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning">{{ $userStats['complaints_handled'] ?? 0 }}</h3>
                            <small class="text-muted">Complaints Handled</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-success">{{ $userStats['permits_processed'] ?? 0 }}</h3>
                            <small class="text-muted">Permits Processed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Tasks -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks Requiring Attention</h5>
                    <small class="text-muted">Priority items</small>
                </div>
                <div class="card-body">
                    <!-- Pending Residents -->
                    @if(isset($pendingTasks['pending_residents']) && $pendingTasks['pending_residents']->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="bi bi-person-plus"></i> Pending Resident Verifications</h6>
                        @foreach($pendingTasks['pending_residents'] as $resident)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $resident->user->full_name ?? 'Unknown' }}</strong><br>
                                <small class="text-muted">Registered {{ $resident->created_at->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('barangay.residents.show', $resident) }}" class="btn btn-sm btn-outline-primary">
                                Review
                            </a>
                        </div>
                        @endforeach
                        @if($pendingTasks['pending_residents']->count() > 3)
                        <div class="text-center">
                            <a href="{{ route('barangay.residents.pending') }}" class="btn btn-sm btn-primary">
                                View All Pending
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Pending Documents -->
                    @if(isset($pendingTasks['pending_documents']) && $pendingTasks['pending_documents']->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="text-info"><i class="bi bi-file-earmark-text"></i> Pending Document Requests</h6>
                        @foreach($pendingTasks['pending_documents'] as $doc)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $doc->documentType->name ?? 'Unknown Document' }}</strong><br>
                                <small class="text-muted">{{ $doc->user->full_name ?? 'Unknown' }} - {{ $doc->submitted_at->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('barangay.documents.show', $doc) }}" class="btn btn-sm btn-outline-info">
                                Process
                            </a>
                        </div>
                        @endforeach
                        @if($pendingTasks['pending_documents']->count() > 3)
                        <div class="text-center">
                            <a href="{{ route('barangay.documents.index', ['status' => 'pending']) }}" class="btn btn-sm btn-info">
                                View All Pending
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Urgent Complaints -->
                    @if(isset($pendingTasks['urgent_complaints']) && $pendingTasks['urgent_complaints']->isNotEmpty())
                    <div class="mb-3">
                        <h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Urgent Complaints</h6>
                        @foreach($pendingTasks['urgent_complaints'] as $complaint)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ Str::limit($complaint->subject, 25) }}</strong><br>
                                <small class="text-muted">{{ $complaint->complainant->full_name ?? 'Unknown' }} - {{ $complaint->received_at->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('barangay.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-danger">
                                Handle
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if((!isset($pendingTasks['pending_residents']) || $pendingTasks['pending_residents']->isEmpty()) && 
                        (!isset($pendingTasks['pending_documents']) || $pendingTasks['pending_documents']->isEmpty()) && 
                        (!isset($pendingTasks['urgent_complaints']) || $pendingTasks['urgent_complaints']->isEmpty()))
                        <div class="text-center text-muted">
                            <i class="bi bi-check-circle fs-1"></i>
                            <p>All caught up! No urgent tasks at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Hearings -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upcoming Hearings</h5>
                </div>
                <div class="card-body">
                    @forelse($upcomingHearings ?? [] as $hearing)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>{{ $hearing->complaint->subject ?? 'Unknown Complaint' }}</strong><br>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> {{ $hearing->scheduled_date->format('M d, Y H:i') }}<br>
                                <i class="bi bi-geo-alt"></i> {{ $hearing->venue ?? 'TBA' }}
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $hearing->status_badge['class'] ?? 'secondary' }}">
                                {{ $hearing->status_badge['text'] ?? 'Unknown' }}
                            </span><br>
                            <small class="text-muted">{{ $hearing->scheduled_date->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <p>No upcoming hearings scheduled</p>
                    </div>
                    @endforelse

                    @if(isset($upcomingHearings) && $upcomingHearings->isNotEmpty())
                    <div class="text-center">
                        <a href="{{ route('lupon.hearings.index') }}" class="btn btn-sm btn-outline-primary">
                            View All Hearings
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Activity Chart -->
    @if(isset($monthlyData) && !empty($monthlyData))
    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monthly Activity Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Documents</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity['documents'] ?? [] as $doc)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $doc->documentType->name ?? 'Unknown Document' }}</strong><br>
                            <small class="text-muted">{{ $doc->user->full_name ?? 'Unknown' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $doc->status_badge['class'] ?? 'secondary' }}">
                                {{ $doc->status_badge['text'] ?? 'Unknown' }}
                            </span><br>
                            <small class="text-muted">{{ $doc->submitted_at->format('M d') }}</small>
                        </div>
                    </div>
                    @if(!$loop->last)<hr class="my-2">@endif
                    @empty
                    <p class="text-muted">No recent documents</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Complaints</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity['complaints'] ?? [] as $complaint)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ Str::limit($complaint->subject, 20) }}</strong><br>
                            <small class="text-muted">{{ $complaint->complainant->full_name ?? 'Unknown' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $complaint->priority_badge['class'] ?? 'secondary' }} mb-1">
                                {{ $complaint->priority_badge['text'] ?? 'Unknown' }}
                            </span><br>
                            <span class="badge bg-{{ $complaint->status_badge['class'] ?? 'secondary' }}">
                                {{ $complaint->status_badge['text'] ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>
                    @if(!$loop->last)<hr class="my-2">@endif
                    @empty
                    <p class="text-muted">No recent complaints</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Business Permits</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity['permits'] ?? [] as $permit)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ Str::limit($permit->business_name, 20) }}</strong><br>
                            <small class="text-muted">{{ $permit->applicant->full_name ?? 'Unknown' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $permit->status_badge['class'] ?? 'secondary' }}">
                                {{ $permit->status_badge['text'] ?? 'Unknown' }}
                            </span><br>
                            <small class="text-muted">{{ $permit->submitted_at->format('M d') }}</small>
                        </div>
                    </div>
                    @if(!$loop->last)<hr class="my-2">@endif
                    @empty
                    <p class="text-muted">No recent permits</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = @json($monthlyData ?? []);
    const ctx = document.getElementById('monthlyChart');
    
    if (ctx && monthlyData.length > 0) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'New Residents',
                    data: monthlyData.map(item => item.residents),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Documents',
                    data: monthlyData.map(item => item.documents),
                    borderColor: 'rgb(23, 162, 184)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Complaints',
                    data: monthlyData.map(item => item.complaints),
                    borderColor: 'rgb(255, 193, 7)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Permits',
                    data: monthlyData.map(item => item.permits),
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
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
    }
});
</script>
@endpush
@endsection