@extends('layouts.barangay')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar"></i> Reports & Analytics
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Residents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_residents']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Document Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_documents']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Complaints
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_complaints']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Business Permits
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_permits']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Reports -->
    <div class="row">
        <!-- Residents Report -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Residents Report
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Generate comprehensive reports on resident demographics, verification status, special classifications, and more.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Demographics breakdown</li>
                        <li><i class="fas fa-check text-success me-2"></i>Verification status</li>
                        <li><i class="fas fa-check text-success me-2"></i>Special classifications (PWD, Senior, etc.)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Purok/Zone distribution</li>
                    </ul>
                    <a href="{{ route('barangay.reports.residents') }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Documents Report -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents Report
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Track document requests, processing times, approval rates, and revenue generated.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Request status tracking</li>
                        <li><i class="fas fa-check text-success me-2"></i>Processing time analytics</li>
                        <li><i class="fas fa-check text-success me-2"></i>Document type breakdown</li>
                        <li><i class="fas fa-check text-success me-2"></i>Revenue summary</li>
                    </ul>
                    <a href="{{ route('barangay.reports.documents') }}" class="btn btn-success">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Complaints Report -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Complaints Report
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Analyze complaint trends, resolution rates, hearing schedules, and case outcomes.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Status distribution</li>
                        <li><i class="fas fa-check text-success me-2"></i>Priority levels</li>
                        <li><i class="fas fa-check text-success me-2"></i>Resolution analytics</li>
                        <li><i class="fas fa-check text-success me-2"></i>Complaint type breakdown</li>
                    </ul>
                    <a href="{{ route('barangay.reports.complaints') }}" class="btn btn-warning">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Business Permits Report -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>Business Permits Report
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Monitor business permit applications, approvals, renewals, and revenue collection.
                    </p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Application status</li>
                        <li><i class="fas fa-check text-success me-2"></i>Business type distribution</li>
                        <li><i class="fas fa-check text-success me-2"></i>Approval rates</li>
                        <li><i class="fas fa-check text-success me-2"></i>Fee collection summary</li>
                    </ul>
                    <a href="{{ route('barangay.reports.permits') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Summary Report
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-muted">
                                Comprehensive monthly overview of all barangay activities, services, and revenue.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>All services summary</li>
                                <li><i class="fas fa-check text-success me-2"></i>Month-over-month comparison</li>
                                <li><i class="fas fa-check text-success me-2"></i>Revenue breakdown</li>
                                <li><i class="fas fa-check text-success me-2"></i>Activity highlights</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('barangay.reports.monthly-summary') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-chart-line me-2"></i>View Monthly Summary
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Quick Export</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Generate instant reports for the current month:</p>
                    <div class="btn-group" role="group">
                        <a href="{{ route('barangay.reports.residents', ['print' => 'true']) }}" class="btn btn-outline-primary">
                            <i class="fas fa-print me-2"></i>Residents 
                        </a>
                        <a href="{{ route('barangay.reports.documents', ['print' => 'true']) }}" class="btn btn-outline-success">
                            <i class="fas fa-print me-2"></i>Documents 
                        </a>
                        <a href="{{ route('barangay.reports.complaints', ['print' => 'true']) }}" class="btn btn-outline-warning">
                            <i class="fas fa-print me-2"></i>Complaints 
                        </a>
                        <a href="{{ route('barangay.reports.permits', ['print' => 'true']) }}" class="btn btn-outline-info">
                            <i class="fas fa-print me-2"></i>Permits 
                        </a>
                        <a href="{{ route('barangay.reports.monthly-summary', ['print' => 'true']) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Monthly Summary 
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection