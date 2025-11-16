{{-- FILE: resources/views/admin/users/index.blade.php --}}
@extends('layouts.abc')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <div>
        <a href="{{ route('abc.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
        <a href="{{ route('abc.users.export-excel', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-download"></i> Export Excel
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
                        <h4 class="card-title">{{ $stats['total_officials'] }}</h4>
                        <p class="card-text">Total Officials</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
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
                        <h4 class="card-title">{{ $stats['active_officials'] }}</h4>
                        <p class="card-text">Active</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
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
                        <h4 class="card-title">{{ $stats['archived_officials'] }}</h4>
                        <p class="card-text">Archived</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-archive fa-2x"></i>
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
                        <h4 class="card-title">{{ $stats['pending_activation'] }}</h4>
                        <p class="card-text">Pending</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                <input type="text" class="form-control" name="search" placeholder="Search users..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-control">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="barangay_id" class="form-control">
                    <option value="">All Barangays</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" {{ request('barangay_id') == $barangay->id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('abc.users.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th>Last Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-primary text-white rounded-circle">
                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                                        @if($user->position_title)
                                            <br><small class="text-muted">{{ $user->position_title }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->barangay)
                                    <span class="badge bg-info">{{ $user->barangay->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_archived)
                                    <span class="badge bg-dark">Archived</span>
                                @elseif($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('abc.users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('abc.users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(!$user->hasRole('municipality-admin'))
                                        @if($user->is_archived)
                                            <form action="{{ route('abc.users.restore', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Restore this user?')" title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('abc.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Activate' }} this user?')"
                                                        title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('abc.users.archive', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Archive this user? This will end their active terms.')"
                                                        title="Archive">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>No Users Found</h5>
                                <p class="text-muted">No users match your search criteria.</p>
                                <a href="{{ route('abc.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add User
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bulk Archive Modal -->
<div class="modal fade" id="bulkArchiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Archive Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('abc.users.bulk-archive') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        This action will archive selected users and end their active terms.
                    </div>
                    
                    <div class="mb-3">
                        <label for="archive_reason" class="form-label">Archive Reason <span class="text-danger">*</span></label>
                        <select class="form-control" id="archive_reason" name="archive_reason" required>
                            <option value="">Select Reason</option>
                            <option value="term_ended">Term Ended</option>
                            <option value="election_change">Election Change</option>
                            <option value="resignation">Resignation</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="archive_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="archive_notes" name="archive_notes" rows="3" 
                                  placeholder="Optional notes about the archive..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Archive Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
@endpush