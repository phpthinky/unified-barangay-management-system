@extends('layouts.abc')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Executive Summary Report</h2>
                    <p class="text-muted mb-0">{{ $dateRange['label'] }} - {{ now()->format('F d, Y') }}</p>
                </div>
                <div>
                    <a href="{{ route('abc.reports.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar"></i> {{ $dateRange['label'] }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?period=last_week">Last Week</a></li>
                            <li><a class="dropdown-item" href="?period=last_month">Last Month</a></li>
                            <li><a class="dropdown-item" href="?period=last_quarter">Last Quarter</a></li>
                            <li><a class="dropdown-item" href="?period=last_year">Last Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-buildings fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Barangays</h6>
                            <h3 class="mb-0">{{ $overview['total_barangays'] }}</h3>
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
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">New Residents</h6>
                            <h3 class="mb-0">{{ number_format($overview['total_residents']) }}</h3>
                            <small class="text-muted">
                                RBI: {{ $overview['rbi_residents'] }} | Online: {{ $overview['online_residents'] }}
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
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Officials</h6>
                            <h3 class="mb-0">{{ $overview['total_officials'] }}</h3>
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
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Services</h6>
                            <h3 class="mb-0">{{ number_format($overview['total_services']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Document Completion</span>
                                    <span class="badge bg-primary">{{ $performance['document_completion_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $performance['document_completion_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Complaint Resolution</span>
                                    <span class="badge bg-success">{{ $performance['complaint_resolution_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $performance['complaint_resolution_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Permit Approval</span>
                                    <span class="badge bg-info">{{ $performance['permit_approval_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ $performance['permit_approval_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Avg. Processing Days</span>
                                    <span class="badge bg-warning">{{ $performance['avg_processing_days'] }} days</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ max(0, 100 - ($performance['avg_processing_days'] * 10)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Service Quality</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>On-Time Completion</span>
                            <strong>{{ $serviceQuality['on_time_completion'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $serviceQuality['on_time_completion'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Satisfaction Rate</span>
                            <strong>{{ $serviceQuality['citizen_satisfaction'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $serviceQuality['citizen_satisfaction'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Digital Adoption</span>
                            <strong>{{ $serviceQuality['digital_adoption'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $serviceQuality['digital_adoption'] }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Staff Efficiency</span>
                            <strong>{{ $serviceQuality['staff_efficiency'] }}%</strong>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ $serviceQuality['staff_efficiency'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Top Performing Barangays</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Barangay</th>
                                    <th>Overall Score</th>
                                    <th>Verification Rate</th>
                                    <th>Completion Rate</th>
                                    <th>Avg. Processing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPerformers as $index => $performer)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                            <span class="badge bg-warning text-dark">🥇 #{{ $index + 1 }}</span>
                                        @elseif($index === 1)
                                            <span class="badge bg-secondary">🥈 #{{ $index + 1 }}</span>
                                        @elseif($index === 2)
                                            <span class="badge bg-warning text-dark" style="background-color: #cd7f32 !important;">🥉 #{{ $index + 1 }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('abc.reports.barangay', $performer['barangay']) }}" class="text-decoration-none">
                                            <strong>{{ $performer['barangay']->name }}</strong>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $performer['score'] }}</span>
                                    </td>
                                    <td>{{ $performer['verification_rate'] }}%</td>
                                    <td>{{ $performer['completion_rate'] }}%</td>
                                    <td>{{ $performer['avg_processing_days'] }} days</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Monthly Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyTrends, 'month')) !!},
        datasets: [
            {
                label: 'New Residents',
                data: {!! json_encode(array_column($monthlyTrends, 'new_residents')) !!},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            },
            {
                label: 'Documents',
                data: {!! json_encode(array_column($monthlyTrends, 'documents')) !!},
                borderColor: 'rgb(54, 162, 235)',
                tension: 0.1
            },
            {
                label: 'Complaints',
                data: {!! json_encode(array_column($monthlyTrends, 'complaints')) !!},
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1
            },
            {
                label: 'Permits',
                data: {!! json_encode(array_column($monthlyTrends, 'permits')) !!},
                borderColor: 'rgb(255, 206, 86)',
                tension: 0.1
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
                beginAtZero: true
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
</style>
@endpush