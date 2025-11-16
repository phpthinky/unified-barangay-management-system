@extends('layouts.abc')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Reports & Analytics</h2>
                    <p class="text-muted mb-0">Comprehensive reports across all barangays</p>
                </div>
                <div>
                    <a href="{{ route('abc.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Executive Summary Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title mb-0">Executive Summary</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted">
                        High-level overview of all barangay operations, performance metrics, and key insights.
                    </p>
                    <a href="{{ route('abc.reports.summary') }}" class="btn btn-primary w-100">
                        View Summary Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Barangay Comparison Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-buildings fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title mb-0">Barangay Reports</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted">
                        Detailed reports for individual barangays with comparative analysis.
                    </p>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#barangaySelectModal">
                        Select Barangay
                    </button>
                </div>
            </div>
        </div>

        <!-- Export Reports Card -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-3 p-3">
                            <i class="bi bi-download fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title mb-0">Export Reports</h5>
                        </div>
                    </div>
                    <p class="card-text text-muted">
                        Download reports in PDF, Excel, or CSV format for offline analysis.
                    </p>
                    <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#exportModal">
                        Export Reports
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">All Barangays Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1 text-primary">{{ $barangays->count() }}</h3>
                                <small class="text-muted">Active Barangays</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1 text-success">{{ $barangays->sum(function($b) { return $b->verifiedInhabitants()->count() + $b->verifiedResidents()->count(); }) }}</h3>
                                <small class="text-muted">Verified Residents</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1 text-info">{{ $barangays->sum(function($b) { return $b->documentRequests()->count(); }) }}</h3>
                                <small class="text-muted">Document Requests</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h3 class="mb-1 text-warning">{{ $barangays->sum(function($b) { return $b->complaints()->count(); }) }}</h3>
                                <small class="text-muted">Total Complaints</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barangay List -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Barangay Directory</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th>Total Residents</th>
                                    <th>Verified</th>
                                    <th>Documents</th>
                                    <th>Complaints</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangays as $barangay)
                                <tr>
                                    <td>
                                        <strong>{{ $barangay->name }}</strong>
                                    </td>
                                    <td>{{ $barangay->inhabitants()->count() + $barangay->residentProfiles()->count() }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $barangay->verifiedInhabitants()->count() + $barangay->verifiedResidents()->count() }}
                                        </span>
                                    </td>
                                    <td>{{ $barangay->documentRequests()->count() }}</td>
                                    <td>{{ $barangay->complaints()->count() }}</td>
                                    <td>
                                        <a href="{{ route('abc.reports.barangay', $barangay) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-text"></i> View Report
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No active barangays found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barangay Select Modal -->
<div class="modal fade" id="barangaySelectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Barangay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($barangays as $barangay)
                    <a href="{{ route('abc.reports.barangay', $barangay) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{ $barangay->name }}</h6>
                            <small>{{ $barangay->verifiedInhabitants()->count() + $barangay->verifiedResidents()->count() }} residents</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('abc.reports.export') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Type</label>
                        <select name="report_type" class="form-select" required>
                            <option value="summary">Executive Summary</option>
                            <option value="barangay">Barangay Reports</option>
                            <option value="performance">Performance Report</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Format</label>
                        <select name="format" class="form-select" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv" selected>CSV</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <select name="date_range" class="form-select" required>
                            <option value="last_week">Last Week</option>
                            <option value="last_month" selected>Last Month</option>
                            <option value="last_quarter">Last Quarter</option>
                            <option value="last_year">Last Year</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Barangays (for Barangay Reports)</label>
                        <select name="barangays[]" class="form-select" multiple size="5">
                            @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download"></i> Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush