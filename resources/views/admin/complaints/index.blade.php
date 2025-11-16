{{-- FILE: resources/views/admin/complaints/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'All Complaints')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Complaints</h2>
    <div>
        <a href="{{ route('admin.complaints.export', request()->query()) }}" class="btn btn-success me-2">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ number_format($stats['total']) }}</h4>
                        <p class="card-text">Total Complaints</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ number_format($stats['received']) }}</h4>
                        <p class="card-text">Received</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-inbox fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ number_format($stats['in_process']) }}</h4>
                        <p class="card-text">In Process</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ number_format($stats['resolved']) }}</h4>
                        <p class="card-text">Resolved</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
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
                <input type="text" class="form-control" name="search" placeholder="Search complaints" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="barangay" class="form-control">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" {{ request('barangay') == $barangay->id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    <option value="in_process" {{ request('status') == 'in_process' ? 'selected' : '' }}>In Process</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    @foreach($complaintTypes as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="urgency" class="form-control">
                    <option value="">All Urgency</option>
                    <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('urgency') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-check">
                    <input type="date" class="form-control" name="date_from" placeholder="Date From" 
                           value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control" name="date_to" placeholder="Date To" 
                       value="{{ request('date_to') }}">
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <div>
                <button type="button" class="btn btn-outline-secondary" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square"></i> Select All
                </button>
                <button type="button" class="btn btn-outline-info" onclick="showBulkAssignModal()">
                    <i class="fas fa-user-plus"></i> Bulk Assign
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="showBulkStatusModal()">
                    <i class="fas fa-edit"></i> Bulk Status Update
                </button>
            </div>
            <div>
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
            </div>
        </div>

        <!-- Complaints Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Reference</th>
                        <th>Complainant</th>
                        <th>Barangay</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Date Filed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr>
                            <td><input type="checkbox" class="complaint-checkbox" value="{{ $complaint->id }}"></td>
                            <td>
                                <strong>{{ $complaint->reference_number }}</strong>
                            </td>
                            <td>
                                {{ $complaint->complainant_name }}
                                @if($complaint->user && $complaint->user->residentProfile)
                                    <br><small class="text-muted">{{ $complaint->user->email }}</small>
                                @endif
                            </td>
                            <td>{{ $complaint->barangay->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $complaint->complaintType->name }}</span>
                            </td>
                            <td>{{ Str::limit($complaint->subject, 30) }}</td>
                            <td>
                                <span class="badge {{ $complaint->urgency_badge }}">{{ $complaint->urgency_text }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $complaint->status_badge }}">{{ $complaint->status_text }}</span>
                            </td>
                            <td>
                                @if($complaint->assignedTo)
                                    {{ $complaint->assignedTo->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $complaint->created_at->format('M j, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No complaints found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $complaints->links() }}
    </div>
</div>

<!-- Bulk Assignment Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Assign Complaints</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.assign-bulk') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select class="form-control" id="assigned_to" name="assigned_to" required>
                            <option value="">Select Staff Member</option>
                            <!-- This will be populated via AJAX based on selected complaints -->
                        </select>
                    </div>
                    <input type="hidden" name="complaint_ids" id="bulkAssignIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Complaints</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Status Update Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Status Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.complaints.bulk-status-update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="received">Received</option>
                            <option value="in_process">In Process</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <input type="hidden" name="complaint_ids" id="bulkStatusIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.complaint-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function showBulkAssignModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one complaint.');
        return;
    }
    
    document.getElementById('bulkAssignIds').value = JSON.stringify(selectedIds);
    new bootstrap.Modal(document.getElementById('bulkAssignModal')).show();
}

function showBulkStatusModal() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one complaint.');
        return;
    }
    
    document.getElementById('bulkStatusIds').value = JSON.stringify(selectedIds);
    new bootstrap.Modal(document.getElementById('bulkStatusModal')).show();
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.complaint-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}
</script>
@endpush
@endsection