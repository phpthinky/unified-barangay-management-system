{{-- FILE: resources/views/resident/documents/index.blade.php --}}
@extends('layouts.resident')

@section('title', 'My Document Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>My Document Requests</h2>
        <p class="text-muted mb-0">Track and manage your document requests</p>
    </div>
    <div>
        <a href="{{ route('resident.documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Request
        </a>
    </div>
</div>

<!-- RBI Eligibility Check -->
@php
    $inhabitant = \App\Models\BarangayInhabitant::where('user_id', auth()->id())
                                                 ->where('barangay_id', auth()->user()->barangay_id)
                                                 ->first();
@endphp

@if(!$inhabitant)
<div class="alert alert-warning">
    <h5><i class="fas fa-exclamation-triangle"></i> Not Registered in RBI</h5>
    <p>You are not yet registered in the <strong>Registry of Barangay Inhabitants (RBI)</strong>.</p>
    <p class="mb-0">
        <strong>What to do:</strong> Please visit the barangay hall to register first before requesting documents. 
        Bring a valid ID and proof of residency.
    </p>
</div>
@else
    <!-- Show eligibility status for clearance -->
    @php
        $eligibility = $inhabitant->checkClearanceEligibility();
        $pendingComplaints = $inhabitant->getPendingComplaintsCount();
    @endphp

    @if(!$eligibility['eligible'])
    <div class="alert alert-danger">
        <h5><i class="fas fa-times-circle"></i> Not Eligible for Barangay Clearance</h5>
        <p>You currently cannot request a barangay clearance due to the following:</p>
        <ul class="mb-2">
            @foreach($eligibility['reasons'] as $reason)
                <li>{{ $reason }}</li>
            @endforeach
        </ul>
        <hr>
        <p class="mb-0">
            <strong>What to do:</strong> Please resolve the issues above before requesting a clearance. 
            You may still request other non-clearance documents.
        </p>
    </div>
    @endif

    @if($pendingComplaints > 0)
    <div class="alert alert-warning">
        <h5><i class="fas fa-gavel"></i> Pending Complaint Cases</h5>
        <p>You have <strong>{{ $pendingComplaints }}</strong> pending complaint case(s) filed against you.</p>
        <p class="mb-0">
            <strong>Note:</strong> These must be resolved before you can request a barangay clearance. 
            Other documents may still be available.
        </p>
    </div>
    @endif
@endif

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
                        <h4 class="card-title">{{ $stats['rejected'] }}</h4>
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
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search by tracking number" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('resident.documents.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <!-- Requests List -->
        <div class="row">
            @forelse($requests as $request)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $request->tracking_number }}</h6>
                            <span class="badge {{ $request->status_badge['class'] }}">{{ $request->status_badge['text'] }}</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">{{ $request->documentType->name }}</h6>
                            <p class="text-muted mb-2">
                                <small>
                                    <i class="fas fa-calendar"></i> {{ $request->submitted_at->format('M j, Y') }} |
                                    <i class="fas fa-copy"></i> {{ $request->copies_requested }} {{ Str::plural('copy', $request->copies_requested) }}
                                </small>
                            </p>
                            <p class="card-text">
                                <strong>Purpose:</strong> {{ Str::limit($request->purpose, 80) }}
                            </p>
                            @if($request->amount_paid > 0)
                                <p class="text-success mb-2">
                                    <small><i class="fas fa-peso-sign"></i> {{ number_format($request->amount_paid, 2) }} paid</small>
                                </p>
                            @endif
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    @if($request->status == 'approved')
                                        Ready for download
                                    @elseif($request->status == 'processing')
                                        Being processed
                                    @elseif($request->status == 'pending')
                                        Waiting for processing
                                    @else
                                        {{ ucfirst($request->status) }}
                                    @endif
                                </small>
                                <a href="{{ route('resident.documents.show', $request->id) }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @if($request->status == 'approved' && $request->generated_file)
                        <div class="card-footer">
                            <div class="d-grid">
                                <a href="{{ route('resident.documents.download', $request->id) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Download Document
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h4>No Document Requests</h4>
                        <p class="text-muted">You haven't submitted any document requests yet.</p>
                        <a href="{{ route('resident.documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Request Your First Document
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{ $requests->links() }}
    </div>
</div>
@endsection