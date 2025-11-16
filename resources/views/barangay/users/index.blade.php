@extends('layouts.barangay')

@section('title', 'Staff Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Staff Management</h1>
            <p class="text-muted mb-0">Manage {{ $barangay->name }} staff and officials</p>
        </div>
        @can('create-users')
        <a href="{{ route('barangay.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Staff
        </a>
        @endcan
    </div>

    <!-- Expiring Terms Alert -->
    @php
        $expiringUsers = App\Models\User::where('barangay_id', $barangay->id)
            ->whereNotNull('term_end')
            ->where('term_end', '<=', now()->addDays(30))
            ->where('term_end', '>=', now())
            ->where('is_active', true)
            ->where('is_archived', false)
            ->with('roles')
            ->get();
        
        $expiredUsers = App\Models\User::where('barangay_id', $barangay->id)
            ->whereNotNull('term_end')
            ->where('term_end', '<', now())
            ->where('is_active', true)
            ->where('is_archived', false)
            ->with('roles')
            ->get();
    @endphp

    @if($expiredUsers->count() > 0)
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div class="flex-grow-1">
            <strong>Terms Expired!</strong>
            <p class="mb-0">{{ $expiredUsers->count() }} staff member(s) have expired terms and should be archived or renewed:</p>
            <ul class="mb-0 mt-2">
                @foreach($expiredUsers as $expiredUser)
                    <li>
                        <strong>{{ $expiredUser->first_name }} {{ $expiredUser->last_name }}</strong> 
                        ({{ ucwords(str_replace('-', ' ', $expiredUser->getRoleNames()->first())) }}) - 
                        Expired {{ $expiredUser->term_end->diffForHumans() }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if($expiringUsers->count() > 0)
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-clock-fill fs-4 me-3"></i>
        <div class="flex-grow-1">
            <strong>Terms Expiring Soon!</strong>
            <p class="mb-0">{{ $expiringUsers->count() }} staff member(s) will have their terms expire within 30 days:</p>
            <ul class="mb-0 mt-2">
                @foreach($expiringUsers as $expiringUser)
                    <li>
                        <strong>{{ $expiringUser->first_name }} {{ $expiringUser->last_name }}</strong> 
                        ({{ ucwords(str_replace('-', ' ', $expiringUser->getRoleNames()->first())) }}) - 
                        Expires {{ $expiringUser->term_end->diffForHumans() }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Staff</h6>
                            <h3 class="mb-0">{{ $stats['total_staff'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Staff</h6>
                            <h3 class="mb-0">{{ $stats['active_staff'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                <i class="bi bi-person-badge-fill fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Councilors</h6>
                            <h3 class="mb-0">{{ $stats['councilors'] }}/7</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                <i class="bi bi-shield-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Lupon Members</h6>
                            <h3 class="mb-0">{{ $stats['lupon_members'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('barangay.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Staff List</h5>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Term</th>
                            <th>Term Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        @if($user->hasRole('barangay-councilor') && $user->councilor_number)
                                            <small class="text-muted">Councilor #{{ $user->councilor_number }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ ucwords(str_replace('-', ' ', $user->getRoleNames()->first())) }}
                                </span>
                            </td>
                            <td>
                                @if($user->position_title)
                                    {{ $user->position_title }}
                                @elseif($user->hasRole('barangay-councilor') && $user->committee)
                                    <small class="text-muted">{{ ucwords(str_replace('_', ' ', $user->committee)) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($user->term_end)
                                    <small>{{ $user->term_end->format('M Y') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->term_end)
                                    @php
                                        $daysUntilExpiration = now()->diffInDays($user->term_end, false);
                                    @endphp
                                    
                                    @if($daysUntilExpiration < 0)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Expired
                                        </span>
                                    @elseif($daysUntilExpiration <= 7)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle"></i> {{ $daysUntilExpiration }}d left
                                        </span>
                                    @elseif($daysUntilExpiration <= 30)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> {{ $daysUntilExpiration }}d left
                                        </span>
                                    @elseif($daysUntilExpiration <= 90)
                                        <span class="badge bg-info">
                                            <i class="bi bi-info-circle"></i> {{ round($daysUntilExpiration / 30) }}mo left
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('barangay.users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('edit-users')
                                    <a href="{{ route('barangay.users.edit', $user) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="dropdown" title="More">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form method="POST" action="{{ route('barangay.users.toggle-status', $user) }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    @if($user->is_active)
                                                        <i class="bi bi-x-circle me-2"></i> Deactivate
                                                    @else
                                                        <i class="bi bi-check-circle me-2"></i> Activate
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                        @hasRole('barangay-captain')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('barangay.users.destroy', $user) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                        @endhasRole
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $users->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No staff members found.</p>
                @can('create-users')
                <a href="{{ route('barangay.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add First Staff Member
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
@endsection