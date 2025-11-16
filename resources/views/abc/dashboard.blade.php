@extends('layouts.abc')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">ABC President Dashboard</h2>
                    <p class="text-muted mb-0">Welcome back, {{ auth()->user()->full_name }}</p>
                </div>
                <div>
                    <a href="{{ route('abc.reports.index') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-bar-graph"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if($alerts->isNotEmpty())
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                <i class="{{ $alert['icon'] }} me-2"></i>
                <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
                @if(isset($alert['url']))
                <a href="{{ $alert['url'] }}" class="alert-link ms-2">View Details</a>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Overview Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-buildings fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Barangays</h6>
                            <h2 class="mb-0">{{ $overview['active_barangays'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Residents</h6>
                            <h2 class="mb-0">{{ number_format($overview['total_residents']) }}</h2>
                            <small class="text-muted">
                                RBI: {{ number_format($overview['rbi_residents']) }} | 
                                Online: {{ number_format($overview['online_residents']) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-person-badge fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Officials</h6>
                            <h2 class="mb-0">{{ $overview['active_officials'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                <i class="bi bi-hourglass-split fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pending Items</h6>
                            <h2 class="mb-0">{{ $overview['pending_documents'] + $overview['pending_complaints'] + $overview['pending_permits'] }}</h2>
                            <small class="text-muted">Requires attention</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Statistics (Last 30 Days) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Service Statistics (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-file-earmark-text text-primary fs-1 mb-2"></i>
                                <h3 class="mb-1">{{ number_format($overview['recent_documents']) }}</h3>
                                <p class="text-muted mb-2">Document Requests</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $serviceStats['document_completion_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $serviceStats['document_completion_rate'] }}% completion rate</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-exclamation-triangle text-warning fs-1 mb-2"></i>
                                <h3 class="mb-1">{{ number_format($overview['recent_complaints']) }}</h3>
                                <p class="text-muted mb-2">Complaints Filed</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $serviceStats['complaint_resolution_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $serviceStats['complaint_resolution_rate'] }}% resolution rate</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-shop text-success fs-1 mb-2"></i>
                                <h3 class="mb-1">{{ number_format($overview['recent_permits']) }}</h3>
                                <p class="text-muted mb-2">Business Permits</p>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $serviceStats['permit_approval_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $serviceStats['permit_approval_rate'] }}% approval rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Service Trends (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performing Barangays -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Top Performers</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($topPerformers as $index => $performer)
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    @if($index === 0)
                                        <span class="badge bg-warning text-dark fs-5">🥇</span>
                                    @elseif($index === 1)
                                        <span class="badge bg-secondary fs-5">🥈</span>
                                    @elseif($index === 2)
                                        <span class="badge bg-warning text-dark fs-5" style="background-color: #cd7f32 !important;">🥉</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <a href="{{ route('abc.reports.barangay', $performer['barangay']) }}" class="text-decoration-none">
                                        <strong>{{ $performer['barangay']->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        Score: {{ $performer['score'] }} | 
                                        {{ $performer['verification_rate'] }}% verified
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barangay Performance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Barangay Performance Overview</h5>
                        <a href="{{ route('abc.reports.index') }}" class="btn btn-sm btn-outline-primary">
                            View Detailed Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th>Total Residents</th>
                                    <th>Verified</th>
                                    <th>Documents (30d)</th>
                                    <th>Complaints (30d)</th>
                                    <th>Permits (30d)</th>
                                    <th>Pending</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangayPerformance as $performance)
                                <tr>
                                    <td>
                                        <strong>{{ $performance['barangay']->name }}</strong>
                                    </td>
                                    <td>{{ number_format($performance['total_residents']) }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ number_format($performance['verified_residents']) }}
                                            ({{ $performance['verification_rate'] }}%)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $performance['documents_processed'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $performance['complaints_received'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $performance['permits_processed'] }}</span>
                                    </td>
                                    <td>
                                        @if($performance['pending_documents'] + $performance['pending_complaints'] > 0)
                                            <span class="badge bg-danger">
                                                {{ $performance['pending_documents'] + $performance['pending_complaints'] }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('abc.reports.barangay', $performance['barangay']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @forelse($recentActivities as $activity)
                        <div class="activity-item d-flex mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="activity-icon bg-{{ $activity['color'] }} bg-opacity-10 text-{{ $activity['color'] }} rounded-circle p-2">
                                    <i class="{{ $activity['icon'] }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $activity['title'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $activity['description'] }}</small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $activity['barangay'] }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $activity['color'] }}">{{ ucwords(str_replace('_', ' ', $activity['status'])) }}</span>
                                        <br>
                                        <small class="text-muted">{{ $activity['timestamp']->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-muted py-4">No recent activities</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Trends Chart
const ctx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyTrends, 'month')) !!},
        datasets: [
            {
                label: 'New Residents',
                data: {!! json_encode(array_column($monthlyTrends, 'residents')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Documents',
                data: {!! json_encode(array_column($monthlyTrends, 'documents')) !!},
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Complaints',
                data: {!! json_encode(array_column($monthlyTrends, 'complaints')) !!},
                borderColor: 'rgb(255, 206, 86)',
                backgroundColor: 'rgba(255, 206, 86, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Permits',
                data: {!! json_encode(array_column($monthlyTrends, 'permits')) !!},
                borderColor: 'rgb(153, 102, 255)',
                backgroundColor: 'rgba(153, 102, 255, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.activity-timeline .activity-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@endpush