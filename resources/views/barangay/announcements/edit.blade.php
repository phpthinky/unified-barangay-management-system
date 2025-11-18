@extends('layouts.barangay')

@section('title', 'Edit Announcement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-edit"></i> Edit Announcement</h2>
            <p class="text-muted mb-0">Update announcement details</p>
        </div>
        <a href="{{ route('barangay.announcements.show', $announcement) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('barangay.announcements.update', $announcement) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Announcement Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                    id="priority" name="priority" required>
                                <option value="low" {{ old('priority', $announcement->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('priority', $announcement->priority) === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Publishing Options</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $announcement->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $announcement->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Published At -->
                            <div class="col-md-6 mb-3">
                                <label for="published_at" class="form-label">Publish Date</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                       id="published_at" name="published_at"
                                       value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Expires At -->
                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">Expiration Date</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at"
                                       value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pin_to_top" name="pin_to_top" value="1"
                                   {{ old('pin_to_top', $announcement->pin_to_top) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pin_to_top">
                                <i class="fas fa-thumbtack"></i> Pin to Top
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_on_public" name="show_on_public" value="1"
                                   {{ old('show_on_public', $announcement->show_on_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_on_public">
                                <i class="fas fa-eye"></i> Show on Public Page
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Announcement
                    </button>
                    <a href="{{ route('barangay.announcements.show', $announcement) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong><br>{{ $announcement->created_at->format('M d, Y h:i A') }}</p>
                    <p><strong>Last Updated:</strong><br>{{ $announcement->updated_at->format('M d, Y h:i A') }}</p>
                    <p class="mb-0"><strong>Created By:</strong><br>{{ $announcement->createdBy->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
