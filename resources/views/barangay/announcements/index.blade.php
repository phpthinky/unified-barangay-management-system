@extends('layouts.barangay')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-bullhorn"></i> Announcements</h2>
            <p class="text-muted mb-0">Manage barangay announcements and notices</p>
        </div>
        <a href="{{ route('barangay.announcements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Announcement
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
            <form method="GET" action="{{ route('barangay.announcements.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="priority" class="form-select">
                        <option value="">All Priority</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
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

    <!-- Announcements List -->
    <div class="row">
        @forelse($announcements as $announcement)
        <div class="col-12 mb-3">
            <div class="card {{ $announcement->pin_to_top ? 'border-primary' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                @if($announcement->pin_to_top)
                                <i class="fas fa-thumbtack text-primary me-2"></i>
                                @endif
                                <h5 class="mb-0">
                                    <a href="{{ route('barangay.announcements.show', $announcement) }}" class="text-decoration-none">
                                        {{ $announcement->title }}
                                    </a>
                                </h5>
                            </div>

                            <p class="text-muted mb-2">{{ $announcement->getExcerpt() }}</p>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-{{ $announcement->getStatusBadgeClass() }}">
                                    {{ ucfirst($announcement->status) }}
                                </span>
                                <span class="badge bg-{{ $announcement->getPriorityBadgeClass() }}">
                                    <i class="fas fa-flag"></i> {{ ucfirst($announcement->priority) }}
                                </span>
                                @if($announcement->show_on_public)
                                <span class="badge bg-info">
                                    <i class="fas fa-eye"></i> Public
                                </span>
                                @endif
                                @if($announcement->expires_at)
                                <span class="badge bg-secondary">
                                    <i class="fas fa-clock"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
                                </span>
                                @endif
                            </div>

                            <small class="text-muted d-block mt-2">
                                By {{ $announcement->createdBy->name }} • {{ $announcement->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('barangay.announcements.show', $announcement) }}">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('barangay.announcements.edit', $announcement) }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </li>
                                @if($announcement->status === 'draft')
                                <li>
                                    <form action="{{ route('barangay.announcements.publish', $announcement) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-paper-plane"></i> Publish
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if($announcement->status === 'published')
                                <li>
                                    <form action="{{ route('barangay.announcements.toggle-pin', $announcement) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-thumbtack"></i> {{ $announcement->pin_to_top ? 'Unpin' : 'Pin' }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('barangay.announcements.archive', $announcement) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                    </form>
                                </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('barangay.announcements.destroy', $announcement) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No announcements found. Create your first announcement!
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($announcements->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
    @endif
</div>
@endsection
