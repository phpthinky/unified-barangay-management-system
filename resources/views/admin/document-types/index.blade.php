@extends('layouts.barangay')

@section('title', 'Document Types')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt"></i> Document Types Management</h2>
        <a href="{{ route('barangay.document-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Document Type
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('barangay.document-types.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Document name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Format</label>
                    <select name="format" class="form-select">
                        <option value="">All Formats</option>
                        @foreach($formats as $format)
                            <option value="{{ $format }}" {{ request('format') == $format ? 'selected' : '' }}>
                                @if($format == 'certificate') 📄 Certificate
                                @elseif($format == 'id_card') 🪪 ID Card
                                @elseif($format == 'half_sheet') 📑 Half Sheet
                                @elseif($format == 'legal') 📋 Legal
                                @else ⚙️ Custom
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('barangay.document-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Types Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Name</th>
                            <th width="15%">Category</th>
                            <th width="10%">Format</th>
                            <th width="10%">Fee</th>
                            <th width="10%">Processing</th>
                            <th width="10%">Printing</th>
                            <th width="10%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentTypes as $documentType)
                        <tr>
                            <td>{{ $documentType->sort_order }}</td>
                            <td>
                                <strong>{{ $documentType->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-file"></i> {{ $documentType->document_requests_count }} requests
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($documentType->category) }}</span>
                            </td>
                            <td>
                                @if($documentType->document_format == 'certificate')
                                    <span class="badge bg-primary">📄 Certificate</span>
                                @elseif($documentType->document_format == 'id_card')
                                    <span class="badge bg-warning text-dark">🪪 ID Card</span>
                                @elseif($documentType->document_format == 'half_sheet')
                                    <span class="badge bg-secondary">📑 Half Sheet</span>
                                @elseif($documentType->document_format == 'legal')
                                    <span class="badge bg-dark">📋 Legal</span>
                                @else
                                    <span class="badge bg-secondary">⚙️ Custom</span>
                                @endif
                            </td>
                            <td>₱{{ number_format($documentType->fee, 2) }}</td>
                            <td>{{ $documentType->processing_days }} {{ Str::plural('day', $documentType->processing_days) }}</td>
                            <td>
                                @if($documentType->enable_printing)
                                    <span class="badge bg-success">
                                        <i class="fas fa-print"></i> Enabled
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-ban"></i> Disabled
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($documentType->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('barangay.document-types.show', $documentType) }}" 
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('barangay.document-types.edit', $documentType) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('barangay.document-types.destroy', $documentType) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this document type?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No document types found.</p>
                                <a href="{{ route('barangay.document-types.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Document Type
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($documentTypes->hasPages())
            <div class="mt-4">
                <div class="pagination-info">
                    Showing {{ $documentTypes->firstItem() }} to {{ $documentTypes->lastItem() }} of {{ $documentTypes->total() }} results
                </div>
                <nav aria-label="Document types pagination">
                    {{ $documentTypes->links('pagination::bootstrap-4') }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
/* Clean Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    margin: 0;
}

.pagination .page-item {
    list-style: none;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 500;
    color: #495057;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    color: #0d6efd;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
    opacity: 0.5;
}

/* Clean arrow icons */
.pagination .page-link svg {
    display: none !important;
}

.pagination .page-link[rel="prev"]::after {
    content: "‹ Previous";
    font-size: 14px;
}

.pagination .page-link[rel="next"]::after {
    content: "Next ›";
    font-size: 14px;
}

.pagination-info {
    text-align: center;
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 15px;
    font-weight: 500;
}
</style>
@endpush
@endsection