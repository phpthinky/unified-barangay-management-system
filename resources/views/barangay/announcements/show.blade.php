@extends('layouts.barangay')

@section('title', $announcement->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-bullhorn"></i> Announcement</h2>
            <p class="text-muted mb-0">View announcement details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('barangay.announcements.edit', $announcement) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('barangay.announcements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Title -->
                    <h3 class="mb-3">
                        @if($announcement->pin_to_top)
                        <i class="fas fa-thumbtack text-primary"></i>
                        @endif
                        {{ $announcement->title }}
                    </h3>

                    <!-- Badges -->
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge bg-{{ $announcement->getStatusBadgeClass() }}">
                            {{ ucfirst($announcement->status) }}
                        </span>
                        <span class="badge bg-{{ $announcement->getPriorityBadgeClass() }}">
                            <i class="fas fa-flag"></i> {{ ucfirst($announcement->priority) }} Priority
                        </span>
                        @if($announcement->show_on_public)
                        <span class="badge bg-info">
                            <i class="fas fa-eye"></i> Public
                        </span>
                        @endif
                        @if($announcement->isExpired())
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle"></i> Expired
                        </span>
                        @endif
                    </div>

                    <!-- Meta Info -->
                    <div class="text-muted mb-4">
                        <i class="fas fa-user"></i> {{ $announcement->createdBy->name }} •
                        <i class="fas fa-calendar"></i> {{ $announcement->created_at->format('F d, Y h:i A') }}
                        @if($announcement->created_at != $announcement->updated_at)
                        • <i class="fas fa-edit"></i> Updated {{ $announcement->updated_at->diffForHumans() }}
                        @endif
                    </div>

                    <hr>

                    <!-- Content -->
                    <div class="announcement-content">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($announcement->status === 'draft')
                        <form action="{{ route('barangay.announcements.publish', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Publish this announcement?')">
                                <i class="fas fa-paper-plane"></i> Publish
                            </button>
                        </form>
                        @endif

                        @if($announcement->status === 'published')
                        <form action="{{ route('barangay.announcements.toggle-pin', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $announcement->pin_to_top ? 'secondary' : 'primary' }}">
                                <i class="fas fa-thumbtack"></i> {{ $announcement->pin_to_top ? 'Unpin' : 'Pin to Top' }}
                            </button>
                        </form>

                        <form action="{{ route('barangay.announcements.archive', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Archive this announcement?')">
                                <i class="fas fa-archive"></i> Archive
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('barangay.announcements.edit', $announcement) }}" class="btn btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <form action="{{ route('barangay.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this announcement? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Details Card -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $announcement->getStatusBadgeClass() }}">
                                    {{ ucfirst($announcement->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Priority:</strong></td>
                            <td>
                                <span class="badge bg-{{ $announcement->getPriorityBadgeClass() }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pinned:</strong></td>
                            <td>
                                @if($announcement->pin_to_top)
                                <i class="fas fa-check text-success"></i> Yes
                                @else
                                <i class="fas fa-times text-danger"></i> No
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Public:</strong></td>
                            <td>
                                @if($announcement->show_on_public)
                                <i class="fas fa-check text-success"></i> Yes
                                @else
                                <i class="fas fa-times text-danger"></i> No
                                @endif
                            </td>
                        </tr>
                        @if($announcement->published_at)
                        <tr>
                            <td><strong>Published:</strong></td>
                            <td>{{ $announcement->published_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endif
                        @if($announcement->expires_at)
                        <tr>
                            <td><strong>Expires:</strong></td>
                            <td>{{ $announcement->expires_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $announcement->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Created By:</strong></td>
                            <td>{{ $announcement->createdBy->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Barangay Info -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Barangay</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $announcement->barangay->name }}</h6>
                    <p class="text-muted small mb-0">
                        This announcement is for {{ $announcement->barangay->name }} residents
                        @if($announcement->show_on_public)
                        and public visitors
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .announcement-content {
        font-size: 16px;
        line-height: 1.6;
    }
</style>
@endpush
@endsection
