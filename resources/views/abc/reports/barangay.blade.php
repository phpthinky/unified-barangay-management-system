@extends('layouts.abc')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $barangay->name }} Report</h2>
                    <p class="text-muted mb-0">{{ $barangayData['date_range'] }} - {{ now()->format('F d, Y') }}</p>
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

    <!-- Barangay Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($barangay->logo)
                                <img src="{{ $barangay->logo_url }}" alt="{{ $barangay->name }}" class="img-fluid rounded" style="max-height: 100px;">
                            @else
                                <div class="bg-light rounded p-4">
                                    <i class="bi bi-building fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-10">
                            <h4>{{ $barangay->name }}</h4>
                            <p class="text-muted mb-2">{{ $barangay->address }}</p>
                            <div class="row g-2">
                                @if($barangay->contact_number)
                                <div class="col-auto">
                                    <small><i class="bi bi-telephone"></i> {{ $barangay->contact_number }}</small>
                                </div>
                                @endif
                                @if($barangay->email)
                                <div class="col-auto">
                                    <small><i class="bi bi-envelope"></i> {{ $barangay->email }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Residents</h6>
                    <h3 class="mb-1">{{ number_format($barangayData['total_residents']) }}</h3>
                    <small class="text-muted">
                        RBI: {{ $barangayData['rbi_total'] }} | Online: {{ $barangayData['online_total'] }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Verified Residents</h6>
                    <h3 class="mb-1 text-success">{{ number_format($barangayData['verified_residents']) }}</h3>
                    <small class="text-muted">
                        RBI: {{ $barangayData['rbi_verified'] }} | Online: {{ $barangayData['online_verified'] }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Pending Verification</h6>
                    <h3 class="mb-1 text-warning">{{ number_format($barangayData['pending_residents']) }}</h3>
                    <small class="text-muted">Awaiting verification</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Verification Rate</h6>
                    <h3 class="mb-1 text-primary">
                        {{ $barangayData['total_residents'] > 0 ? round(($barangayData['verified_residents'] / $barangayData['total_residents']) * 100, 1) : 0 }}%
                    </h3>
                    <small class="text-muted">Of total residents</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Documents Processed</h6>
                            <h3 class="mb-0">{{ number_format($barangayData['documents_processed']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                <i class="bi bi-exclamation-triangle fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Complaints Received</h6>
                            <h3 class="mb-0">{{ number_format($barangayData['complaints_received']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="bi bi-shop fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Permits Processed</h6>
                            <h3 class="mb-0">{{ number_format($barangayData['permits_processed']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance vs Average -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Performance Comparison</h5>
                    <small class="text-muted">vs All Barangays Average</small>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Your Barangay</span>
                            <span>Average</span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Verified Residents</label>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    @php
                                        $maxVerified = max($comparisonData['current']['verified_residents'], $comparisonData['average']['verified_residents']);
                                        $verifiedWidth = $maxVerified > 0 ? ($comparisonData['current']['verified_residents'] / $maxVerified) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" style="width: {{ $verifiedWidth }}%">
                                            {{ $comparisonData['current']['verified_residents'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="ms-2 text-muted">{{ round($comparisonData['average']['verified_residents'], 1) }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Documents Processed</label>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    @php
                                        $maxDocs = max($comparisonData['current']['documents_processed'], $comparisonData['average']['documents_processed']);
                                        $docsWidth = $maxDocs > 0 ? ($comparisonData['current']['documents_processed'] / $maxDocs) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $docsWidth }}%">
                                            {{ $comparisonData['current']['documents_processed'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="ms-2 text-muted">{{ round($comparisonData['average']['documents_processed'], 1) }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Complaints Handled</label>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    @php
                                        $maxComplaints = max($comparisonData['current']['complaints_received'], $comparisonData['average']['complaints_received']);
                                        $complaintsWidth = $maxComplaints > 0 ? ($comparisonData['current']['complaints_received'] / $maxComplaints) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $complaintsWidth }}%">
                                            {{ $comparisonData['current']['complaints_received'] }}
                                        </div>
                                    </div>
                                </div>
                                <span class="ms-2 text-muted">{{ round($comparisonData['average']['complaints_received'], 1) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <strong>Ranking:</strong> #{{ $comparisonData['rank']['current'] }} out of {{ $comparisonData['rank']['total'] }} barangays
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Service Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Document Completion Rate</span>
                            <span class="badge bg-primary">{{ $serviceMetrics['document_completion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: {{ $serviceMetrics['document_completion_rate'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Complaint Resolution Rate</span>
                            <span class="badge bg-success">{{ $serviceMetrics['complaint_resolution_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $serviceMetrics['complaint_resolution_rate'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Permit Approval Rate</span>
                            <span class="badge bg-info">{{ $serviceMetrics['permit_approval_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ $serviceMetrics['permit_approval_rate'] }}%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Average Processing Days</span>
                            <span class="badge bg-warning">{{ $serviceMetrics['avg_processing_days'] }} days</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ max(0, 100 - ($serviceMetrics['avg_processing_days'] * 10)) }}%"></div>
                        </div>
                    </div>

                    <div class="alert alert-success mt-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Performance Summary:</strong>
                        @if($serviceMetrics['document_completion_rate'] >= 80 && $serviceMetrics['complaint_resolution_rate'] >= 70)
                            Excellent performance! Keep up the good work.
                        @elseif($serviceMetrics['document_completion_rate'] >= 60)
                            Good performance with room for improvement.
                        @else
                            Focus needed on improving completion rates.
                        @endif
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
                    <h5 class="mb-0">6-Month Service Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="barangayTrendsChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('barangayTrendsChart').getContext('2d');
const trendsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyTrends, 'month')) !!},
        datasets: [
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
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Permits',
                data: {!! json_encode(array_column($monthlyTrends, 'permits')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
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
</style>
@endpush