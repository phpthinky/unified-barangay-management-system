@extends('layouts.barangay')

@section('title', 'Create Announcement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-bullhorn"></i> Create Announcement</h2>
            <p class="text-muted mb-0">Post a new announcement for your barangay</p>
        </div>
        <a href="{{ route('barangay.announcements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('barangay.announcements.store') }}" method="POST">
                @csrf

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Announcement Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Write the full announcement content here.</small>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror"
                                    id="priority" name="priority" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
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
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Save as Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publish Now</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Published At -->
                            <div class="col-md-6 mb-3">
                                <label for="published_at" class="form-label">Publish Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                       id="published_at" name="published_at" value="{{ old('published_at') }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to publish immediately when status is "Published"</small>
                            </div>

                            <!-- Expires At -->
                            <div class="col-md-6 mb-3">
                                <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Announcement will hide after this date</small>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pin_to_top" name="pin_to_top" value="1" {{ old('pin_to_top') ? 'checked' : '' }}>
                            <label class="form-check-label" for="pin_to_top">
                                <i class="fas fa-thumbtack"></i> Pin to Top
                            </label>
                            <small class="text-muted d-block">Pinned announcements appear first</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="show_on_public" name="show_on_public" value="1" {{ old('show_on_public', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_on_public">
                                <i class="fas fa-eye"></i> Show on Public Page
                            </label>
                            <small class="text-muted d-block">Make visible to website visitors (non-residents)</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Announcement
                    </button>
                    <a href="{{ route('barangay.announcements.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Tips</h5>
                </div>
                <div class="card-body">
                    <h6>Priority Levels:</h6>
                    <ul class="small">
                        <li><strong>Urgent:</strong> Emergency announcements</li>
                        <li><strong>High:</strong> Important updates</li>
                        <li><strong>Normal:</strong> Regular announcements</li>
                        <li><strong>Low:</strong> General information</li>
                    </ul>

                    <hr>

                    <h6>Publishing:</h6>
                    <ul class="small mb-0">
                        <li><strong>Draft:</strong> Save without publishing</li>
                        <li><strong>Published:</strong> Visible to residents and public</li>
                        <li><strong>Pin to Top:</strong> Keep announcement at the top</li>
                        <li><strong>Expiration:</strong> Auto-hide after date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
