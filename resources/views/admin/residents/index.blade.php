@extends('layouts.admin')

@section('title', 'Resident Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> Resident Management
        </h1>
        <div class="d-flex">
            <a href="{{ route('admin.residents.export.excel', request()->query()) }}" 
               class="btn btn-success btn-sm mr-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
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
                                {{ number_format($stats['total']) }}
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
                                Verified Residents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['verified']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Pending Verification
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['pending']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Verification Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demographics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Senior Citizens
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['senior_citizens']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                PWD
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['pwd']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wheelchair fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Solo Parents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['solo_parents']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                4Ps Beneficiaries
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['4ps_beneficiaries']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.residents.index') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="barangay_id" class="form-label">Barangay</label>
                    <select name="barangay_id" id="barangay_id" class="form-control">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay->id }}" 
                                    {{ request('barangay_id') == $barangay->id ? 'selected' : '' }}>
                                {{ $barangay->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>
                            Verified
                        </option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="classification" class="form-label">Classification</label>
                    <select name="classification" id="classification" class="form-control">
                        <option value="">All Classifications</option>
                        <option value="senior" {{ request('classification') == 'senior' ? 'selected' : '' }}>
                            Senior Citizen
                        </option>
                        <option value="pwd" {{ request('classification') == 'pwd' ? 'selected' : '' }}>
                            PWD
                        </option>
                        <option value="solo_parent" {{ request('classification') == 'solo_parent' ? 'selected' : '' }}>
                            Solo Parent
                        </option>
                        <option value="4ps" {{ request('classification') == '4ps' ? 'selected' : '' }}>
                            4Ps Beneficiary
                        </option>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label for="purok" class="form-label">Purok/Zone</label>
                    <select name="purok" id="purok" class="form-control">
                        <option value="">All Puroks</option>
                        @foreach($puroks as $purok)
                            <option value="{{ $purok }}" {{ request('purok') == $purok ? 'selected' : '' }}>
                                {{ $purok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Name, email, barangay..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            @if(request()->hasAny(['barangay_id', 'status', 'classification', 'purok', 'search']))
                <div class="mt-2">
                    <a href="{{ route('admin.residents.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Residents Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Residents List 
                <span class="text-muted">({{ $residents->total() }} total)</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Barangay</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Classifications</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($residents as $resident)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($resident->user->profile_photo)
                                        <img class="rounded-circle mr-2" src="{{ asset('uploads/photos/' . $resident->user->profile_photo) }}" 
                                             alt="Photo" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
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
                                <span class="badge badge-info">{{ $resident->barangay->name }}</span>
                                @if($resident->purok_zone)
                                    <br><small class="text-muted">Purok {{ $resident->purok_zone }}</small>
                                @endif
                            </td>
                            <td>
                                @if($resident->user->phone_number)
                                    <div>{{ $resident->user->phone_number }}</div>
                                @endif
                                @if($resident->user->birth_date)
                                    <small class="text-muted">Age: {{ $resident->age }}</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ $resident->full_address }}</small>
                            </td>
                            <td>
                                @if($resident->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                    @if($resident->verified_at)
                                        <br><small class="text-muted">{{ $resident->verified_at->format('M d, Y') }}</small>
                                    @endif
                                    @if($resident->verifier)
                                        <br><small class="text-muted">by {{ $resident->verifier->first_name }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($resident->hasSpecialClassifications())
                                    @foreach($resident->special_classifications as $classification)
                                        <span class="badge badge-secondary badge-sm">{{ $classification }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $resident->created_at->format('M d, Y') }}</small>
                                <br><small class="text-muted">{{ $resident->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.residents.show', $resident) }}" 
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$resident->is_verified)
                                        <button type="button" class="btn btn-success btn-sm" 
                                                data-toggle="modal" data-target="#verifyModal{{ $resident->id }}"
                                                title="Admin Override Verify">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                data-toggle="modal" data-target="#unverifyModal{{ $resident->id }}"
                                                title="Admin Override Unverify">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Verify Modal -->
                        @if(!$resident->is_verified)
                        <div class="modal fade" id="verifyModal{{ $resident->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Admin Override - Verify Resident</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.residents.verify', $resident) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-warning">
                                                <strong>Administrative Override:</strong> You are about to verify this resident as Municipality Admin. This action will be logged.
                                            </div>
                                            <p><strong>Resident:</strong> {{ $resident->user->first_name }} {{ $resident->user->last_name }}</p>
                                            <p><strong>Barangay:</strong> {{ $resident->barangay->name }}</p>
                                            
                                            <div class="form-group">
                                                <label for="override_reason{{ $resident->id }}">Override Reason (Required)</label>
                                                <textarea class="form-control" name="override_reason" 
                                                          id="override_reason{{ $resident->id }}" rows="3" 
                                                          placeholder="Explain why you are overriding the normal verification process..." required></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="notes{{ $resident->id }}">Additional Notes (Optional)</label>
                                                <textarea class="form-control" name="notes" 
                                                          id="notes{{ $resident->id }}" rows="2" 
                                                          placeholder="Any additional verification notes..."></textarea>
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
                        @endif

                        <!-- Unverify Modal -->
                        @if($resident->is_verified)
                        <div class="modal fade" id="unverifyModal{{ $resident->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Admin Override - Unverify Resident</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.residents.unverify', $resident) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body">
                                            <div class="alert alert-danger">
                                                <strong>Administrative Override:</strong> You are about to remove verification from this resident. This action will be logged.
                                            </div>
                                            <p><strong>Resident:</strong> {{ $resident->user->first_name }} {{ $resident->user->last_name }}</p>
                                            <p><strong>Barangay:</strong> {{ $resident->barangay->name }}</p>
                                            
                                            <div class="form-group">
                                                <label for="reason{{ $resident->id }}">Reason for Unverification (Required)</label>
                                                <textarea class="form-control" name="reason" 
                                                          id="reason{{ $resident->id }}" rows="3" 
                                                          placeholder="Explain why verification is being removed..." required></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="override_reason_unverify{{ $resident->id }}">Override Reason (Required)</label>
                                                <textarea class="form-control" name="override_reason" 
                                                          id="override_reason_unverify{{ $resident->id }}" rows="2" 
                                                          placeholder="Explain why you are overriding the normal process..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Remove Verification</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No residents found matching your criteria.</p>
                                    @if(request()->hasAny(['barangay_id', 'status', 'classification', 'purok', 'search']))
                                        <a href="{{ route('admin.residents.index') }}" class="btn btn-primary">
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $residents->firstItem() ?? 0 }} to {{ $residents->lastItem() ?? 0 }} 
                    of {{ $residents->total() }} results
                </div>
                {{ $residents->links() }}
            </div>
        </div>
    </div>

    <!-- Barangay Statistics -->
    @if($barangayStats->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Residents by Barangay</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Barangay</th>
                            <th>Total Residents</th>
                            <th>Verified</th>
                            <th>Pending</th>
                            <th>Verification Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangayStats as $barangay)
                        <tr>
                            <td>
                                <a href="{{ route('admin.residents.index', ['barangay_id' => $barangay->id]) }}" 
                                   class="text-decoration-none">
                                    {{ $barangay->name }}
                                </a>
                            </td>
                            <td>{{ number_format($barangay->resident_profiles_count) }}</td>
                            <td>
                                <span class="text-success">{{ number_format($barangay->verified_count) }}</span>
                            </td>
                            <td>
                                <span class="text-warning">{{ number_format($barangay->pending_count) }}</span>
                            </td>
                            <td>
                                @if($barangay->resident_profiles_count > 0)
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $rate = round(($barangay->verified_count / $barangay->resident_profiles_count) * 100, 1);
                                        @endphp
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $rate }}%">
                                            {{ $rate }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#barangay_id, #status, #classification, #purok').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush