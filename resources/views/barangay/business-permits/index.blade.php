@extends('layouts.barangay')

@section('title', 'Business Permits')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Business Permits</h1>
        <div>
            <a href="{{ route('barangay.permits.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export to Excel
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total</div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        </div>
                        <i class="fas fa-certificate fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Pending</div>
                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Approved</div>
                            <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Rejected</div>
                            <h4 class="mb-0">{{ $stats['rejected'] }}</h4>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Expired</div>
                            <h4 class="mb-0">{{ $stats['expired'] }}</h4>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.permits.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Business name, owner, tracking #">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Permit Type</label>
                        <select name="permit_type_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach($permitTypes as $type)
                                <option value="{{ $type->id }}" {{ request('permit_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('barangay.permits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Permits Table -->
    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">Business Permits List</h5>
        </div>
        <div class="card-body">
            @if($permits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tracking #</th>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Permit Type</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $permit->tracking_number }}</span>
                            </td>
                            <td>
                                <strong>{{ $permit->business_name }}</strong><br>
                                <small class="text-muted">{{ Str::limit($permit->business_address, 40) }}</small>
                            </td>
                            <td>{{ $permit->owner_name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $permit->businessPermitType->name }}</span>
                            </td>
                            <td>
                                @if($permit->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($permit->status == 'approved')
                                    @if($permit->expires_at && $permit->expires_at->isPast())
                                        <span class="badge bg-secondary">Expired</span>
                                    @else
                                        <span class="badge bg-success">Approved</span>
                                    @endif
                                @elseif($permit->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $permit->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('barangay.permits.show', $permit) }}" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($permit->status == 'pending' && auth()->user()->can('process-business-permits'))
                                    <a href="{{ route('barangay.permits.process', $permit) }}" 
                                       class="btn btn-sm btn-primary" title="Process">
                                        <i class="fas fa-tasks"></i>
                                    </a>
                                    @endif
                                    @if($permit->status == 'approved' && $permit->pdf_path)
                                    <a href="{{ route('barangay.permits.pdf', $permit) }}" 
                                       class="btn btn-sm btn-success" title="Download PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $permits->firstItem() ?? 0 }} to {{ $permits->lastItem() ?? 0 }} of {{ $permits->total() }} permits
                </div>
                <div>
                    {{ $permits->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                <p class="text-muted">No business permits found.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection