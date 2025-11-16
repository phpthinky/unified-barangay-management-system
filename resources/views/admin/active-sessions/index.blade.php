@extends('layouts.abc')

@section('title', 'Active Sessions Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2"><i class="fas fa-users"></i> Active Sessions</h1>
            <p class="text-muted">Monitor and manage currently logged-in users</p>
        </div>
        <div>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#clearInactiveModal">
                <i class="fas fa-broom"></i> Clear Inactive
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Active
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['total_active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Residents
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['residents'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Officials
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['officials'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Admins
                            </div>
                            <div class="h5 mb-0 font-weight-bold">{{ $stats['admins'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-danger"></i>
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
            <form method="GET" action="{{ route('abc.active-sessions.index') }}" class="row">
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="resident" {{ request('role') == 'resident' ? 'selected' : '' }}>Resident</option>
                        <option value="barangay-captain" {{ request('role') == 'barangay-captain' ? 'selected' : '' }}>Barangay Captain</option>
                        <option value="barangay-secretary" {{ request('role') == 'barangay-secretary' ? 'selected' : '' }}>Secretary</option>
                        <option value="barangay-staff" {{ request('role') == 'barangay-staff' ? 'selected' : '' }}>Staff</option>
                        <option value="municipality-admin" {{ request('role') == 'municipality-admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="barangay" class="form-control">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy->id }}" {{ request('barangay') == $brgy->id ? 'selected' : '' }}>
                                {{ $brgy->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Sessions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Active Users</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Barangay</th>
                            <th>Last Activity</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeSessions as $user)
                            <tr>
                                <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="status-indicator bg-success"></div>
                                        <div class="ml-2">
                                            <strong>{{ $user->name }}</strong><br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-{{ 
                                            $role->name == 'municipality-admin' ? 'danger' : 
                                            ($role->name == 'abc-president' ? 'warning' : 
                                            (str_contains($role->name, 'barangay') ? 'info' : 'primary'))
                                        }}">
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->barangay->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="text-{{ $user->last_activity_at && $user->last_activity_at->diffInMinutes() < 5 ? 'success' : 'warning' }}">
                                        {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('abc.active-sessions.force-logout', $user) }}" class="d-inline" onsubmit="return confirm('Force logout this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-sign-out-alt"></i> Force Logout
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No active sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $activeSessions->links() }}

            @if($activeSessions->count() > 0)
                <div class="mt-3">
                    <button type="button" class="btn btn-danger" onclick="forceLogoutSelected()">
                        <i class="fas fa-sign-out-alt"></i> Force Logout Selected
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Clear Inactive Modal -->
<div class="modal fade" id="clearInactiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('abc.active-sessions.clear-inactive') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Clear Inactive Sessions</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Clear sessions with no activity for:</p>
                    <select name="minutes" class="form-control">
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Clear Inactive</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.border-left-primary { border-left: 0.25rem solid #4e73df!important; }
.border-left-info { border-left: 0.25rem solid #36b9cc!important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e!important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b!important; }
</style>

<script>
// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Force logout selected
function forceLogoutSelected() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one user');
        return;
    }
    
    if (!confirm(`Force logout ${checkboxes.length} selected users?`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("abc.active-sessions.force-logout-multiple") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection