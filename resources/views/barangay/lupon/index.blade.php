@extends('layouts.barangay')

@section('title', 'Lupon Members')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gavel"></i> Lupon Tagapamayapa Members
            <small class="text-muted">{{ $barangay->name }}</small>
        </h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Lupon Members
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

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['active']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Inactive Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['inactive']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lupon Members List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Lupon Members List
                <span class="text-muted">({{ $luponMembers->total() }} total)</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Assigned Complaints</th>
                            <th>Presiding Hearings</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($luponMembers as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($member->profile_photo)
                                        <img class="rounded-circle mr-2" src="{{ asset('uploads/photos/' . $member->profile_photo) }}" 
                                             alt="Photo" width="40" height="40">
                                    @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                             style="width: 40px; height: 40px;">
                                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </div>
                                        <small class="text-muted">{{ $member->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($member->contact_number)
                                    <i class="fas fa-phone text-primary"></i> {{ $member->contact_number }}
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong>{{ $member->assigned_complaints_count }}</strong>
                                @if($member->active_complaints_count > 0)
                                    <br><small class="text-warning">({{ $member->active_complaints_count }} active)</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong>{{ $member->presiding_hearings_count }}</strong>
                                @if($member->completed_hearings_count > 0)
                                    <br><small class="text-success">({{ $member->completed_hearings_count }} completed)</small>
                                @endif
                            </td>
                            <td>
                                @if($member->last_login_at)
                                    {{ $member->last_login_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $member->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('barangay.lupon.show', $member) }}" 
                                   class="btn btn-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No lupon members found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $luponMembers->firstItem() ?? 0 }} to {{ $luponMembers->lastItem() ?? 0 }} 
                    of {{ $luponMembers->total() }} results
                </div>
                {{ $luponMembers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection