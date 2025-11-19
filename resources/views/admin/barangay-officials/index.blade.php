@extends('layouts.admin')

@section('title', 'Barangay Officials Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-users-cog"></i> Barangay Officials Management</h2>
            <p class="text-muted mb-0">Manage organizational charts and official terms</p>
        </div>
        <a href="{{ route('admin.barangay-officials.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Official
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.barangay-officials.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name, position..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="barangay_id" class="form-select">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $barangay)
                        <option value="{{ $barangay->id }}" {{ request('barangay_id') == $barangay->id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="term" class="form-select">
                        <option value="">All Terms</option>
                        <option value="current" {{ request('term') === 'current' ? 'selected' : '' }}>Current Term</option>
                        <option value="past" {{ request('term') === 'past' ? 'selected' : '' }}>Past Terms</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Officials Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Committee</th>
                            <th>Barangay</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($officials as $official)
                        <tr>
                            <td>
                                @if($official->avatar)
                                <img src="{{ asset('storage/' . $official->avatar) }}" alt="{{ $official->name }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                                @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $official->name }}</strong>
                                @if($official->contact_number)
                                <br><small class="text-muted"><i class="fas fa-phone"></i> {{ $official->contact_number }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $official->getPositionBadgeClass() }}">
                                    {{ $official->position }}
                                </span>
                            </td>
                            <td>{{ $official->committee ?? '-' }}</td>
                            <td>{{ $official->barangay->name }}</td>
                            <td>
                                <small>
                                    {{ $official->term_start->format('M d, Y') }}<br>
                                    to {{ $official->term_end->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                @if($official->isCurrentlyServing())
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $official->display_order }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.barangay-officials.edit', $official) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.barangay-officials.destroy', $official) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this official?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No officials found.</p>
                                <a href="{{ route('admin.barangay-officials.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add First Official
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($officials->hasPages())
            <div class="mt-4">
                {{ $officials->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
