{{-- FILE: resources/views/admin/requests/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Document Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Requests</h2>
    <a href="{{ route('admin.requests.export', request()->query()) }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export to Excel
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search requests" 
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
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="document_type" class="form-control">
                    <option value="">All Types</option>
                    @foreach($documentTypes as $docType)
                        <option value="{{ $docType->id }}" {{ request('document_type') == $docType->id ? 'selected' : '' }}>
                            {{ $docType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <!-- Requests Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Reference #</th>
                        <th>Resident</th>
                        <th>Barangay</th>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>
                                <strong>{{ $request->reference_number }}</strong>
                            </td>
                            <td>
                                {{ $request->user->name }}
                                <br><small class="text-muted">{{ $request->user->email }}</small>
                            </td>
                            <td>{{ $request->barangay->name }}</td>
                            <td>{{ $request->documentType->name }}</td>
                            <td>
                                <span class="badge {{ $request->status_badge }}">
                                    {{ $request->status_text }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M j, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.requests.show', $request) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p>No document requests found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
