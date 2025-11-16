{{-- FILE: resources/views/barangay/documents/index.blade.php --}}
@extends('layouts.barangay')

@section('title', 'Document Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Document Requests</h2>
        <p class="text-muted mb-0">Manage document requests for {{ auth()->user()->barangay->name }}</p>
    </div>
    <div>
        <!-- Export functionality not defined in routes yet -->
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total'] }}</h4>
                        <p class="card-text">Total</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['pending'] }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['processing'] }}</h4>
                        <p class="card-text">Processing</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['approved'] }}</h4>
                        <p class="card-text">Approved</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{-- $stats['rejected'] --}}</h4>
                        <p class="card-text">Rejected</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search by tracking # or name" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="document_type" class="form-control">
                    <option value="">All Document Types</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('document_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('barangay.documents.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <!-- Requests Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tracking #</th>
                        <th>Resident</th>
                        <th>Document Type</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Processing Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr class="{{ $request->isExpired() ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $request->tracking_number }}</strong>
                                @if($request->isExpired())
                                    <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Expired</small>
                                @endif
                            </td>
                            <td>
                                {{ $request->user->full_name ?? $request->user->name }}
                                <br><small class="text-muted">{{ $request->user->email }}</small>
                            </td>
                            <td>
                                {{ $request->documentType->name }}
                                <br><small class="text-muted">₱{{ number_format($request->amount_paid, 2) }}</small>
                            </td>
                            <td>{{ Str::limit($request->purpose, 30) }}</td>
                            <td>
                                <span class="badge {{ $request->status_badge['class'] }}">
                                    {{ $request->status_badge['text'] }}
                                </span>
                            </td>
                            <td>{{ $request->submitted_at->format('M j, Y') }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    {{ $request->submitted_at->diffForHumans() }}
                                @elseif($request->status == 'processing')
                                    Processing for {{ $request->processed_at ? $request->processed_at->diffForHumans() : 'N/A' }}
                                @elseif($request->status == 'approved')
                                    <span class="text-success">{{ $request->processing_days }} days</span>
                                @else
                                    -
                                @endif
                            </td>
                            {{-- In resources/views/barangay/documents/index.blade.php --}}
<td>
    <div class="btn-group" role="group">
        <a href="{{ route('barangay.documents.show', $request) }}" 
           class="btn btn-sm btn-outline-info">
            <i class="fas fa-eye"></i>
        </a>
        
        @if($request->status == 'pending')
            <form action="{{ route('barangay.documents.process', $request) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-play"></i>
                </button>
            </form>
        @endif

        @if(in_array($request->status, ['pending', 'processing']))
            <form action="{{ route('barangay.documents.approve', $request) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-check"></i>
                </button>
            </form>
            <button type="button" class="btn btn-sm btn-outline-danger" 
                    onclick="showRejectModal({{ $request->id }})">
                <i class="fas fa-times"></i>
            </button>
        @endif

        {{-- ADD PRINT BUTTON FOR APPROVED DOCUMENTS --}}
        @if($request->status == 'approved')
            <a href="{{ route('barangay.documents.print', $request) }}" 
               target="_blank" class="btn btn-sm btn-outline-primary" 
               title="Print Document">
                <i class="fas fa-print"></i>
            </a>
        @endif
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5>No document requests found</h5>
                                <p class="text-muted">There are no document requests matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_reason" name="rejection_reason" rows="4" 
                                  placeholder="Please provide a clear reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.badge {
    color: white !important;
    font-weight: 600;
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge-info {
    background-color: #0dcaf0 !important;
}

.badge-success {
    background-color: #198754 !important;
}

.badge-danger {
    background-color: #dc3545 !important;
}

.badge-primary {
    background-color: #0d6efd !important;
}
</style>
@endpush

@push('scripts')
<script>
function showRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = `/barangay/documents/${requestId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
@endsection