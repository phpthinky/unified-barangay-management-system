@extends('layouts.barangay')

@section('title', 'Pending Resident Verifications - ' . auth()->user()->barangay->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clock"></i> Pending Resident Verifications
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
        <div class="d-flex">
            <a href="{{ route('barangay.residents.index') }}" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-list"></i> All Residents
            </a>
        </div>
    </div>

    <!-- Pending Statistics -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Verifications in {{ $barangay->name }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($pendingResidents->total()) }} residents awaiting verification
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.residents.pending') }}" class="row">
                <div class="col-md-8 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Search by name or email..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @if(request('search'))
                <div class="mt-2">
                    <a href="{{ route('barangay.residents.pending') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear Search
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Residents Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Pending Residents 
                <span class="text-muted">({{ $pendingResidents->total() }} total)</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Resident</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th>Registered</th>
                            <th>Waiting Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingResidents as $resident)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($resident->user->profile_photo)
                                        <img class="rounded-circle mr-2" src="{{ asset('uploads/photos/' . $resident->user->profile_photo) }}" 
                                             alt="Photo" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mr-2" 
                                             style="width: 40px; height: 40px;">
                                            {{ substr($resident->user->first_name, 0, 1) }}{{ substr($resident->user->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $resident->user->first_name }} {{ $resident->user->last_name }}
                                        </div>
                                        <div class="text-muted small">{{ $resident->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($resident->user->phone_number)
                                    <div><i class="fas fa-phone text-muted"></i> {{ $resident->user->phone_number }}</div>
                                @endif
                                @if($resident->user->birth_date)
                                    <small class="text-muted"><i class="fas fa-birthday-cake"></i> Age: {{ $resident->age }}</small>
                                @endif
                            </td>
                            <td>
                                @if($resident->purok_zone)
                                    <div><strong>Purok {{ $resident->purok_zone }}</strong></div>
                                @endif
                                <small>{{ $resident->user->address }}</small>
                            </td>
                            <td>
                                <div>{{ $resident->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $resident->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                @php
                                    $waitingDays = $resident->created_at->diffInDays(now());
                                    $waitingHours = $resident->created_at->diffInHours(now());
                                @endphp
                                
                                @if($waitingDays > 0)
                                    <span class="badge badge-{{ $waitingDays > 7 ? 'danger' : ($waitingDays > 3 ? 'warning' : 'secondary') }}">
                                        {{ $waitingDays }} {{ Str::plural('day', $waitingDays) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary">{{ $waitingHours }} {{ Str::plural('hour', $waitingHours) }}</span>
                                @endif
                                
                                <br><small class="text-muted">{{ $resident->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('barangay.residents.show', $resident) }}" 
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-success btn-sm" 
                                            data-toggle="modal" data-target="#verifyModal{{ $resident->id }}"
                                            title="Verify Resident">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Individual Verify Modal -->
                        <div class="modal fade" id="verifyModal{{ $resident->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Verify Resident</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('barangay.residents.verify', $resident) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <p><strong>Resident:</strong> {{ $resident->user->first_name }} {{ $resident->user->last_name }}</p>
                                            <p><strong>Address:</strong> {{ $resident->full_address }}</p>
                                            <p><strong>Waiting Time:</strong> {{ $resident->created_at->diffForHumans() }}</p>
                                            
                                            @if($resident->uploaded_files && count($resident->uploaded_files) > 0)
                                                <div class="mb-3">
                                                    <strong>Uploaded Documents:</strong>
                                                    <div class="mt-2">
                                                        @foreach($resident->id_document_urls as $index => $url)
                                                            <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm mr-1 mb-1">
                                                                <i class="fas fa-file-image"></i> Document {{ $index + 1 }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="form-group">
                                                <label for="notes{{ $resident->id }}">Verification Notes (Optional)</label>
                                                <textarea class="form-control" name="notes" 
                                                          id="notes{{ $resident->id }}" rows="3" 
                                                          placeholder="Any notes about the verification..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Verify Resident</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <h5>No Pending Verifications</h5>
                                    <p>All residents in {{ $barangay->name }} have been verified.</p>
                                    @if(request('search'))
                                        <a href="{{ route('barangay.residents.pending') }}" class="btn btn-primary">
                                            View All Pending
                                        </a>
                                    @else
                                        <a href="{{ route('barangay.residents.index') }}" class="btn btn-primary">
                                            View All Residents
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
            @if($pendingResidents->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $pendingResidents->firstItem() ?? 0 }} to {{ $pendingResidents->lastItem() ?? 0 }} 
                        of {{ $pendingResidents->total() }} results
                    </div>
                    {{ $pendingResidents->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Verification Guidelines</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">Before Verifying:</h6>
                    <ul class="text-muted small">
                        <li>Check uploaded ID documents</li>
                        <li>Verify resident actually lives in the barangay</li>
                        <li>Confirm personal information accuracy</li>
                        <li>Validate address and purok/zone</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info">Priority Verification:</h6>
                    <ul class="text-muted small">
                        <li><span class="badge badge-danger">7+ days</span> - High priority</li>
                        <li><span class="badge badge-warning">3-7 days</span> - Medium priority</li>
                        <li><span class="badge badge-secondary">&lt; 3 days</span> - Normal priority</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Nothing specific needed for this page
});
</script>
@endpush