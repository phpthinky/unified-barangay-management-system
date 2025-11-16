@extends('layouts.admin')

@section('title', 'Complaint Types Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Complaint Types Management</h2>
    <a href="{{ route('admin.complaint-types.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Complaint Type
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $complaintTypes->total() }}</h4>
                        <p class="card-text">Total Types</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-list-alt fa-2x"></i>
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
                        <h4 class="card-title">{{ $complaintTypes->where('is_active', true)->count() }}</h4>
                        <p class="card-text">Active Types</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
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
                        <h4 class="card-title">{{ $complaintTypes->where('is_active', false)->count() }}</h4>
                        <p class="card-text">Inactive Types</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-pause-circle fa-2x"></i>
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
                        <h4 class="card-title">{{ $categories->count() }}</h4>
                        <p class="card-text">Categories</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-folder fa-2x"></i>
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
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search complaint types..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Complaint Types Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Default Handler</th>
                        <th>Requires Hearing</th>
                        <th>Est. Resolution</th>
                        <th>Complaints</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaintTypes as $type)
                        <tr>
                            <td>
                                <strong>{{ $type->name }}</strong>
                                @if($type->description)
                                    <br><small class="text-muted">{{ Str::limit($type->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($type->category) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_', ' ', $type->default_handler_type)) }}
                                </span>
                            </td>
                            <td>
                                @if($type->requires_hearing)
                                    <span class="badge bg-warning">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $type->estimated_resolution_days }} days</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $type->complaints_count }}</span>
                            </td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.complaint-types.show', $type) }}" 
                                       class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.complaint-types.edit', $type) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($type->complaints_count == 0)
                                        <form action="{{ route('admin.complaint-types.destroy', $type) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this complaint type?')"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                                <h5>No Complaint Types Found</h5>
                                <p class="text-muted">No complaint types match your search criteria.</p>
                                <a href="{{ route('admin.complaint-types.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Complaint Type
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($complaintTypes->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $complaintTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection