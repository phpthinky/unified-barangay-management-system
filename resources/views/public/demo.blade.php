{{-- resources/views/public/barangay/demo.blade.php --}}
@extends('layouts.public')

@section('title', 'Demo Accounts - Sablayan System')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary mb-3">Demo Accounts</h1>
                <p class="lead">Access the Sablayan Unified Barangay Management System with these pre-configured demo accounts to explore different user roles and functionalities.</p>
            </div>

            <!-- Quick Access Card -->
            <div class="card border-primary mb-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>Quick Access Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <strong><i class="fas fa-info-circle me-2"></i>Universal Password:</strong> 
                        All demo accounts use the password: <code class="fs-6">password</code>
                    </div>
                </div>
            </div>

            <!-- Municipality Accounts Section -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2 text-primary"></i>Municipality Accounts
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th width="25%">Role</th>
                                    <th width="50%">Email Address</th>
                                    <th width="25%">Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>ABC President</strong>
                                        <br><small class="text-muted">Municipality Level</small>
                                    </td>
                                    <td>
                                        <code>abc.president@sablayan.gov.ph</code>
                                        <br><small>Full administrative access</small>
                                    </td>
                                    <td>
                                        <code>password</code>
                                    </td>
                                </tr>
                                <!-- Add more municipality accounts here if needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Barangay Accounts Section -->
            <div class="card shadow-sm mb-5">
                <div class="card-header bg-light">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2 text-success"></i>Barangay Accounts
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Important Setup Note
                        </h6>
                        <p class="mb-2">The following barangay accounts need to be created using the ABC President account before handling resident services requests:</p>
                        <ul class="mb-0">
                            <li><strong>Barangay Captain Account</strong> - Primary barangay administrator</li>
                            <li><strong>Secretary Account</strong> - Documentation and record management</li>
                            <li><strong>Lupon Member Account</strong> - Conflict resolution and mediation</li>
                        </ul>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th width="25%">Role</th>
                                    <th width="50%">Setup Instructions</th>
                                    <th width="25%">Access Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Barangay Captain</strong>
                                    </td>
                                    <td>
                                        Create via User Management in ABC President account
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">Barangay Admin</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Secretary</strong>
                                    </td>
                                    <td>
                                        Create via User Management in ABC President account
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Document Manager</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Lupon Member</strong>
                                    </td>
                                    <td>
                                        Create via User Management in ABC President account
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Mediator</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-database me-2"></i>System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Account Status</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>All accounts are email verified</li>
                                <li><i class="fas fa-sync-alt text-primary me-2"></i>Data resets periodically</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Source Information</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-code me-2 text-info"></i>Generated from UserSeeder.php</li>
                                <li><i class="fas fa-calendar me-2 text-info"></i>Last updated: {{ date('F j, Y') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Tips -->
            <div class="mt-4 p-3 bg-light rounded">
                <h6><i class="fas fa-lightbulb me-2 text-warning"></i>Demo Tips</h6>
                <ul class="mb-0">
                    <li>Start with the ABC President account to explore full system capabilities</li>
                    <li>Use the ABC President account to create barangay-level accounts</li>
                    <li>Test different workflows by logging in with different role types</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection