@extends('layouts.barangay')

@section('title', 'Monthly Summary Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt"></i> Monthly Summary Report
                <small class="text-muted">{{ $barangay->name }}</small>
            </h1>
            <p class="text-muted mb-0">{{ $date->format('F Y') }}</p>
        </div>
        <div>
            <a href="{{ route('barangay.reports.index') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <a href="{{ route('barangay.reports.monthly-summary', array_merge(request()->all(), ['export' => 'pdf'])) }}" 
               class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Month Selector -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.reports.monthly-summary') }}" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Select Month</label>
                    <input type="month" name="month" class="form-control" value="{{ request('month', $date->format('Y-m')) }}" max="{{ date('Y-m') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> View Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue for {{ $date->format('F Y') }}
                            </div>
                            <div class="h2 mb-0 font-weight-bold text-success">₱{{ number_format($totalRevenue, 2) }}</div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Documents: ₱{{ number_format($documentsData['revenue'], 2) }} | 
                                    Permits: ₱{{ number_format($permitsData['revenue'], 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-peso-sign fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Sections -->
    <div class="row">
        <!-- Residents Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Residents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h3 class="text-primary mb-0">{{ number_format($residentsData['new_registrations']) }}</h3>
                                <small class="text-muted">New Registrations</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">{{ number_format($residentsData['verified']) }}</h3>
                                <small class="text-muted">Verified</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $residentsData['new_registrations'] }} new residents registered this month, 
                        {{ $residentsData['verified'] }} profiles verified.
                    </p>
                </div>
            </div>
        </div>

        <!-- Documents Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Document Requests</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-primary mb-0">{{ number_format($documentsData['total_requests']) }}</h3>
                                <small class="text-muted">Total Requests</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">{{ number_format($documentsData['approved']) }}</h3>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">₱{{ number_format($documentsData['revenue'], 2) }}</h3>
                                <small class="text-muted">Revenue</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $documentsData['total_requests'] }} document requests processed, 
                        {{ $documentsData['approved'] }} approved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Complaints Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Complaints & Disputes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-primary mb-0">{{ number_format($complaintsData['total_filed']) }}</h3>
                                <small class="text-muted">Filed</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">{{ number_format($complaintsData['resolved']) }}</h3>
                                <small class="text-muted">Resolved</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-info mb-0">{{ number_format($complaintsData['hearings_held']) }}</h3>
                                <small class="text-muted">Hearings</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $complaintsData['total_filed'] }} complaints filed, 
                        {{ $complaintsData['resolved'] }} resolved, 
                        {{ $complaintsData['hearings_held'] }} hearings conducted.
                    </p>
                </div>
            </div>
        </div>

        <!-- Business Permits Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Business Permits</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-primary mb-0">{{ number_format($permitsData['total_applications']) }}</h3>
                                <small class="text-muted">Applications</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">{{ number_format($permitsData['approved']) }}</h3>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-center">
                                <h3 class="text-success mb-0">₱{{ number_format($permitsData['revenue'], 2) }}</h3>
                                <small class="text-muted">Revenue</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $permitsData['total_applications'] }} permit applications, 
                        {{ $permitsData['approved'] }} approved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Overall Summary</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary mb-3">Highlights for {{ $date->format('F Y') }}</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Service Statistics</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>{{ number_format($residentsData['new_registrations']) }}</strong> new resident registrations
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>{{ number_format($documentsData['total_requests']) }}</strong> document requests processed
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>{{ number_format($complaintsData['total_filed']) }}</strong> complaints handled
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>{{ number_format($permitsData['total_applications']) }}</strong> business permit applications
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted">Financial Summary</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Document Fees</td>
                                            <td class="text-end"><strong>₱{{ number_format($documentsData['revenue'], 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Business Permit Fees</td>
                                            <td class="text-end"><strong>₱{{ number_format($permitsData['revenue'], 2) }}</strong></td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>Total Revenue</strong></td>
                                            <td class="text-end"><strong>₱{{ number_format($totalRevenue, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Key Insights</h6>
                        <ul class="mb-0">
                            @if($complaintsData['resolved'] > 0 && $complaintsData['total_filed'] > 0)
                            <li>Resolution rate: <strong>{{ number_format(($complaintsData['resolved'] / $complaintsData['total_filed']) * 100, 1) }}%</strong> of complaints resolved</li>
                            @endif
                            @if($documentsData['approved'] > 0 && $documentsData['total_requests'] > 0)
                            <li>Approval rate: <strong>{{ number_format(($documentsData['approved'] / $documentsData['total_requests']) * 100, 1) }}%</strong> of documents approved</li>
                            @endif
                            @if($permitsData['approved'] > 0 && $permitsData['total_applications'] > 0)
                            <li>Business permit approval rate: <strong>{{ number_format(($permitsData['approved'] / $permitsData['total_applications']) * 100, 1) }}%</strong></li>
                            @endif
                            <li>Total hearings conducted: <strong>{{ number_format($complaintsData['hearings_held']) }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection