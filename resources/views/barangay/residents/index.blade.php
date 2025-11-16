{{-- FILE: resources/views/barangay/residents/index.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Online Account Management - ' . auth()->user()->barangay->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> Online Account Management
        </h1>
        <div class="d-flex gap-2">
            @if($stats['pending'] > 0)
            <a href="{{ route('barangay.residents.pending') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-clock"></i> Pending: {{ $stats['pending'] }}
            </a>
            @endif
            <a href="{{ route('barangay.residents.export.excel', request()->query()) }}" 
               class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Accounts</h6>
                            <h4 class="mb-0">{{ number_format($stats['total']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Email Verified</h6>
                            <h4 class="mb-0">{{ number_format($stats['email_verified']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-envelope fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">RBI Linked</h6>
                            <h4 class="mb-0">{{ number_format($stats['rbi_linked']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-link fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending Review</h6>
                            <h4 class="mb-0">{{ number_format($stats['pending']) }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.residents.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">RBI Status</label>
                    <select name="rbi_status" class="form-control">
                        <option value="">All RBI Status</option>
                        <option value="linked" {{ request('rbi_status') == 'linked' ? 'selected' : '' }}>Linked</option>
                        <option value="not_linked" {{ request('rbi_status') == 'not_linked' ? 'selected' : '' }}>Not Linked</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Email Status</label>
                    <select name="email_status" class="form-control">
                        <option value="">All Email Status</option>
                        <option value="verified" {{ request('email_status') == 'verified' ? 'selected' : '' }}>Email Verified</option>
                        <option value="pending" {{ request('email_status') == 'pending' ? 'selected' : '' }}>Email Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Residential Status</label>
                    <select name="residential_status" class="form-control">
                        <option value="">All Residential Status</option>
                        <option value="permanent" {{ request('residential_status') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="temporary" {{ request('residential_status') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                        <option value="transient" {{ request('residential_status') == 'transient' ? 'selected' : '' }}>Transient</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Name, email..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Online Accounts Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Resident Accounts ({{ $residents->total() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                   <thead class="table-light">
    <tr>
        <th>Account Info</th>
        <th>Profile</th>
        <th>Email</th>
        <th>RBI</th>
        <th>Residency</th>
        <th>Eligibility</th>
        <th>Registered</th>
        <th>Actions</th>
    </tr>
</thead>
                    <tbody>
                        @forelse($residents as $resident)
                        <tr>
                            <!-- Account Info -->
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($resident->user->profile_photo)
                                        <img class="rounded-circle me-2" src="{{ asset('uploads/photos/' . $resident->user->profile_photo) }}" 
                                             alt="Photo" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ substr($resident->user->first_name, 0, 1) }}{{ substr($resident->user->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold">
                                            {{ $resident->user->first_name }} {{ $resident->user->last_name }}
                                        </div>
                                        <div class="text-muted small">{{ $resident->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                          <td>
    @if($resident->is_verified)
        <span class="badge bg-success">Approved</span>
    @else
        <span class="badge bg-warning">Pending</span>
    @endif
</td>

<td>
    @if($resident->user->email_verified_at)
        <span class="badge bg-success">Verified</span>
    @else
        <span class="badge bg-warning">Pending</span>
    @endif
</td>

<td>
    @if($resident->rbi_inhabitant_id)
        <span class="badge bg-success">Linked</span>
    @else
        <span class="badge bg-danger">Not Linked</span>
    @endif
</td>

<td>
    <small>
        {{ ucfirst($resident->residency_type) }}<br>
        @if($resident->residency_since)
            {{ $resident->residency_months }} months
        @endif
    </small>
</td>

<td>
    @if($resident->is_verified && $resident->rbi_inhabitant_id && $resident->meetsResidencyRequirement())
        <span class="badge bg-success">Eligible</span>
    @else
        <span class="badge bg-warning">Limited</span>
    @endif
</td>

                            <!-- Registered -->
                            <td>
                                {{ $resident->created_at->format('m/d/Y') }}
                            </td>

                            <!-- Actions -->
                            <td>
                                <a href="{{ route('barangay.residents.show', $resident) }}" 
                                   class="btn btn-info btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No online accounts found.</p>
                                    @if(request()->hasAny(['rbi_status', 'email_status', 'residential_status', 'search']))
                                        <a href="{{ route('barangay.residents.index') }}" class="btn btn-primary btn-sm">
                                            Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $residents->firstItem() ?? 0 }} to {{ $residents->lastItem() ?? 0 }} of {{ $residents->total() }}
                </div>
                {{ $residents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit filters when changed
    $('select[name="rbi_status"], select[name="email_status"], select[name="residential_status"]').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush