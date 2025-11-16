@extends('layouts.barangay')

@section('title', 'Residents Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users"></i> Residents Report
                <small class="text-muted">{{ $barangay->name }}</small>
            </h1>
        </div>
        <div>
            <a href="{{ route('barangay.reports.index') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
            <a href="{{ route('barangay.reports.residents', ['print' => true]) }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-print me-2"></i>Print Residents
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Verified</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['verified']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Male</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['male']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-male fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Female</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['female']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">PWD</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pwd']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wheelchair fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Senior</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['senior']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Solo Parent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['solo_parent']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-child fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">4Ps</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['4ps']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.reports.residents') }}">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">All Gender</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Purok/Zone</label>
                        <select name="purok" class="form-control">
                            <option value="">All Purok</option>
                            @foreach($puroks as $purok)
                                <option value="{{ $purok }}" {{ request('purok') == $purok ? 'selected' : '' }}>
                                    {{ $purok }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Civil Status</label>
                        <select name="civil_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="single" {{ request('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ request('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                            <option value="widowed" {{ request('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ request('civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Age From</label>
                        <input type="number" name="age_from" class="form-control" value="{{ request('age_from') }}" placeholder="Min age">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Age To</label>
                        <input type="number" name="age_to" class="form-control" value="{{ request('age_to') }}" placeholder="Max age">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Special Classifications</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="is_pwd" value="1" id="is_pwd" {{ request('is_pwd') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pwd">PWD</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="is_senior_citizen" value="1" id="is_senior" {{ request('is_senior_citizen') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_senior">Senior Citizen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="is_solo_parent" value="1" id="is_solo" {{ request('is_solo_parent') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_solo">Solo Parent</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="is_4ps" value="1" id="is_4ps" {{ request('is_4ps') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_4ps">4Ps Beneficiary</label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('barangay.reports.residents') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Residents List ({{ $residents->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age/Gender</th>
                            <th>Purok</th>
                            <th>Civil Status</th>
                            <th>Occupation</th>
                            <th>Classifications</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($residents as $resident)
                        <tr>
                            <td>{{ $resident->user->full_name }}</td>
                            <td>
                                {{ $resident->user->age ?? 'N/A' }} / {{ ucfirst($resident->user->gender ?? 'N/A') }}
                            </td>
                            <td>{{ $resident->purok_zone }}</td>
                            <td>{{ ucfirst($resident->civil_status) }}</td>
                            <td>{{ $resident->occupation }}</td>
                            <td>
                                @if($resident->is_pwd)
                                    <span class="badge badge-primary">PWD</span>
                                @endif
                                @if($resident->is_senior_citizen)
                                    <span class="badge badge-success">Senior</span>
                                @endif
                                @if($resident->is_solo_parent)
                                    <span class="badge badge-warning">Solo Parent</span>
                                @endif
                                @if($resident->is_4ps_beneficiary)
                                    <span class="badge badge-danger">4Ps</span>
                                @endif
                            </td>
                            <td>
                                @if($resident->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No residents found matching the filters.</p>
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
</div>
@endsection